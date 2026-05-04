<?php

namespace App\Models;

use Core\Model;
use RuntimeException;

class Order extends Model
{
    public function placeOrder(string $userId, string $addressId, array $data): string
    {
        $cartModel = new Cart();
        $cartSummary = $cartModel->getSummary($userId);
        $items = $cartSummary['items'];

        if ($items === []) {
            throw new RuntimeException('Giỏ hàng của bạn đang trống.');
        }

        $productTypes = array_values(array_unique(array_filter(array_map(
            static fn (array $item): string => (string) ($item['product_type'] ?? ''),
            $items
        ))));

        if (count($productTypes) > 1) {
            throw new RuntimeException('Mỗi đơn hàng hiện chỉ hỗ trợ một loại sản phẩm. Vui lòng tách giỏ hàng theo từng loại đơn.');
        }

        $voucher = null;
        $discountAmount = 0.0;
        if (!empty($data['voucher_code'])) {
            $voucher = $this->findActiveVoucherByCode($data['voucher_code']);
            if (!$voucher) {
                throw new RuntimeException('Mã voucher không hợp lệ hoặc đã hết hạn.');
            }

            $discountAmount = $this->calculateDiscount((float) $cartSummary['subtotal'], $voucher);
            if ($discountAmount <= 0) {
                throw new RuntimeException('Đơn hàng hiện chưa đủ điều kiện áp dụng voucher này.');
            }
        }

        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
        $paymentMethod = $data['payment_method'] ?: 'cod';
        $orderType = $productTypes[0] ?? ($data['order_type'] ?: 'ready_stock');
        $hasPrescriptionAttachment = !empty($data['prescription_image']);
        $orderStatus = match ($orderType) {
            'pre_order' => 'pre_order_pending',
            'prescription' => 'prescription_review',
            default => 'pending_confirmation',
        };

        if ($orderType === 'prescription' && !$this->hasPrescriptionData($data)) {
            throw new RuntimeException('Đơn cắt kính theo toa cần ít nhất một thông số mắt trước khi đặt hàng.');
        }

        $totalAmount = max(0, (float) $cartSummary['subtotal'] - $discountAmount + $shippingFee);

        $this->db->beginTransaction();

        try {
            $orderId = generate_id('ORD');
            $statement = $this->db->prepare('
                INSERT INTO orders (
                    id, order_code, user_id, address_id, order_type, voucher_id, subtotal,
                    discount_amount, shipping_fee, total_amount, payment_method, payment_status,
                    order_status, note, created_at, updated_at
                ) VALUES (
                    :id, :order_code, :user_id, :address_id, :order_type, :voucher_id, :subtotal,
                    :discount_amount, :shipping_fee, :total_amount, :payment_method, :payment_status,
                    :order_status, :note, :created_at, :updated_at
                )
            ');
            $statement->execute([
                'id' => $orderId,
                'order_code' => generate_order_code(),
                'user_id' => $userId,
                'address_id' => $addressId,
                'order_type' => $orderType,
                'voucher_id' => $voucher['id'] ?? null,
                'subtotal' => $cartSummary['subtotal'],
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'paid',
                'order_status' => $orderStatus,
                'note' => $data['note'] ?: null,
                'created_at' => now_sql(),
                'updated_at' => now_sql(),
            ]);

            foreach ($items as $item) {
                $itemStatement = $this->db->prepare('
                    INSERT INTO order_items (
                        id, order_id, product_variant_id, combo_id, item_type,
                        item_name_snapshot, sku_snapshot, quantity, unit_price, subtotal
                    ) VALUES (
                        :id, :order_id, :product_variant_id, NULL, "variant",
                        :item_name_snapshot, :sku_snapshot, :quantity, :unit_price, :subtotal
                    )
                ');
                $itemStatement->execute([
                    'id' => generate_id('OIT'),
                    'order_id' => $orderId,
                    'product_variant_id' => $item['variant_id'],
                    'item_name_snapshot' => $item['product_name'],
                    'sku_snapshot' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['line_total'],
                ]);

                if ($orderType !== 'pre_order') {
                    if ((int) $item['stock_quantity'] < (int) $item['quantity']) {
                        throw new RuntimeException('Một trong các sản phẩm trong giỏ đã vượt quá tồn kho hiện tại.');
                    }

                    $stockStatement = $this->db->prepare('
                        UPDATE product_variants
                        SET stock_quantity = stock_quantity - :quantity
                        WHERE id = :id
                    ');
                    $stockStatement->execute([
                        'id' => $item['variant_id'],
                        'quantity' => $item['quantity'],
                    ]);

                    $this->insertInventoryTransaction(
                        $item['variant_id'],
                        'order_out',
                        (int) $item['quantity'],
                        $userId,
                        'Stock deducted for order ' . $orderId
                    );
                }
            }

            $paymentStatement = $this->db->prepare('
                INSERT INTO payments (
                    id, order_id, payment_method, amount, transaction_code, payment_status, paid_at, created_at
                ) VALUES (
                    :id, :order_id, :payment_method, :amount, :transaction_code, :payment_status, :paid_at, :created_at
                )
            ');
            $paymentStatement->execute([
                'id' => generate_id('PAY'),
                'order_id' => $orderId,
                'payment_method' => $paymentMethod,
                'amount' => $totalAmount,
                'transaction_code' => null,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'paid',
                'paid_at' => $paymentMethod === 'cod' ? null : now_sql(),
                'created_at' => now_sql(),
            ]);

            $this->insertStatusHistory($orderId, null, $orderStatus, $userId, 'Order created');

            if ($orderType === 'pre_order') {
                $preOrderStatement = $this->db->prepare('
                    INSERT INTO pre_orders (
                        id, order_id, expected_arrival_date, supplier_note, received_at, status
                    ) VALUES (
                        :id, :order_id, :expected_arrival_date, :supplier_note, NULL, :status
                    )
                ');
                $preOrderStatement->execute([
                    'id' => generate_id('PRE'),
                    'order_id' => $orderId,
                    'expected_arrival_date' => $this->normalizeDateTimeValue($data['expected_arrival_date'] ?: null),
                    'supplier_note' => $data['note'] ?: 'Đơn pre-order mới',
                    'status' => 'awaiting_stock',
                ]);
            }

            if ($orderType === 'prescription' || $hasPrescriptionAttachment) {
                $prescriptionId = generate_id('PRS');
                $prescriptionStatement = $this->db->prepare('
                    INSERT INTO prescriptions (
                        id, order_id, user_id, sphere_left, sphere_right, cylinder_left, cylinder_right,
                        axis_left, axis_right, pd, add_power, prescription_image, note, status, verified_by, verified_at
                    ) VALUES (
                        :id, :order_id, :user_id, :sphere_left, :sphere_right, :cylinder_left, :cylinder_right,
                        :axis_left, :axis_right, :pd, :add_power, :prescription_image, :note, :status, NULL, NULL
                    )
                ');
                $prescriptionStatement->execute([
                    'id' => $prescriptionId,
                    'order_id' => $orderId,
                    'user_id' => $userId,
                    'sphere_left' => $data['sphere_left'] ?: null,
                    'sphere_right' => $data['sphere_right'] ?: null,
                    'cylinder_left' => $data['cylinder_left'] ?: null,
                    'cylinder_right' => $data['cylinder_right'] ?: null,
                    'axis_left' => $data['axis_left'] ?: null,
                    'axis_right' => $data['axis_right'] ?: null,
                    'pd' => $data['pd'] ?: null,
                    'add_power' => $data['add_power'] ?: null,
                    'prescription_image' => $data['prescription_image'] ?: null,
                    'note' => $data['note'] ?: null,
                    'status' => 'pending_review',
                ]);

                $this->insertPrescriptionWorkflow(
                    $prescriptionId,
                    'review_request',
                    'pending_review',
                    $userId,
                    $orderType === 'prescription'
                        ? 'Prescription order created and waiting for verification.'
                        : 'Prescription image attached and waiting for verification.'
                );
            }

            if ($voucher) {
                $voucherUpdate = $this->db->prepare('
                    UPDATE vouchers
                    SET usage_limit = CASE
                        WHEN usage_limit IS NULL THEN NULL
                        WHEN usage_limit > 0 THEN usage_limit - 1
                        ELSE usage_limit
                    END
                    WHERE id = :id
                ');
                $voucherUpdate->execute([
                    'id' => $voucher['id'],
                ]);
            }

            $cartModel->clearByUserId($userId);
            $this->db->commit();

            return $orderId;
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function getOrdersByUserId(string $userId): array
    {
        $statement = $this->db->prepare('
            SELECT
                o.*,
                (
                    SELECT COUNT(*)
                    FROM order_items oi
                    WHERE oi.order_id = o.id
                ) AS items_count,
                (
                    SELECT oi.item_name_snapshot
                    FROM order_items oi
                    WHERE oi.order_id = o.id
                    ORDER BY oi.id
                    LIMIT 1
                ) AS first_item_name
            FROM orders o
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC, o.id DESC
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function getOrderById(string $orderId): ?array
    {
        $statement = $this->db->prepare('
            SELECT
                o.*,
                u.full_name,
                u.email,
                u.phone,
                a.receiver_name,
                a.receiver_phone,
                a.province,
                a.district,
                a.ward,
                a.address_line
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            INNER JOIN addresses a ON a.id = o.address_id
            WHERE o.id = :id
            LIMIT 1
        ');
        $statement->execute([
            'id' => $orderId,
        ]);

        $order = $statement->fetch();
        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItemsByOrderId($orderId);
        $order['history'] = $this->getStatusHistory($orderId);
        $order['shipment'] = $this->getShipmentByOrderId($orderId);
        $order['pre_order_detail'] = $this->getPreOrderByOrderId($orderId);
        $order['prescription_detail'] = $this->getPrescriptionByOrderId($orderId);
        $order['prescription_workflows'] = $this->getPrescriptionWorkflowsByOrderId($orderId);

        return $order;
    }

    public function getOrderByIdForUser(string $orderId, string $userId): ?array
    {
        $order = $this->getOrderById($orderId);
        return $order && $order['user_id'] === $userId ? $order : null;
    }

    public function getAllOrders(): array
    {
        $statement = $this->db->query('
            SELECT
                o.*,
                u.full_name,
                u.phone,
                (
                    SELECT COUNT(*)
                    FROM order_items oi
                    WHERE oi.order_id = o.id
                ) AS items_count,
                s.carrier,
                s.tracking_code,
                s.shipping_status,
                s.shipped_at,
                s.delivered_at,
                po.expected_arrival_date,
                po.received_at,
                po.status AS pre_order_status,
                po.supplier_note,
                p.status AS prescription_status,
                p.verified_at,
                p.pd
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            LEFT JOIN shipments s ON s.order_id = o.id
            LEFT JOIN pre_orders po ON po.order_id = o.id
            LEFT JOIN prescriptions p ON p.order_id = o.id
            ORDER BY o.created_at DESC, o.id DESC
        ');

        return $statement->fetchAll();
    }

    public function previewVoucher(string $voucherCode, float $subtotal): array
    {
        $voucherCode = trim($voucherCode);
        if ($voucherCode === '') {
            throw new RuntimeException('Vui lòng nhập mã voucher.');
        }

        $voucher = $this->findActiveVoucherByCode($voucherCode);
        if (!$voucher) {
            throw new RuntimeException('Mã voucher không hợp lệ hoặc đã hết hạn.');
        }

        $discountAmount = $this->calculateDiscount($subtotal, $voucher);
        if ($discountAmount <= 0) {
            throw new RuntimeException('Đơn hàng hiện chưa đủ điều kiện áp dụng voucher này.');
        }

        return [
            'code' => strtoupper($voucherCode),
            'discount_amount' => $discountAmount,
            'total_amount' => max(0, $subtotal - $discountAmount),
        ];
    }

    public function updateOrderStatus(string $orderId, string $newStatus, string $changedBy, string $note = ''): void
    {
        $this->updateAdminOrder($orderId, $changedBy, [
            'order_status' => $newStatus,
            'note' => $note,
        ]);
    }

    public function updateAdminOrder(string $orderId, string $changedBy, array $data): void
    {
        $order = $this->getOrderById($orderId);
        if (!$order) {
            return;
        }

        $oldStatus = (string) ($order['order_status'] ?? '');
        $newStatus = $data['order_status'] ?: $oldStatus;
        $note = trim((string) ($data['note'] ?? ''));
        $paymentStatus = (string) ($order['payment_status'] ?? 'pending');

        if ($newStatus === 'delivered' && ($order['payment_method'] ?? 'cod') === 'cod') {
            $paymentStatus = 'paid';
        } elseif ($newStatus === 'cancelled' && $paymentStatus === 'pending') {
            $paymentStatus = 'cancelled';
        }

        $this->db->beginTransaction();

        try {
            if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled' && ($order['order_type'] ?? '') !== 'pre_order') {
                $this->restoreInventoryForOrder($orderId, $changedBy);
            }

            $update = $this->db->prepare('
                UPDATE orders
                SET order_status = :order_status, payment_status = :payment_status, updated_at = :updated_at
                WHERE id = :id
            ');
            $update->execute([
                'id' => $orderId,
                'order_status' => $newStatus,
                'payment_status' => $paymentStatus,
                'updated_at' => now_sql(),
            ]);

            if ($oldStatus !== $newStatus) {
                $this->insertStatusHistory($orderId, $oldStatus, $newStatus, $changedBy, $note);
            }

            $this->syncPaymentStatus($orderId, $paymentStatus);
            $this->upsertShipment($order, $data);
            $this->upsertPreOrder($order, $data);
            $this->upsertPrescription($order, $changedBy, $data);

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function getDashboardStats(): array
    {
        $stats = $this->db->query('
            SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue,
                SUM(CASE WHEN order_status IN ("pending_confirmation", "pre_order_pending", "prescription_review", "processing", "shipping", "after_sales") THEN 1 ELSE 0 END) AS processing_orders
            FROM orders
        ')->fetch() ?: [];

        $products = $this->db->query('SELECT COUNT(*) AS total_products FROM products')->fetch() ?: [];
        $users = $this->db->query('SELECT COUNT(*) AS total_users FROM users')->fetch() ?: [];

        return [
            'total_orders' => (int) ($stats['total_orders'] ?? 0),
            'total_revenue' => (float) ($stats['total_revenue'] ?? 0),
            'processing_orders' => (int) ($stats['processing_orders'] ?? 0),
            'total_products' => (int) ($products['total_products'] ?? 0),
            'total_users' => (int) ($users['total_users'] ?? 0),
        ];
    }

    public function getRevenueReport(): array
    {
        $statement = $this->db->query('
            SELECT
                DATE(created_at) AS order_date,
                COUNT(*) AS orders_count,
                COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            GROUP BY DATE(created_at)
            ORDER BY order_date DESC
            LIMIT 15
        ');

        return $statement->fetchAll();
    }

    public function getAdminReportPayload(): array
    {
        $currentYear = (int) date('Y');
        $years = [$currentYear, $currentYear - 1];
        $payload = [];

        foreach ($years as $year) {
            $payload[(string) $year] = [
                'summary' => [
                    'daily' => format_currency($this->sumRevenueForPeriod((string) $year, date('m'), date('d'))),
                    'monthly' => format_currency($this->sumRevenueForPeriod((string) $year, date('m'))),
                    'yearly' => format_currency($this->sumRevenueForPeriod((string) $year)),
                ],
                'chart' => [
                    'daily' => $this->getDailyRevenueSeries($year),
                    'monthly' => $this->getMonthlyRevenueSeries($year),
                    'yearly' => $this->getYearlyRevenueSeries($year),
                ],
                'topProducts' => $this->getTopSellingProductsByYear($year),
            ];
        }

        return $payload;
    }

    protected function sumRevenueForPeriod(string $year, ?string $month = null, ?string $day = null): float
    {
        $conditions = ['YEAR(created_at) = :year'];
        $params = ['year' => $year];

        if ($month !== null) {
            $conditions[] = 'MONTH(created_at) = :month';
            $params['month'] = $month;
        }

        if ($day !== null) {
            $conditions[] = 'DAY(created_at) = :day';
            $params['day'] = $day;
        }

        $statement = $this->db->prepare('
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
            WHERE ' . implode(' AND ', $conditions) . '
        ');
        $statement->execute($params);

        return (float) $statement->fetchColumn();
    }

    protected function getDailyRevenueSeries(int $year): array
    {
        $month = (int) date('m');
        $statement = $this->db->prepare('
            SELECT DAY(created_at) AS day_number, COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month
            GROUP BY DAY(created_at)
            ORDER BY day_number ASC
        ');
        $statement->execute([
            'year' => $year,
            'month' => $month,
        ]);

        $rows = $statement->fetchAll();

        return [
            'labels' => array_map(static fn(array $row): string => str_pad((string) $row['day_number'], 2, '0', STR_PAD_LEFT) . '/' . str_pad((string) $month, 2, '0', STR_PAD_LEFT), $rows),
            'values' => array_map(static fn(array $row): float => round(((float) $row['revenue']) / 1000000, 2), $rows),
        ];
    }

    protected function getMonthlyRevenueSeries(int $year): array
    {
        $statement = $this->db->prepare('
            SELECT MONTH(created_at) AS month_number, COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            WHERE YEAR(created_at) = :year
            GROUP BY MONTH(created_at)
            ORDER BY month_number ASC
        ');
        $statement->execute(['year' => $year]);
        $rowsByMonth = [];

        foreach ($statement->fetchAll() as $row) {
            $rowsByMonth[(int) $row['month_number']] = (float) $row['revenue'];
        }

        $labels = [];
        $values = [];

        for ($month = 1; $month <= 12; $month += 1) {
            $labels[] = 'T' . $month;
            $values[] = round(($rowsByMonth[$month] ?? 0) / 1000000, 2);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function getYearlyRevenueSeries(int $selectedYear): array
    {
        $startYear = $selectedYear - 4;
        $statement = $this->db->prepare('
            SELECT YEAR(created_at) AS year_number, COALESCE(SUM(total_amount), 0) AS revenue
            FROM orders
            WHERE YEAR(created_at) BETWEEN :start_year AND :selected_year
            GROUP BY YEAR(created_at)
            ORDER BY year_number ASC
        ');
        $statement->execute([
            'start_year' => $startYear,
            'selected_year' => $selectedYear,
        ]);
        $rowsByYear = [];

        foreach ($statement->fetchAll() as $row) {
            $rowsByYear[(int) $row['year_number']] = (float) $row['revenue'];
        }

        $labels = [];
        $values = [];

        for ($year = $startYear; $year <= $selectedYear; $year += 1) {
            $labels[] = (string) $year;
            $values[] = round(($rowsByYear[$year] ?? 0) / 1000000, 2);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function getTopSellingProductsByYear(int $year): array
    {
        $statement = $this->db->prepare('
            SELECT
                oi.item_name_snapshot AS name,
                COALESCE(NULLIF(oi.sku_snapshot, ""), "N/A") AS sku,
                SUM(oi.quantity) AS sold,
                COALESCE(SUM(oi.subtotal), 0) AS revenue
            FROM order_items oi
            INNER JOIN orders o ON o.id = oi.order_id
            WHERE YEAR(o.created_at) = :year
            GROUP BY oi.item_name_snapshot, oi.sku_snapshot
            ORDER BY sold DESC, revenue DESC
            LIMIT 5
        ');
        $statement->execute(['year' => $year]);

        return array_map(static function (array $row): array {
            return [
                'name' => (string) ($row['name'] ?? 'Sản phẩm'),
                'sku' => (string) ($row['sku'] ?? 'N/A'),
                'sold' => (int) ($row['sold'] ?? 0),
                'revenue' => format_currency($row['revenue'] ?? 0),
            ];
        }, $statement->fetchAll());
    }

    protected function getItemsByOrderId(string $orderId): array
    {
        $statement = $this->db->prepare('
            SELECT
                oi.*,
                pv.color,
                pv.size,
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = pv.product_id OR pi.variant_id = pv.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_url
            FROM order_items oi
            LEFT JOIN product_variants pv ON pv.id = oi.product_variant_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetchAll();
    }

    protected function getStatusHistory(string $orderId): array
    {
        $statement = $this->db->prepare('
            SELECT osh.*, u.full_name AS changed_by_name
            FROM order_status_histories osh
            LEFT JOIN users u ON u.id = osh.changed_by
            WHERE osh.order_id = :order_id
            ORDER BY osh.created_at DESC, osh.id DESC
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetchAll();
    }

    protected function getShipmentByOrderId(string $orderId): ?array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM shipments
            WHERE order_id = :order_id
            LIMIT 1
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetch() ?: null;
    }

    protected function getPreOrderByOrderId(string $orderId): ?array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM pre_orders
            WHERE order_id = :order_id
            LIMIT 1
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetch() ?: null;
    }

    protected function getPrescriptionByOrderId(string $orderId): ?array
    {
        $statement = $this->db->prepare('
            SELECT p.*, u.full_name AS verified_by_name
            FROM prescriptions p
            LEFT JOIN users u ON u.id = p.verified_by
            WHERE p.order_id = :order_id
            LIMIT 1
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetch() ?: null;
    }

    protected function getPrescriptionWorkflowsByOrderId(string $orderId): array
    {
        $statement = $this->db->prepare('
            SELECT pw.*, u.full_name AS handled_by_name
            FROM prescription_workflows pw
            INNER JOIN prescriptions p ON p.id = pw.prescription_id
            LEFT JOIN users u ON u.id = pw.handled_by
            WHERE p.order_id = :order_id
            ORDER BY pw.updated_at DESC, pw.id DESC
        ');
        $statement->execute([
            'order_id' => $orderId,
        ]);

        return $statement->fetchAll();
    }

    protected function findActiveVoucherByCode(string $code): ?array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM vouchers
            WHERE code = :code
              AND (is_active = 1 OR is_active IS NULL)
              AND (start_at IS NULL OR start_at <= NOW())
              AND (expired_at IS NULL OR expired_at >= NOW())
              AND (usage_limit IS NULL OR usage_limit > 0)
            LIMIT 1
        ');
        $statement->execute([
            'code' => strtoupper($code),
        ]);

        return $statement->fetch() ?: null;
    }

    protected function calculateDiscount(float $subtotal, array $voucher): float
    {
        if (!empty($voucher['min_order_value']) && $subtotal < (float) $voucher['min_order_value']) {
            return 0.0;
        }

        $discount = (float) $voucher['discount_value'];
        if (($voucher['discount_type'] ?? '') === 'percent') {
            $discount = $subtotal * ($discount / 100);
        }

        if (!empty($voucher['max_discount_value'])) {
            $discount = min($discount, (float) $voucher['max_discount_value']);
        }

        return max(0, $discount);
    }

    protected function hasPrescriptionData(array $data): bool
    {
        foreach ([
            'sphere_left',
            'sphere_right',
            'cylinder_left',
            'cylinder_right',
            'axis_left',
            'axis_right',
            'pd',
            'add_power',
            'prescription_image',
        ] as $field) {
            if (!empty($data[$field])) {
                return true;
            }
        }

        return false;
    }

    protected function insertStatusHistory(
        string $orderId,
        ?string $oldStatus,
        string $newStatus,
        ?string $changedBy,
        ?string $note = null
    ): void {
        $history = $this->db->prepare('
            INSERT INTO order_status_histories (
                id, order_id, old_status, new_status, changed_by, note, created_at
            ) VALUES (
                :id, :order_id, :old_status, :new_status, :changed_by, :note, :created_at
            )
        ');
        $history->execute([
            'id' => generate_id('OSH'),
            'order_id' => $orderId,
            'old_status' => $oldStatus ?: null,
            'new_status' => $newStatus,
            'changed_by' => $changedBy ?: null,
            'note' => $note ?: null,
            'created_at' => now_sql(),
        ]);
    }

    protected function syncPaymentStatus(string $orderId, string $paymentStatus): void
    {
        $statement = $this->db->prepare('
            UPDATE payments
            SET payment_status = :payment_status, paid_at = :paid_at
            WHERE order_id = :order_id
        ');
        $statement->execute([
            'order_id' => $orderId,
            'payment_status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'paid' ? now_sql() : null,
        ]);
    }

    protected function upsertShipment(array $order, array $data): void
    {
        $needsShipmentRecord = !empty($data['carrier'])
            || !empty($data['tracking_code'])
            || !empty($data['shipping_status'])
            || !empty($data['shipped_at'])
            || !empty($data['delivered_at'])
            || !empty($data['shipment_note'])
            || in_array($data['order_status'] ?? '', ['shipping', 'delivered'], true);

        if (!$needsShipmentRecord) {
            return;
        }

        $shipment = $this->getShipmentByOrderId($order['id']);
        $shippingStatus = $data['shipping_status']
            ?: ($shipment['shipping_status'] ?? $this->mapOrderStatusToShippingStatus($data['order_status'] ?? $order['order_status']));
        $shippedAt = $this->normalizeDateTimeValue(
            $data['shipped_at'] ?: ($shipment['shipped_at'] ?? (($data['order_status'] ?? '') === 'shipping' ? now_sql() : null))
        );
        $deliveredAt = $this->normalizeDateTimeValue(
            $data['delivered_at'] ?: ($shipment['delivered_at'] ?? (($data['order_status'] ?? '') === 'delivered' ? now_sql() : null))
        );

        if ($shipment) {
            $statement = $this->db->prepare('
                UPDATE shipments
                SET
                    carrier = :carrier,
                    tracking_code = :tracking_code,
                    shipping_status = :shipping_status,
                    shipped_at = :shipped_at,
                    delivered_at = :delivered_at,
                    note = :note
                WHERE id = :id
            ');
            $statement->execute([
                'id' => $shipment['id'],
                'carrier' => $data['carrier'] ?: ($shipment['carrier'] ?? null),
                'tracking_code' => $data['tracking_code'] ?: ($shipment['tracking_code'] ?? null),
                'shipping_status' => $shippingStatus ?: null,
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
                'note' => $data['shipment_note'] ?: ($shipment['note'] ?? null),
            ]);

            return;
        }

        $statement = $this->db->prepare('
            INSERT INTO shipments (
                id, order_id, carrier, tracking_code, shipping_status, shipped_at, delivered_at, note
            ) VALUES (
                :id, :order_id, :carrier, :tracking_code, :shipping_status, :shipped_at, :delivered_at, :note
            )
        ');
        $statement->execute([
            'id' => generate_id('SHP'),
            'order_id' => $order['id'],
            'carrier' => $data['carrier'] ?: null,
            'tracking_code' => $data['tracking_code'] ?: null,
            'shipping_status' => $shippingStatus ?: null,
            'shipped_at' => $shippedAt,
            'delivered_at' => $deliveredAt,
            'note' => $data['shipment_note'] ?: null,
        ]);
    }

    protected function upsertPreOrder(array $order, array $data): void
    {
        if (($order['order_type'] ?? '') !== 'pre_order') {
            return;
        }

        $preOrder = $this->getPreOrderByOrderId($order['id']);
        $status = $data['pre_order_status']
            ?: ($preOrder['status'] ?? $this->mapOrderStatusToPreOrderStatus($data['order_status'] ?? $order['order_status']));

        if ($preOrder) {
            $statement = $this->db->prepare('
                UPDATE pre_orders
                SET
                    expected_arrival_date = :expected_arrival_date,
                    supplier_note = :supplier_note,
                    received_at = :received_at,
                    status = :status
                WHERE id = :id
            ');
            $statement->execute([
                'id' => $preOrder['id'],
                'expected_arrival_date' => $this->normalizeDateTimeValue($data['expected_arrival_date'] ?: ($preOrder['expected_arrival_date'] ?? null)),
                'supplier_note' => $data['supplier_note'] ?: ($preOrder['supplier_note'] ?? null),
                'received_at' => $this->normalizeDateTimeValue($data['received_at'] ?: ($preOrder['received_at'] ?? null)),
                'status' => $status ?: null,
            ]);

            return;
        }

        $statement = $this->db->prepare('
            INSERT INTO pre_orders (
                id, order_id, expected_arrival_date, supplier_note, received_at, status
            ) VALUES (
                :id, :order_id, :expected_arrival_date, :supplier_note, :received_at, :status
            )
        ');
        $statement->execute([
            'id' => generate_id('PRE'),
            'order_id' => $order['id'],
            'expected_arrival_date' => $this->normalizeDateTimeValue($data['expected_arrival_date'] ?: null),
            'supplier_note' => $data['supplier_note'] ?: null,
            'received_at' => $this->normalizeDateTimeValue($data['received_at'] ?: null),
            'status' => $status ?: 'awaiting_stock',
        ]);
    }

    protected function upsertPrescription(array $order, string $changedBy, array $data): void
    {
        if (($order['order_type'] ?? '') !== 'prescription') {
            return;
        }

        $prescription = $this->getPrescriptionByOrderId($order['id']);
        if (!$prescription) {
            return;
        }

        $status = $data['prescription_status']
            ?: ($prescription['status'] ?? $this->mapOrderStatusToPrescriptionStatus($data['order_status'] ?? $order['order_status']));
        $verifiedAt = $prescription['verified_at'] ?? null;
        $verifiedBy = $prescription['verified_by'] ?? null;

        if (!in_array($status, ['pending_review', 'needs_adjustment', 'cancelled'], true) && empty($verifiedAt)) {
            $verifiedAt = now_sql();
            $verifiedBy = $changedBy;
        }

        $statement = $this->db->prepare('
            UPDATE prescriptions
            SET
                note = :note,
                status = :status,
                verified_by = :verified_by,
                verified_at = :verified_at
            WHERE id = :id
        ');
        $statement->execute([
            'id' => $prescription['id'],
            'note' => $data['prescription_note'] ?: ($prescription['note'] ?? null),
            'status' => $status ?: null,
            'verified_by' => $verifiedBy,
            'verified_at' => $verifiedAt,
        ]);

        $workflowStep = trim((string) ($data['workflow_step'] ?? ''));
        $workflowNote = trim((string) ($data['workflow_note'] ?? ''));
        $shouldLogWorkflow = $workflowStep !== ''
            || $workflowNote !== ''
            || (($data['order_status'] ?? ($order['order_status'] ?? '')) !== ($order['order_status'] ?? ''));

        if ($shouldLogWorkflow) {
            $this->insertPrescriptionWorkflow(
                $prescription['id'],
                $workflowStep !== '' ? $workflowStep : $this->mapPrescriptionStatusToWorkflowStep($status),
                $status ?: 'pending_review',
                $changedBy,
                $workflowNote !== '' ? $workflowNote : ($data['note'] ?? '')
            );
        }
    }

    protected function insertPrescriptionWorkflow(
        string $prescriptionId,
        string $stepName,
        string $stepStatus,
        ?string $handledBy,
        ?string $note = null
    ): void {
        $statement = $this->db->prepare('
            INSERT INTO prescription_workflows (
                id, prescription_id, step_name, step_status, handled_by, note, updated_at
            ) VALUES (
                :id, :prescription_id, :step_name, :step_status, :handled_by, :note, :updated_at
            )
        ');
        $statement->execute([
            'id' => generate_id('PWF'),
            'prescription_id' => $prescriptionId,
            'step_name' => $stepName,
            'step_status' => $stepStatus,
            'handled_by' => $handledBy ?: null,
            'note' => $note ?: null,
            'updated_at' => now_sql(),
        ]);
    }

    protected function insertInventoryTransaction(
        string $variantId,
        string $transactionType,
        int $quantity,
        ?string $createdBy,
        ?string $note = null
    ): void {
        $statement = $this->db->prepare('
            INSERT INTO inventory_transactions (
                id, product_variant_id, transaction_type, quantity, note, created_by, created_at
            ) VALUES (
                :id, :product_variant_id, :transaction_type, :quantity, :note, :created_by, :created_at
            )
        ');
        $statement->execute([
            'id' => generate_id('IVT'),
            'product_variant_id' => $variantId,
            'transaction_type' => $transactionType,
            'quantity' => $quantity,
            'note' => $note ?: null,
            'created_by' => $createdBy ?: null,
            'created_at' => now_sql(),
        ]);
    }

    protected function restoreInventoryForOrder(string $orderId, string $changedBy): void
    {
        foreach ($this->getItemsByOrderId($orderId) as $item) {
            if (empty($item['product_variant_id'])) {
                continue;
            }

            $statement = $this->db->prepare('
                UPDATE product_variants
                SET stock_quantity = stock_quantity + :quantity
                WHERE id = :id
            ');
            $statement->execute([
                'id' => $item['product_variant_id'],
                'quantity' => (int) $item['quantity'],
            ]);

            $this->insertInventoryTransaction(
                $item['product_variant_id'],
                'order_cancel_restore',
                (int) $item['quantity'],
                $changedBy,
                'Restored stock because order ' . $orderId . ' was cancelled'
            );
        }
    }

    protected function mapOrderStatusToShippingStatus(?string $status): ?string
    {
        return match ($status) {
            'shipping' => 'in_transit',
            'delivered' => 'delivered',
            'processing' => 'preparing',
            'cancelled' => 'cancelled',
            default => null,
        };
    }

    protected function mapOrderStatusToPreOrderStatus(?string $status): string
    {
        return match ($status) {
            'processing' => 'stock_received',
            'shipping' => 'shipping',
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
            default => 'awaiting_stock',
        };
    }

    protected function mapOrderStatusToPrescriptionStatus(?string $status): string
    {
        return match ($status) {
            'processing' => 'verified',
            'shipping' => 'ready_to_ship',
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
            default => 'pending_review',
        };
    }

    protected function mapPrescriptionStatusToWorkflowStep(?string $status): string
    {
        return match ($status) {
            'needs_adjustment' => 'customer_adjustment',
            'verified' => 'verification_complete',
            'lens_cutting' => 'lens_cutting',
            'assembled' => 'assembly',
            'ready_to_ship' => 'handover_to_shipping',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'review_request',
        };
    }

    protected function normalizeDateTimeValue(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return str_replace('T', ' ', $value);
    }
}