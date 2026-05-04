<?php
$vouchers = $vouchers ?? [];

$normalizeVoucherName = static function (array $voucher): string {
    $name = trim((string) ($voucher['name'] ?? ''));
    $code = strtoupper(trim((string) ($voucher['code'] ?? '')));

    if ($code === 'WELCOME20' && (str_contains($name, '?') || str_contains($name, 'Kh'))) {
        return 'Khách mới';
    }

    return $name !== '' ? $name : 'Voucher';
};

$formatVoucherType = static function (string $type): array {
    return match ($type) {
        'percent' => ['label' => 'Phần trăm', 'class' => 'promotion-type promotion-type--percent'],
        'ship' => ['label' => 'Miễn phí ship', 'class' => 'promotion-type promotion-type--ship'],
        default => ['label' => 'Số tiền', 'class' => 'promotion-type promotion-type--cash'],
    };
};

$formatVoucherStatus = static function (array $voucher): array {
    $now = time();
    $startAt = !empty($voucher['start_at']) ? strtotime((string) $voucher['start_at']) : null;
    $expiredAt = !empty($voucher['expired_at']) ? strtotime((string) $voucher['expired_at']) : null;
    $usageLimit = $voucher['usage_limit'] ?? null;
    $hasUsageLimit = $usageLimit !== null && $usageLimit !== '';

    if (!(bool) ($voucher['is_active'] ?? 0)) {
        return ['label' => 'Lưu nháp', 'class' => 'status-pill status-pill--muted'];
    }

    if ($startAt !== null && $startAt > $now) {
        return ['label' => 'Sắp diễn ra', 'class' => 'status-pill status-pill--pending'];
    }

    if ($expiredAt !== null && $expiredAt < $now) {
        return ['label' => 'Đã kết thúc', 'class' => 'status-pill status-pill--muted'];
    }

    if ($hasUsageLimit && (int) $usageLimit <= 0) {
        return ['label' => 'Hết hạn', 'class' => 'status-pill status-pill--muted'];
    }

    return ['label' => 'Đang chạy', 'class' => 'status-pill status-pill--active'];
};
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Khuyến mãi</h1>
  </div>

  <label class="admin-products-search" aria-label="Tìm kiếm voucher">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input type="search" placeholder="Tìm theo mã voucher..." data-promo-search />
  </label>

  <a class="admin-primary-button admin-primary-button--topbar" href="<?= e(url('/admin/promotions/create')) ?>">
    <i class="fa-solid fa-plus" aria-hidden="true"></i>
    Tạo voucher
  </a>
</header>

<section class="admin-content admin-content--promotions">
  <section class="admin-panel promotion-filter-panel">
    <div class="promotion-filter-grid">
      <label class="product-filter-field">
        <span>MÃ VOUCHER</span>
        <input type="search" placeholder="Ví dụ: COMBO20" data-filter-code />
      </label>
      <label class="product-filter-field">
        <span>LOẠI GIẢM GIÁ</span>
        <select data-filter-type>
          <option value="all">Tất cả loại</option>
          <option value="Phần trăm">Phần trăm</option>
          <option value="Số tiền">Số tiền</option>
          <option value="Miễn phí ship">Miễn phí ship</option>
        </select>
      </label>
      <label class="product-filter-field">
        <span>TRẠNG THÁI</span>
        <select data-filter-status>
          <option value="all">Tất cả trạng thái</option>
          <option value="Đang chạy">Đang chạy</option>
          <option value="Sắp diễn ra">Sắp diễn ra</option>
          <option value="Hết hạn">Hết hạn</option>
          <option value="Đã kết thúc">Đã kết thúc</option>
          <option value="Lưu nháp">Lưu nháp</option>
        </select>
      </label>
    </div>

    <div class="product-toolbar">

      <p class="product-toolbar__summary" data-promo-summary data-promo-total="<?= e((string) count($vouchers)) ?>">
        Hiển thị <?= count($vouchers) > 0 ? '1' : '0' ?> - <?= e((string) count($vouchers)) ?> trong số <?= e((string) count($vouchers)) ?> voucher
      </p>
    </div>
  </section>

  <section class="admin-panel promotion-table-panel">
    <div class="promotion-table" role="table" aria-label="Danh sách voucher">
      <div class="promotion-table__head" role="row">
        <span>MÃ VOUCHER</span>
        <span>MÔ TẢ</span>
        <span>LOẠI GIẢM</span>
        <span>ĐIỀU KIỆN</span>
        <span>LƯỢT DÙNG</span>
        <span>HIỆU LỰC</span>
        <span>TRẠNG THÁI</span>
        <span>HÀNH ĐỘNG</span>
      </div>

      <div class="promotion-table__body" data-promo-table>
        <?php foreach ($vouchers as $voucher): ?>
          <?php
          $voucherName = $normalizeVoucherName($voucher);
          $type = $formatVoucherType((string) ($voucher['discount_type'] ?? 'amount'));
          $status = $formatVoucherStatus($voucher);
          $valueLabel = ($voucher['discount_type'] ?? '') === 'percent'
              ? rtrim(rtrim(number_format((float) $voucher['discount_value'], 2, '.', ''), '0'), '.') . '%'
              : (($voucher['discount_type'] ?? '') === 'ship'
                  ? 'Free ship'
                  : number_format((float) $voucher['discount_value'], 0, ',', '.') . 'đ');
          $condition = !empty($voucher['min_order_value'])
              ? 'Đơn từ ' . number_format((float) $voucher['min_order_value'], 0, ',', '.') . 'đ'
              : 'Không giới hạn';
          $dateLabel = trim((!empty($voucher['start_at']) ? date('d/m/Y', strtotime((string) $voucher['start_at'])) : '--') . ' - ' . (!empty($voucher['expired_at']) ? date('d/m/Y', strtotime((string) $voucher['expired_at'])) : '--'));
          ?>
          <article class="promotion-row" data-code="<?= e((string) $voucher['code']) ?>" data-type="<?= e($type['label']) ?>" data-status="<?= e($status['label']) ?>">
            <div class="promotion-code">
              <strong><?= e((string) $voucher['code']) ?></strong>
              <span><?= e($voucherName) ?></span>
            </div>
            <div class="promotion-desc">Giảm <?= e($valueLabel) ?> cho chiến dịch <?= e($voucherName) ?>.</div>
            <div><span class="<?= e($type['class']) ?>"><?= e($valueLabel) ?></span></div>
            <div class="promotion-rule"><?= e($condition) ?></div>
            <div class="promotion-usage"><?= e((string) ((int) ($voucher['used_count'] ?? 0))) ?> / <?= e((string) ((int) ($voucher['usage_limit'] ?? 0))) ?></div>
            <div class="promotion-date"><?= e($dateLabel) ?></div>
            <div><span class="<?= e($status['class']) ?>"><?= e($status['label']) ?></span></div>
            <div class="promotion-actions">
              <a href="<?= e(url('/admin/promotions/create')) ?>" aria-label="Chỉnh sửa voucher">
                <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>

  </section>
</section>