<?php
$customers = $customers ?? [];
$currentAdminId = auth_user()['id'] ?? null;

$roleLabels = [
    'admin' => 'Quản trị viên',
    'manager' => 'Quản lý',
    'sales' => 'Nhân viên bán hàng',
    'operations' => 'Nhân viên vận hành',
    'customer' => 'Khách hàng',
];

$avatarClasses = [
    'user-avatar--amber',
    'user-avatar--green',
    'user-avatar--violet',
    'user-avatar--blue',
    'user-avatar--rose',
];

$getRoleLabel = static fn(string $role): string => $roleLabels[$role] ?? ucfirst($role);
$getKind = static fn(string $role): string => $role === 'customer' ? 'customer' : 'staff';
$getKindLabel = static fn(string $kind): string => $kind === 'customer' ? 'Khách hàng' : 'Nhân viên';
$getStatusLabel = static fn(string $status): string => $status === 'active' ? 'Hoạt động' : 'Tạm khóa';
$getStatusClass = static fn(string $status): string => $status === 'active' ? 'status-pill--active' : 'status-pill--muted';
$getInitials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        $letters .= strtoupper(substr($part, 0, 1));
        if (strlen($letters) >= 2) {
            break;
        }
    }

    return $letters !== '' ? $letters : 'CV';
};

$staffCount = 0;
$customerCount = 0;
$activeCount = 0;
$rolesInUse = [];

foreach ($customers as $customer) {
    $role = (string) ($customer['role_name'] ?? '');
    $status = (string) ($customer['status'] ?? '');
    $kind = $getKind($role);

    if ($kind === 'customer') {
        $customerCount += 1;
    } else {
        $staffCount += 1;
    }

    if ($status === 'active') {
        $activeCount += 1;
    }

    if ($role !== '') {
        $rolesInUse[$role] = $getRoleLabel($role);
    }
}

asort($rolesInUse);
$totalCustomers = count($customers);
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Quản lý người dùng</h1>
  </div>

  <label class="admin-products-search" aria-label="Tìm kiếm người dùng">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input
      type="search"
      placeholder="Tìm theo tên, email, số điện thoại..."
      data-user-search
    />
  </label>

  <a
    class="admin-primary-button admin-primary-button--topbar"
    href="<?= e(url('/admin/users/create')) ?>"
  >
    <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
    Thêm người dùng
  </a>
</header>

<section class="admin-content admin-content--users">
  <section class="admin-panel user-overview-panel">
    <div class="user-overview">
      <div class="user-overview__item">
        <span>NHÂN VIÊN VẬN HÀNH</span>
        <strong data-overview-staff><?= e((string) $staffCount) ?></strong>
      </div>
      <div class="user-overview__item">
        <span>KHÁCH HÀNG</span>
        <strong data-overview-customer><?= e((string) $customerCount) ?></strong>
      </div>
      <div class="user-overview__item">
        <span>TÀI KHOẢN HOẠT ĐỘNG</span>
        <strong data-overview-active><?= e((string) $activeCount) ?></strong>
      </div>
    </div>
  </section>

  <section class="admin-panel user-filter-panel">
    <div class="user-tabs" role="tablist" aria-label="Nhóm người dùng">
      <button class="user-tab is-active" type="button" data-user-kind="all">
        <span>Tất cả</span>
        <strong data-tab-count="all"><?= e((string) $totalCustomers) ?></strong>
      </button>
      <button class="user-tab" type="button" data-user-kind="staff">
        <span>Nhân viên vận hành</span>
        <strong data-tab-count="staff"><?= e((string) $staffCount) ?></strong>
      </button>
      <button class="user-tab" type="button" data-user-kind="customer">
        <span>Khách hàng</span>
        <strong data-tab-count="customer"><?= e((string) $customerCount) ?></strong>
      </button>
    </div>

    <div class="user-filter-grid">
      <label class="product-filter-field">
        <span>VAI TRÒ</span>
        <select data-role-filter>
          <option value="all">Tất cả vai trò</option>
          <?php foreach ($rolesInUse as $role => $label): ?>
            <option value="<?= e($role) ?>"><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="product-filter-field">
        <span>TRẠNG THÁI</span>
        <select data-status-filter>
          <option value="all">Tất cả trạng thái</option>
          <option value="active">Hoạt động</option>
          <option value="inactive">Tạm khóa</option>
        </select>
      </label>
    </div>

    <div class="product-toolbar">

      <p class="product-toolbar__summary" data-user-summary>
        Hiển thị <?= e($totalCustomers > 0 ? '1' : '0') ?> - <?= e((string) $totalCustomers) ?> trong số <?= e((string) $totalCustomers) ?> người dùng
      </p>
    </div>
  </section>

  <section class="admin-panel user-table-panel">
    <?php if ($customers === []): ?>
      <div class="user-empty-state">
        <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
        <strong>Chưa có người dùng nào</strong>
        <p>Danh sách sẽ hiển thị tại đây sau khi hệ thống có tài khoản người dùng.</p>
      </div>
    <?php else: ?>
      <div class="user-table" role="table" aria-label="Danh sách người dùng">
        <div class="user-table__head" role="row">
          <span>NGƯỜI DÙNG</span>
          <span>PHÂN LOẠI</span>
          <span>VAI TRÒ</span>
          <span>LIÊN HỆ</span>
          <span>TRẠNG THÁI</span>
          <span>CẬP NHẬT</span>
          <span>HÀNH ĐỘNG</span>
        </div>

        <div class="user-table__body" data-user-table>
          <?php foreach ($customers as $index => $customer): ?>
            <?php
            $role = (string) ($customer['role_name'] ?? '');
            $kind = $getKind($role);
            $status = (string) ($customer['status'] ?? 'inactive');
            $status = $status === 'active' ? 'active' : 'inactive';
            $fullName = (string) ($customer['full_name'] ?? 'ClearVision User');
            $email = (string) ($customer['email'] ?? '');
            $phone = (string) ($customer['phone'] ?? '');
            $updatedAt = (string) (($customer['updated_at'] ?? '') ?: ($customer['created_at'] ?? ''));
            $updatedTimestamp = $updatedAt !== '' ? strtotime($updatedAt) : false;
            $updatedLabel = $updatedTimestamp !== false ? date('d/m/Y', $updatedTimestamp) : '-';
            $avatarClass = $avatarClasses[$index % count($avatarClasses)];
            $keyword = trim($fullName . ' ' . $email . ' ' . $phone . ' ' . $getRoleLabel($role));
            $customerId = (string) ($customer['id'] ?? '');
            $editUrl = url('/admin/users/edit?id=' . rawurlencode($customerId));
            ?>
            <article
              class="user-row"
              data-kind="<?= e($kind) ?>"
              data-role="<?= e($role) ?>"
              data-status="<?= e($status) ?>"
              data-keyword="<?= e($keyword) ?>"
            >
              <div class="user-profile">
                <div class="user-avatar <?= e($avatarClass) ?>"><?= e($getInitials($fullName)) ?></div>
                <div>
                  <strong><?= e($fullName) ?></strong>
                  <span>ID: <?= e((string) ($customer['id'] ?? '-')) ?></span>
                </div>
              </div>
              <div>
                <span class="user-kind-pill user-kind-pill--<?= e($kind) ?>"><?= e($getKindLabel($kind)) ?></span>
              </div>
              <div class="user-role"><?= e($getRoleLabel($role)) ?></div>
              <div class="user-contact">
                <span><?= e($email !== '' ? $email : '-') ?></span>
                <span><?= e($phone !== '' ? $phone : '-') ?></span>
              </div>
              <div>
                <span class="status-pill <?= e($getStatusClass($status)) ?>"><?= e($getStatusLabel($status)) ?></span>
              </div>
              <div class="user-updated"><?= e($updatedLabel) ?></div>
              <div class="user-actions">
                <a href="<?= e($editUrl) ?>" aria-label="Sửa người dùng <?= e($fullName) ?>">
                  <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <form
                  method="POST"
                  action="<?= e(url('/admin/users/delete')) ?>"
                  data-delete-user-form
                  data-user-name="<?= e($fullName) ?>"
                >
                  <input type="hidden" name="user_id" value="<?= e($customerId) ?>" />
                  <button
                    type="submit"
                    aria-label="Xóa người dùng <?= e($fullName) ?>"
                    <?= $currentAdminId === $customerId ? 'disabled' : '' ?>
                  >
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="user-empty-state" data-user-empty hidden>
        <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
        <strong>Không có người dùng phù hợp</strong>
        <p>Hãy thử đổi nhóm tài khoản, vai trò hoặc từ khóa tìm kiếm để xem thêm kết quả.</p>
      </div>

      <div class="user-table-footer">
        <div class="user-table-footer__note">
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