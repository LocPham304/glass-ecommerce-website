<?php if (false): ?>
<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title"><h1>Quản lý đơn hàng</h1></div>
</header>

<section class="admin-content admin-content--orders">
  <section class="admin-panel order-table-panel">
    <div class="p-4">
      <?php if ($orders === []): ?>
        <div class="alert alert-light border mb-0">Chưa có đơn hàng nào trong hệ thống.</div>
      <?php else: ?>
        <div class="d-grid gap-4">
          <?php foreach ($orders as $order): ?>
            <?php $status = build_status_badge($order['order_status']); ?>
            <article class="border rounded-4 bg-white p-4 shadow-sm">
              <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                  <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h2 class="h5 mb-0">#<?= e($order['order_code']) ?></h2>
                    <span class="badge <?= e($status['class']) ?>"><?= e($status['label']) ?></span>
                  </div>
                  <p class="mb-1"><strong><?= e($order['full_name']) ?></strong> · <?= e($order['phone']) ?></p>
                  <p class="mb-1 text-secondary small">
                    <?= e(order_type_label($order['order_type'] ?? null)) ?>
                    · <?= e((string) ($order['items_count'] ?? 0)) ?> sản phẩm
                    · Thanh toán: <?= e(strtoupper((string) ($order['payment_method'] ?? 'COD'))) ?>
                  </p>
                  <p class="mb-0 text-secondary small">
                    Tổng tiền: <strong><?= e(format_currency($order['total_amount'])) ?></strong>
                  </p>
                </div>
                <div class="small text-secondary text-lg-end">
                  <div>Ngày tạo: <?= e(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?></div>
                  <div>Thanh toán: <?= e((string) ($order['payment_status'] ?? 'pending')) ?></div>
                </div>
              </div>

              <form class="row g-3" method="POST" action="<?= e(url('/admin/orders/status')) ?>">
                <input type="hidden" name="order_id" value="<?= e($order['id']) ?>" />

                <div class="col-lg-3">
                  <label class="form-label small text-secondary">Trạng thái đơn</label>
                  <select class="form-select" name="order_status">
                    <option value="pending_confirmation" <?= $order['order_status'] === 'pending_confirmation' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="pre_order_pending" <?= $order['order_status'] === 'pre_order_pending' ? 'selected' : '' ?>>Chờ hàng về</option>
                    <option value="prescription_review" <?= $order['order_status'] === 'prescription_review' ? 'selected' : '' ?>>Duyệt toa</option>
                    <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                    <option value="shipping" <?= $order['order_status'] === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                    <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="after_sales" <?= $order['order_status'] === 'after_sales' ? 'selected' : '' ?>>Hậu mãi</option>
                    <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                  </select>
                </div>

                <div class="col-lg-9">
                  <label class="form-label small text-secondary">Ghi chú cập nhật</label>
                  <input class="form-control" type="text" name="note" placeholder="Ví dụ: đã gọi xác nhận với khách hàng" />
                </div>

                <div class="col-12">
                  <div class="row g-3">
                    <div class="col-lg-4">
                      <div class="border rounded-4 p-3 h-100">
                        <h3 class="h6 mb-3">Vận chuyển</h3>
                        <div class="d-grid gap-2">
                          <input class="form-control form-control-sm" type="text" name="carrier" value="<?= e((string) ($order['carrier'] ?? '')) ?>" placeholder="Đơn vị vận chuyển" />
                          <input class="form-control form-control-sm" type="text" name="tracking_code" value="<?= e((string) ($order['tracking_code'] ?? '')) ?>" placeholder="Mã tracking" />
                          <select class="form-select form-select-sm" name="shipping_status">
                            <option value="">Giữ theo trạng thái đơn</option>
                            <option value="preparing" <?= ($order['shipping_status'] ?? '') === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                            <option value="in_transit" <?= ($order['shipping_status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In transit</option>
                            <option value="delivered" <?= ($order['shipping_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= ($order['shipping_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                          </select>
                          <input class="form-control form-control-sm" type="datetime-local" name="shipped_at" value="<?= !empty($order['shipped_at']) ? e(date('Y-m-d\TH:i', strtotime((string) $order['shipped_at']))) : '' ?>" />
                          <input class="form-control form-control-sm" type="datetime-local" name="delivered_at" value="<?= !empty($order['delivered_at']) ? e(date('Y-m-d\TH:i', strtotime((string) $order['delivered_at']))) : '' ?>" />
                          <input class="form-control form-control-sm" type="text" name="shipment_note" placeholder="Ghi chú giao vận" />
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="border rounded-4 p-3 h-100">
                        <h3 class="h6 mb-3">Pre-order</h3>
                        <div class="d-grid gap-2">
                          <input class="form-control form-control-sm" type="datetime-local" name="expected_arrival_date" value="<?= !empty($order['expected_arrival_date']) ? e(date('Y-m-d\TH:i', strtotime((string) $order['expected_arrival_date']))) : '' ?>" <?= ($order['order_type'] ?? '') !== 'pre_order' ? 'disabled' : '' ?> />
                          <select class="form-select form-select-sm" name="pre_order_status" <?= ($order['order_type'] ?? '') !== 'pre_order' ? 'disabled' : '' ?>>
                            <option value="">Giữ trạng thái hiện tại</option>
                            <option value="awaiting_stock" <?= ($order['pre_order_status'] ?? '') === 'awaiting_stock' ? 'selected' : '' ?>>Awaiting stock</option>
                            <option value="stock_received" <?= ($order['pre_order_status'] ?? '') === 'stock_received' ? 'selected' : '' ?>>Stock received</option>
                            <option value="shipping" <?= ($order['pre_order_status'] ?? '') === 'shipping' ? 'selected' : '' ?>>Shipping</option>
                            <option value="completed" <?= ($order['pre_order_status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($order['pre_order_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                          </select>
                          <input class="form-control form-control-sm" type="datetime-local" name="received_at" value="<?= !empty($order['received_at']) ? e(date('Y-m-d\TH:i', strtotime((string) $order['received_at']))) : '' ?>" <?= ($order['order_type'] ?? '') !== 'pre_order' ? 'disabled' : '' ?> />
                          <textarea class="form-control form-control-sm" rows="3" name="supplier_note" placeholder="Ghi chú pre-order" <?= ($order['order_type'] ?? '') !== 'pre_order' ? 'disabled' : '' ?>><?= e((string) ($order['supplier_note'] ?? '')) ?></textarea>
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="border rounded-4 p-3 h-100">
                        <h3 class="h6 mb-3">Toa kính</h3>
                        <div class="d-grid gap-2">
                          <select class="form-select form-select-sm" name="prescription_status" <?= ($order['order_type'] ?? '') !== 'prescription' ? 'disabled' : '' ?>>
                            <option value="">Giữ trạng thái hiện tại</option>
                            <option value="pending_review" <?= ($order['prescription_status'] ?? '') === 'pending_review' ? 'selected' : '' ?>>Pending review</option>
                            <option value="needs_adjustment" <?= ($order['prescription_status'] ?? '') === 'needs_adjustment' ? 'selected' : '' ?>>Needs adjustment</option>
                            <option value="verified" <?= ($order['prescription_status'] ?? '') === 'verified' ? 'selected' : '' ?>>Verified</option>
                            <option value="lens_cutting" <?= ($order['prescription_status'] ?? '') === 'lens_cutting' ? 'selected' : '' ?>>Lens cutting</option>
                            <option value="assembled" <?= ($order['prescription_status'] ?? '') === 'assembled' ? 'selected' : '' ?>>Assembled</option>
                            <option value="ready_to_ship" <?= ($order['prescription_status'] ?? '') === 'ready_to_ship' ? 'selected' : '' ?>>Ready to ship</option>
                            <option value="completed" <?= ($order['prescription_status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($order['prescription_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                          </select>
                          <input class="form-control form-control-sm" type="text" name="workflow_step" placeholder="Ví dụ: lens_cutting" <?= ($order['order_type'] ?? '') !== 'prescription' ? 'disabled' : '' ?> />
                          <input class="form-control form-control-sm" type="text" name="workflow_note" placeholder="Ghi chú workflow" <?= ($order['order_type'] ?? '') !== 'prescription' ? 'disabled' : '' ?> />
                          <textarea class="form-control form-control-sm" rows="3" name="prescription_note" placeholder="Ghi chú toa kính" <?= ($order['order_type'] ?? '') !== 'prescription' ? 'disabled' : '' ?>><?= !empty($order['verified_at']) ? e('Verified at: ' . date('d/m/Y H:i', strtotime((string) $order['verified_at']))) : '' ?></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                  <button class="btn btn-dark" type="submit">Lưu cập nhật</button>
                </div>
              </form>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
</section>
</section>
<?php endif; ?>
<?php
$orderCount = count($orders);
$orderStatuses = [
    'pending_confirmation' => 'Chờ xác nhận',
    'pre_order_pending' => 'Chờ hàng về',
    'prescription_review' => 'Đang duyệt toa',
    'processing' => 'Đang xử lý',
    'shipping' => 'Đang giao',
    'delivered' => 'Hoàn thành',
    'after_sales' => 'Đổi trả / hoàn tiền',
    'cancelled' => 'Đã hủy',
];
$paymentStatuses = [
    'pending' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán',
    'cancelled' => 'Đã hủy thanh toán',
    'failed' => 'Thanh toán lỗi',
    'refunded' => 'Đã hoàn tiền',
];
$resolveStatusMeta = static function (string $status) use ($orderStatuses): array {
    $label = $orderStatuses[$status] ?? 'Đang xử lý';
    $class = match ($status) {
        'delivered' => 'status-pill--active',
        'shipping' => 'status-pill--shipping',
        'cancelled' => 'status-pill--cancelled',
        'pending_confirmation', 'pre_order_pending' => 'status-pill--pending',
        default => 'status-pill--processing',
    };

    return [
        'label' => $label,
        'class' => $class,
    ];
};
$resolvePaymentMeta = static function (?string $paymentMethod): array {
    $paymentMethod = strtolower(trim((string) $paymentMethod));

    return match ($paymentMethod) {
        'bank_transfer', 'banking', 'bank' => [
            'label' => 'Chuyển khoản',
            'class' => 'order-payment-pill--bank',
        ],
        'wallet', 'e_wallet', 'momo', 'zalopay', 'vnpay' => [
            'label' => 'Ví điện tử',
            'class' => 'order-payment-pill--wallet',
        ],
        default => [
            'label' => 'COD',
            'class' => 'order-payment-pill--cod',
        ],
    };
};
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Quản lý đơn hàng</h1>
  </div>

  <label class="admin-products-search" aria-label="Tìm kiếm đơn hàng">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input
      type="search"
      placeholder="Tìm theo mã đơn, khách hàng..."
      data-order-search
    />
  </label>
</header>

<section class="admin-content admin-content--orders">
  <section class="admin-panel order-filter-panel">
    <div class="order-filter-grid">
      <label class="product-filter-field">
        <span>MÃ ĐƠN / KHÁCH HÀNG</span>
        <input
          type="search"
          placeholder="Ví dụ: #ORD12345"
          data-filter-keyword
        />
      </label>

      <label class="product-filter-field">
        <span>TRẠNG THÁI</span>
        <select data-filter-order-status>
          <option value="all">Tất cả trạng thái</option>
          <?php foreach ($orderStatuses as $statusLabel): ?>
            <option value="<?= e($statusLabel) ?>"><?= e($statusLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="product-filter-field">
        <span>THANH TOÁN</span>
        <select data-filter-payment>
          <option value="all">Tất cả phương thức</option>
          <option value="COD">COD</option>
          <option value="Chuyển khoản">Chuyển khoản</option>
          <option value="Ví điện tử">Ví điện tử</option>
        </select>
      </label>

      <label class="product-filter-field">
        <span>THỜI GIAN</span>
        <select data-filter-date>
          <option value="all">Tất cả thời gian</option>
          <option value="today">Hôm nay</option>
          <option value="week">7 ngày qua</option>
          <option value="month">30 ngày qua</option>
        </select>
      </label>
    </div>

    <div class="product-toolbar">

      <p class="product-toolbar__summary" data-order-summary>
        Hiển thị <?= $orderCount > 0 ? '1 - ' . e((string) $orderCount) : '0' ?> trong số <?= e((string) $orderCount) ?> đơn hàng
      </p>
    </div>
  </section>

  <section class="admin-panel order-table-panel">
    <?php if ($orders === []): ?>
      <div class="order-empty-state">
        <i class="fa-solid fa-box-open" aria-hidden="true"></i>
        <h2>Chưa có đơn hàng nào</h2>
        <p>Danh sách đơn hàng sẽ xuất hiện tại đây ngay khi khách hàng bắt đầu mua sắm.</p>
      </div>
    <?php else: ?>
      <div class="order-admin-table" role="table" aria-label="Danh sách đơn hàng">
        <div class="order-admin-table__head" role="row">
          <span>MÃ ĐƠN</span>
          <span>KHÁCH HÀNG</span>
          <span>SẢN PHẨM</span>
          <span>TỔNG TIỀN</span>
          <span>THANH TOÁN</span>
          <span>TRẠNG THÁI</span>
          <span>NGÀY TẠO</span>
          <span>HÀNH ĐỘNG</span>
        </div>

        <div class="order-admin-table__body" data-order-table>
          <?php foreach ($orders as $order): ?>
            <?php
            $statusCode = (string) ($order['order_status'] ?? 'pending_confirmation');
            $statusMeta = $resolveStatusMeta($statusCode);
            $paymentMeta = $resolvePaymentMeta($order['payment_method'] ?? null);
            $itemsCount = max(0, (int) ($order['items_count'] ?? 0));
            $createdAt = !empty($order['created_at']) ? strtotime((string) $order['created_at']) : false;
            $createdAtDisplay = $createdAt ? date('d/m/Y', $createdAt) : '-';
            $orderCode = (string) ($order['order_code'] ?? $order['id'] ?? '');
            $customerName = trim((string) ($order['full_name'] ?? 'Khách lẻ'));
            $customerPhone = trim((string) ($order['phone'] ?? ''));
            $orderTypeLabel = order_type_label($order['order_type'] ?? null);
            $paymentStatusLabel = $paymentStatuses[(string) ($order['payment_status'] ?? 'pending')] ?? 'Chờ thanh toán';
            $itemSummaryParts = [];
            $itemSummaryParts[] = $orderTypeLabel;
            $itemSummaryParts[] = $itemsCount . ' sản phẩm';

            if (!empty($order['tracking_code'])) {
                $itemSummaryParts[] = 'Mã vận đơn: ' . (string) $order['tracking_code'];
            } elseif (!empty($order['expected_arrival_date'])) {
                $arrivalTimestamp = strtotime((string) $order['expected_arrival_date']);
                if ($arrivalTimestamp) {
                    $itemSummaryParts[] = 'Dự kiến: ' . date('d/m/Y', $arrivalTimestamp);
                }
            } elseif (!empty($order['pd'])) {
                $itemSummaryParts[] = 'PD: ' . (string) $order['pd'];
            }

            $itemSummary = implode(' • ', array_filter($itemSummaryParts));
            $searchKeyword = trim(implode(' ', array_filter([
                '#' . $orderCode,
                $customerName,
                $customerPhone,
                $orderTypeLabel,
                $itemSummary,
            ])));
            ?>
            <article
              class="order-admin-row"
              data-order-code="<?= e($orderCode) ?>"
              data-keyword="<?= e($searchKeyword) ?>"
              data-status="<?= e($statusMeta['label']) ?>"
              data-payment="<?= e($paymentMeta['label']) ?>"
              data-created-at="<?= e($createdAt ? (string) $createdAt : '0') ?>"
            >
              <div class="order-admin-code">
                <strong>#<?= e($orderCode) ?></strong>
                <span><?= e($itemsCount . ' sản phẩm') ?></span>
              </div>

              <div class="order-admin-customer">
                <strong><?= e($customerName !== '' ? $customerName : 'Khách lẻ') ?></strong>
                <span><?= e($customerPhone !== '' ? $customerPhone : 'Chưa có số điện thoại') ?></span>
              </div>

              <div class="order-admin-items">
                <strong><?= e($itemSummary) ?></strong>
                <span><?= e($paymentStatusLabel) ?></span>
              </div>

              <div class="order-admin-total"><?= e(format_currency($order['total_amount'] ?? 0)) ?></div>

              <div>
                <span class="order-payment-pill <?= e($paymentMeta['class']) ?>">
                  <?= e($paymentMeta['label']) ?>
                </span>
              </div>

              <div>
                <span class="status-pill <?= e($statusMeta['class']) ?>" data-order-status-badge>
                  <?= e($statusMeta['label']) ?>
                </span>
              </div>

              <div class="order-admin-date"><?= e($createdAtDisplay) ?></div>

              <div class="order-admin-actions">
                <form
                  class="order-status-form"
                  method="POST"
                  action="<?= e(url('/admin/orders/status')) ?>"
                  data-order-status-form
                >
                  <input type="hidden" name="order_id" value="<?= e((string) ($order['id'] ?? '')) ?>" />
                  <select
                    class="order-status-select"
                    name="order_status"
                    data-order-status-select
                    aria-label="Cập nhật trạng thái cho đơn #<?= e($orderCode) ?>"
                  >
                    <?php foreach ($orderStatuses as $statusValue => $statusLabel): ?>
                      <option value="<?= e($statusValue) ?>" <?= $statusCode === $statusValue ? 'selected' : '' ?>>
                        <?= e($statusLabel) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button
                    type="submit"
                    class="order-status-save"
                    data-order-status-save
                    aria-label="Lưu trạng thái đơn #<?= e($orderCode) ?>"
                  >
                    <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="order-admin-footer">
        <div class="promotion-table-footer__note">
        </div>

        <nav class="product-pagination" aria-label="Phân trang">
          <button type="button" aria-label="Trang trước" disabled>
            <i class="fa-solid fa-angle-left" aria-hidden="true"></i>
          </button>
          <button class="is-active" type="button">1</button>
          <button type="button" aria-label="Trang sau" disabled>
            <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
          </button>
        </nav>
      </div>
    <?php endif; ?>
  </section>
</section>

<?php return; ?>
