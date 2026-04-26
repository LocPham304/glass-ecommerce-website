<?php
$status = build_status_badge($order['order_status'] ?? '');
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');
?>

<main class="py-5" style="background: #f8fafc; min-height: 60vh">
  <div class="site-container">
    <div class="rounded-4 border bg-white p-4 p-md-5 shadow-sm">
      <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
          <p class="text-uppercase text-secondary fw-semibold mb-2">Chi tiết đơn hàng</p>
          <h1 class="mb-2" style="font-family: 'Playfair Display', serif">#<?= e($order['order_code']) ?></h1>
          <p class="mb-0 text-secondary">Khách hàng: <?= e($order['receiver_name']) ?> · <?= e($order['receiver_phone']) ?></p>
        </div>
        <span class="badge rounded-pill <?= e($status['class']) ?> align-self-start px-3 py-2"><?= e($status['label']) ?></span>
      </div>

      <div class="row g-4">
        <div class="col-lg-7">
          <div class="border rounded-4 p-4 h-100">
            <h2 class="h5 mb-3">Sản phẩm trong đơn</h2>
            <?php foreach ($order['items'] as $item): ?>
              <div class="d-flex gap-3 align-items-center border-bottom pb-3 mb-3">
                <img src="<?= e(media_url($item['image_url'] ?? null)) ?>" alt="<?= e($item['item_name_snapshot']) ?>" style="width: 88px; height: 88px; object-fit: cover; border-radius: 16px;" onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';" />
                <div>
                  <h3 class="h6 mb-1"><?= e($item['item_name_snapshot']) ?></h3>
                  <p class="mb-1 text-secondary">SKU: <?= e($item['sku_snapshot'] ?: '-') ?><?php if ($item['color']): ?> · <?= e($item['color']) ?><?php endif; ?><?php if ($item['size']): ?> · <?= e($item['size']) ?><?php endif; ?></p>
                  <strong><?= e(format_currency($item['subtotal'])) ?></strong>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="border rounded-4 p-4 h-100">
            <h2 class="h5 mb-3">Tóm tắt đơn hàng</h2>
            <div class="d-flex justify-content-between mb-2"><span>Loại đơn</span><strong><?= e(order_type_label($order['order_type'] ?? null)) ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Thanh toán</span><strong><?= e(strtoupper($order['payment_method'] ?? 'COD')) ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Tạm tính</span><strong><?= e(format_currency($order['subtotal'])) ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Giảm giá</span><strong>-<?= e(format_currency($order['discount_amount'])) ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Phí vận chuyển</span><strong><?= e(format_currency($order['shipping_fee'])) ?></strong></div>
            <div class="d-flex justify-content-between border-top pt-3 mt-3"><span class="fw-semibold">Tổng cộng</span><strong class="text-warning"><?= e(format_currency($order['total_amount'])) ?></strong></div>
            <p class="small text-secondary mt-3 mb-0">Địa chỉ: <?= e(trim(($order['address_line'] ?? '') . ', ' . ($order['ward'] ?? '') . ', ' . ($order['district'] ?? '') . ', ' . ($order['province'] ?? ''), ', ')) ?></p>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <?php if (!empty($order['pre_order_detail'])): ?>
          <div class="col-lg-6">
            <div class="border rounded-4 p-4 h-100">
              <h2 class="h5 mb-3">Thông tin pre-order</h2>
              <p class="mb-2"><strong>Trạng thái:</strong> <?= e($order['pre_order_detail']['status'] ?: 'awaiting_stock') ?></p>
              <p class="mb-2"><strong>Ngày hàng về dự kiến:</strong> <?= e($order['pre_order_detail']['expected_arrival_date'] ?: 'Chưa cập nhật') ?></p>
              <p class="mb-0"><strong>Ghi chú:</strong> <?= e($order['pre_order_detail']['supplier_note'] ?: 'Chưa có ghi chú') ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($order['prescription_detail'])): ?>
          <div class="col-lg-6">
            <div class="border rounded-4 p-4 h-100">
              <h2 class="h5 mb-3">Thông số mắt</h2>
              <div class="row row-cols-2 g-2 small">
                <div><strong>SPH trái:</strong> <?= e($order['prescription_detail']['sphere_left'] ?: '-') ?></div>
                <div><strong>SPH phải:</strong> <?= e($order['prescription_detail']['sphere_right'] ?: '-') ?></div>
                <div><strong>CYL trái:</strong> <?= e($order['prescription_detail']['cylinder_left'] ?: '-') ?></div>
                <div><strong>CYL phải:</strong> <?= e($order['prescription_detail']['cylinder_right'] ?: '-') ?></div>
                <div><strong>AXIS trái:</strong> <?= e($order['prescription_detail']['axis_left'] ?: '-') ?></div>
                <div><strong>AXIS phải:</strong> <?= e($order['prescription_detail']['axis_right'] ?: '-') ?></div>
                <div><strong>PD:</strong> <?= e($order['prescription_detail']['pd'] ?: '-') ?></div>
                <div><strong>ADD:</strong> <?= e($order['prescription_detail']['add_power'] ?: '-') ?></div>
              </div>
              <p class="small text-secondary mt-3 mb-1">Trạng thái toa: <?= e($order['prescription_detail']['status'] ?: 'pending_review') ?></p>
              <?php if (!empty($order['prescription_detail']['prescription_image'])): ?>
                <div class="mt-3">
                  <p class="small text-secondary mb-2">Phiếu khám mắt đã tải lên</p>
                  <a href="<?= e(asset((string) $order['prescription_detail']['prescription_image'])) ?>" target="_blank" rel="noopener noreferrer">
                    <img
                      src="<?= e(asset((string) $order['prescription_detail']['prescription_image'])) ?>"
                      alt="Phiếu khám mắt"
                      style="width: 100%; max-width: 260px; border-radius: 16px; object-fit: cover; border: 1px solid #e5e7eb;"
                    />
                  </a>
                </div>
              <?php endif; ?>
              <?php if (!empty($order['prescription_detail']['verified_by_name'])): ?>
                <p class="small text-secondary mb-0">
                  Đã xác minh bởi <?= e($order['prescription_detail']['verified_by_name']) ?>
                  <?php if (!empty($order['prescription_detail']['verified_at'])): ?>
                    vào <?= e(date('d/m/Y H:i', strtotime((string) $order['prescription_detail']['verified_at']))) ?>
                  <?php endif; ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($order['shipment'])): ?>
          <div class="col-lg-6">
            <div class="border rounded-4 p-4 h-100">
              <h2 class="h5 mb-3">Vận chuyển</h2>
              <p class="mb-2"><strong>Đơn vị:</strong> <?= e($order['shipment']['carrier'] ?: 'Chưa cập nhật') ?></p>
              <p class="mb-2"><strong>Mã tracking:</strong> <?= e($order['shipment']['tracking_code'] ?: 'Chưa cập nhật') ?></p>  
              <p class="mb-0"><strong>Trạng thái giao hàng:</strong> <?= e($order['shipment']['shipping_status'] ?: 'Chưa cập nhật') ?></p>
            </div>
          </div>
        <?php endif; ?>

        <div class="col-lg-6">
          <div class="border rounded-4 p-4 h-100">
            <h2 class="h5 mb-3">Lịch sử trạng thái</h2>
            <?php if (($order['history'] ?? []) === []): ?>
              <p class="mb-0 text-secondary">Chưa có lịch sử trạng thái.</p>
            <?php endif; ?>
            <?php foreach ($order['history'] ?? [] as $history): ?>
              <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between gap-3">
                  <strong><?= e(build_status_badge($history['new_status'])['label']) ?></strong>
                  <span class="small text-secondary"><?= e(date('d/m/Y H:i', strtotime($history['created_at']))) ?></span>
                </div>
                <p class="small text-secondary mb-0">
                  <?= e($history['changed_by_name'] ?: 'Hệ thống') ?>
                  <?php if (!empty($history['note'])): ?>
                    · <?= e($history['note']) ?>
                  <?php endif; ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if (!empty($order['prescription_workflows'])): ?>
          <div class="col-lg-6">
            <div class="border rounded-4 p-4 h-100">
              <h2 class="h5 mb-3">Tiến trình xử lý toa</h2>
              <?php foreach ($order['prescription_workflows'] as $workflow): ?>
                <div class="border-bottom pb-2 mb-2">
                  <div class="d-flex justify-content-between gap-3">
                    <strong><?= e((string) ($workflow['step_name'] ?? 'workflow')) ?></strong>
                    <span class="small text-secondary"><?= e(date('d/m/Y H:i', strtotime((string) $workflow['updated_at']))) ?></span>
                  </div>
                  <p class="small text-secondary mb-0">
                    <?= e((string) ($workflow['step_status'] ?? 'pending_review')) ?>
                    <?php if (!empty($workflow['handled_by_name'])): ?>
                      · <?= e($workflow['handled_by_name']) ?>
                    <?php endif; ?>
                    <?php if (!empty($workflow['note'])): ?>
                      · <?= e($workflow['note']) ?>
                    <?php endif; ?>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>