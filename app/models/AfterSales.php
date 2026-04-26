<?php

namespace App\Models;

use Core\Model;

class AfterSales extends Model
{
    public function hasActiveRequest(string $userId, string $orderId): bool
    {
        $statement = $this->db->prepare('
            SELECT id
            FROM after_sales_requests
            WHERE user_id = :user_id
              AND order_id = :order_id
              AND status IN ("pending", "processing")
            LIMIT 1
        ');
        $statement->execute([
            'user_id' => $userId,
            'order_id' => $orderId,
        ]);

        return (bool) $statement->fetch();
    }

    public function createRequest(string $userId, string $orderId, array $data): string
    {
        $requestId = generate_id('AFS');

        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare('
                INSERT INTO after_sales_requests (
                    id, order_id, user_id, request_type, reason, description, status, created_at, updated_at, handled_by
                ) VALUES (
                    :id, :order_id, :user_id, :request_type, :reason, :description, :status, :created_at, :updated_at, NULL
                )
            ');
            $statement->execute([
                'id' => $requestId,
                'order_id' => $orderId,
                'user_id' => $userId,
                'request_type' => $data['request_type'],
                'reason' => $data['reason'],
                'description' => $data['description'] ?: null,
                'status' => 'pending',
                'created_at' => now_sql(),
                'updated_at' => now_sql(),
            ]);

            $orderStatement = $this->db->prepare('
                SELECT order_status
                FROM orders
                WHERE id = :id
                LIMIT 1
            ');
            $orderStatement->execute([
                'id' => $orderId,
            ]);
            $order = $orderStatement->fetch();

            if ($order && ($order['order_status'] ?? '') !== 'after_sales') {
                $updateOrder = $this->db->prepare('
                    UPDATE orders
                    SET order_status = :order_status, updated_at = :updated_at
                    WHERE id = :id
                ');
                $updateOrder->execute([
                    'id' => $orderId,
                    'order_status' => 'after_sales',
                    'updated_at' => now_sql(),
                ]);

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
                    'old_status' => $order['order_status'] ?: null,
                    'new_status' => 'after_sales',
                    'changed_by' => $userId,
                    'note' => 'Customer submitted after-sales request',
                    'created_at' => now_sql(),
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }

        return $requestId;
    }

    public function getRequestsByUserId(string $userId): array
    {
        $statement = $this->db->prepare('
            SELECT
                afr.*,
                o.order_code,
                o.total_amount,
                r.refund_status,
                r.refund_method,
                r.amount AS refund_amount,
                r.note AS refund_note
            FROM after_sales_requests afr
            INNER JOIN orders o ON o.id = afr.order_id
            LEFT JOIN refunds r ON r.after_sales_request_id = afr.id
            WHERE afr.user_id = :user_id
            ORDER BY afr.created_at DESC, afr.id DESC
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function getAllRequests(): array
    {
        $statement = $this->db->query('
            SELECT
                afr.*,
                o.order_code,
                o.total_amount,
                u.full_name,
                u.email,
                u.phone,
                r.refund_status,
                r.refund_method,
                r.amount AS refund_amount,
                r.note AS refund_note
            FROM after_sales_requests afr
            INNER JOIN orders o ON o.id = afr.order_id
            INNER JOIN users u ON u.id = afr.user_id
            LEFT JOIN refunds r ON r.after_sales_request_id = afr.id
            ORDER BY afr.created_at DESC, afr.id DESC
        ');

        return $statement->fetchAll();
    }

    public function updateRequest(string $requestId, string $handledBy, array $data): void
    {
        $statement = $this->db->prepare('
            UPDATE after_sales_requests
            SET status = :status, updated_at = :updated_at, handled_by = :handled_by
            WHERE id = :id
        ');
        $statement->execute([
            'id' => $requestId,
            'status' => $data['status'],
            'updated_at' => now_sql(),
            'handled_by' => $handledBy,
        ]);

        if (!empty($data['refund_amount']) && (float) $data['refund_amount'] > 0) {
            $refundCheck = $this->db->prepare('
                SELECT id, order_id
                FROM refunds
                WHERE after_sales_request_id = :request_id
                LIMIT 1
            ');
            $refundCheck->execute([
                'request_id' => $requestId,
            ]);
            $refund = $refundCheck->fetch();

            if ($refund) {
                $updateRefund = $this->db->prepare('
                    UPDATE refunds
                    SET
                        amount = :amount,
                        refund_method = :refund_method,
                        refund_status = :refund_status,
                        processed_by = :processed_by,
                        processed_at = :processed_at,
                        note = :note
                    WHERE id = :id
                ');
                $updateRefund->execute([
                    'id' => $refund['id'],
                    'amount' => $data['refund_amount'],
                    'refund_method' => $data['refund_method'] ?: 'bank_transfer',
                    'refund_status' => $data['refund_status'] ?: 'processed',
                    'processed_by' => $handledBy,
                    'processed_at' => now_sql(),
                    'note' => $data['note'] ?: null,
                ]);
            } else {
                $requestStatement = $this->db->prepare('
                    SELECT order_id
                    FROM after_sales_requests
                    WHERE id = :id
                    LIMIT 1
                ');
                $requestStatement->execute([
                    'id' => $requestId,
                ]);
                $request = $requestStatement->fetch();

                if ($request) {
                    $insertRefund = $this->db->prepare('
                        INSERT INTO refunds (
                            id, after_sales_request_id, order_id, amount, refund_method,
                            refund_status, processed_by, processed_at, note
                        ) VALUES (
                            :id, :after_sales_request_id, :order_id, :amount, :refund_method,
                            :refund_status, :processed_by, :processed_at, :note
                        )
                    ');
                    $insertRefund->execute([
                        'id' => generate_id('RFD'),
                        'after_sales_request_id' => $requestId,
                        'order_id' => $request['order_id'],
                        'amount' => $data['refund_amount'],
                        'refund_method' => $data['refund_method'] ?: 'bank_transfer',
                        'refund_status' => $data['refund_status'] ?: 'processed',
                        'processed_by' => $handledBy,
                        'processed_at' => now_sql(),
                        'note' => $data['note'] ?: null,
                    ]);
                }
            }
        }
    }
}