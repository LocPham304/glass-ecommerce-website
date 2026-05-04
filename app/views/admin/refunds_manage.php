<?php
$requests = $requests ?? [];

$statusLabels = [
    'pending' => 'Mới tạo',
    'processing' => 'Đang xác minh',
    'approved' => 'Đã duyệt',
    'resolved' => 'Hoàn tất',
    'rejected' => 'Từ chối',
];

$statusClasses = [
    'pending' => 'status-pill--pending',
    'processing' => 'status-pill--processing',
    'approved' => 'status-pill--active',
    'resolved' => 'status-pill--shipping',
    'rejected' => 'status-pill--cancelled',
];

$typeLabels = [
    'exchange' => 'Đổi hàng',
    'refund' => 'Hoàn tiền',
    'warranty' => 'Bảo hành',
];

$typeClasses = [
    'exchange' => 'return-type-pill--exchange',
    'refund' => 'return-type-pill--refund',
    'warranty' => 'return-type-pill--warranty',
];

$refundStatusLabels = [
    'pending' => 'Chờ hoàn tiền',
    'processed' => 'Đã hoàn tiền',
    'rejected' => 'Từ chối hoàn tiền',
];

$requestCount = count($requests);
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Quản lý trả hàng</h1>
  </div>

  <label class="admin-products-search" aria-label="Tìm kiếm yêu cầu trả hàng">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input
      type="search"
      placeholder="Tìm theo mã yêu cầu, mã đơn, khách hàng..."
      data-return-search
    />
  </label>

</header>

<section class="admin-content admin-content--returns">
  <section class="admin-panel return-filter-panel">
    <div class="return-filter-grid">
      <label class="product-filter-field">
        <span>MÃ YÊU CẦU / MÃ ĐƠN</span>
        <input
          type="search"
          placeholder="Ví dụ: AFS... hoặc CV..."
          data-filter-return-keyword
        />
      </label>

      <label class="product-filter-field">
        <span>LOẠI YÊU CẦU</span>
        <select data-filter-return-type>
          <option value="all">Tất cả loại yêu cầu</option>
          <?php foreach ($typeLabels as $typeValue => $typeLabel): ?>
            <option value="<?= e($typeValue) ?>"><?= e($typeLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="product-filter-field">
        <span>TRẠNG THÁI</span>
        <select data-filter-return-status>
          <option value="all">Tất cả trạng thái</option>
          <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
            <option value="<?= e($statusValue) ?>"><?= e($statusLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="product-filter-field">
        <span>THỜI GIAN</span>
        <select data-filter-return-date>
          <option value="all">Tất cả thời gian</option>
          <option value="today">Hôm nay</option>
          <option value="week">7 ngày qua</option>
          <option value="month">30 ngày qua</option>
        </select>
      </label>
    </div>

    <div class="product-toolbar">

      <p class="product-toolbar__summary" data-return-summary>
        Hiển thị <?= e($requestCount > 0 ? '1' : '0') ?> - <?= e((string) $requestCount) ?> trong số <?= e((string) $requestCount) ?> yêu cầu đổi trả
      </p>
    </div>
  </section>

  <section class="admin-panel return-table-panel">
    <?php if ($requests === []): ?>
      <div class="return-empty-state">
        <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
        <strong>Chưa có yêu cầu đổi trả nào</strong>
        <p>Danh sách sẽ hiển thị tại đây khi khách hàng gửi yêu cầu đổi trả hoặc hoàn tiền.</p>
      </div>
    <?php else: ?>
      <div class="return-table" role="table" aria-label="Danh sách yêu cầu trả hàng">
        <div class="return-table__head" role="row">
          <span>MÃ YÊU CẦU</span>
          <span>MÃ ĐƠN</span>
          <span>KHÁCH HÀNG (SĐT)</span>
          <span>EMAIL</span>
          <span>LOẠI YÊU CẦU</span>
          <span>LÝ DO & GHI CHÚ</span>
          <span>TRẠNG THÁI</span>
          <span>HÀNH ĐỘNG</span>
        </div>

        <div class="return-table__body" data-return-table>
          <?php foreach ($requests as $request): ?>
            <?php
            $status = (string) ($request['status'] ?? 'pending');
            $statusLabel = $statusLabels[$status] ?? ucfirst($status);
            $statusClass = $statusClasses[$status] ?? 'status-pill--pending';
            $type = (string) ($request['request_type'] ?? 'exchange');
            $typeLabel = $typeLabels[$type] ?? ucfirst($type);
            $typeClass = $typeClasses[$type] ?? 'return-type-pill--exchange';
            $createdAt = (string) ($request['created_at'] ?? '');
            $createdTimestamp = $createdAt !== '' ? strtotime($createdAt) : false;
            $createdDate = $createdTimestamp !== false ? date('Y-m-d', $createdTimestamp) : '';
            $createdLabel = $createdTimestamp !== false ? date('d/m/Y', $createdTimestamp) : '-';
            $requestCode = (string) ($request['id'] ?? '');
            $orderCode = (string) ($request['order_code'] ?? '');
            $fullName = (string) ($request['full_name'] ?? '-');
            $phone = (string) ($request['phone'] ?? '');
            $email = (string) ($request['email'] ?? '');
            $reason = trim((string) ($request['reason'] ?? ''));
            $description = trim((string) ($request['description'] ?? ''));
            $refundStatus = (string) ($request['refund_status'] ?? '');
            $refundNote = trim((string) ($request['refund_note'] ?? ''));
            $refundAmount = (float) ($request['refund_amount'] ?? 0);
            $keyword = trim($requestCode . ' ' . $orderCode . ' ' . $fullName . ' ' . $phone . ' ' . $email . ' ' . $reason . ' ' . $description);
            ?>
            <article
              class="return-row"
              data-keyword="<?= e($keyword) ?>"
              data-type="<?= e($type) ?>"
              data-status="<?= e($status) ?>"
              data-date="<?= e($createdDate) ?>"
            >
              <div class="return-code">
                <strong><?= e($requestCode) ?></strong>
                <span>Gửi ngày <?= e($createdLabel) ?></span>
              </div>
              <div class="return-order">#<?= e($orderCode) ?></div>
              <div class="return-customer">
                <strong><?= e($fullName) ?></strong>
                <span><?= e($phone !== '' ? $phone : '-') ?></span>
              </div>
              <div class="return-email"><?= e($email !== '' ? $email : '-') ?></div>
              <div>
                <span class="return-type-pill <?= e($typeClass) ?>"><?= e($typeLabel) ?></span>
              </div>
              <div class="return-detail">
                <strong><?= e($reason !== '' ? $reason : 'Không có lý do') ?></strong>
                <?php if ($description !== '' || $refundStatus !== '' || $refundAmount > 0): ?>
                  <span>
                    <?= e($description !== '' ? $description : 'Chưa có ghi chú thêm') ?>
                    <?php if ($refundStatus !== ''): ?>
                      · <?= e($refundStatusLabels[$refundStatus] ?? $refundStatus) ?>
                    <?php endif; ?>
                    <?php if ($refundAmount > 0): ?>
                      · <?= e(format_currency($refundAmount)) ?>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              </div>
              <div>
                <span class="status-pill <?= e($statusClass) ?>" data-return-status-badge><?= e($statusLabel) ?></span>
              </div>
              <form class="return-actions" method="POST" action="<?= e(url('/admin/refunds/update')) ?>" data-return-status-form>
                <input type="hidden" name="request_id" value="<?= e($requestCode) ?>" />
                <input type="hidden" name="refund_amount" value="<?= e((string) ($request['refund_amount'] ?? '')) ?>" />
                <input type="hidden" name="refund_method" value="<?= e((string) ($request['refund_method'] ?? 'bank_transfer')) ?>" />
                <input type="hidden" name="refund_status" value="<?= e((string) ($request['refund_status'] ?? 'pending')) ?>" />
                <input type="hidden" name="note" value="<?= e($refundNote) ?>" />
                <select class="return-status-select" name="status" data-return-status-select>
                  <?php foreach ($statusLabels as $statusValue => $optionLabel): ?>
                    <option value="<?= e($statusValue) ?>" <?= $status === $statusValue ? 'selected' : '' ?>>
                      <?= e($optionLabel) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button
                  type="submit"
                  class="return-status-save"
                  data-return-status-save
                  aria-label="Lưu trạng thái"
                >
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </form>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="return-empty-state" data-return-empty hidden>
        <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
        <strong>Không có yêu cầu phù hợp</strong>
        <p>Hãy thử đổi bộ lọc, lý do hoặc từ khóa tìm kiếm để xem thêm hồ sơ trả hàng.</p>
      </div>

      <div class="return-admin-footer">
        <div class="return-admin-footer__note">
         
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
