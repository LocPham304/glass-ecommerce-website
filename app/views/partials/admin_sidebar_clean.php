<?php
$currentAdminSection = $currentAdminSection ?? 'dashboard';
$adminUser = auth_user();
?>

<aside class="admin-sidebar">
  <div class="admin-sidebar__brand">
    <a class="admin-brand" href="<?= e(url('/admin')) ?>" aria-label="ClearVision Admin">
      <img
        class="admin-brand__mark"
        src="<?= e(asset('assets/images/common/logo.png')) ?>"
        alt="ClearVision logo"
      />
      <span class="admin-brand__text">
        <span class="admin-brand__text-main">CLEAR</span>
        <span class="admin-brand__text-accent">VISION</span>
      </span>
    </a>
  </div>

  <button
    class="admin-menu-toggle"
    type="button"
    aria-expanded="false"
    aria-controls="admin-nav"
  >
    <i class="fa-solid fa-bars" aria-hidden="true"></i>
    <span>Menu quản trị</span>
  </button>

  <nav class="admin-nav" id="admin-nav" aria-label="Quản trị viên">
    <a class="admin-nav__item <?= $currentAdminSection === 'dashboard' ? 'is-active' : '' ?>" href="<?= e(url('/admin')) ?>">
      <i class="fa-solid fa-table-cells-large" aria-hidden="true"></i>
      <span>Tổng quan</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'products' ? 'is-active' : '' ?>" href="<?= e(url('/admin/products')) ?>">
      <i class="fa-solid fa-glasses" aria-hidden="true"></i>
      <span>Sản phẩm</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'promotions' ? 'is-active' : '' ?>" href="<?= e(url('/admin/promotions')) ?>">
      <i class="fa-solid fa-tag" aria-hidden="true"></i>
      <span>Khuyến mãi</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'orders' ? 'is-active' : '' ?>" href="<?= e(url('/admin/orders')) ?>">
      <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
      <span>Đơn hàng</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'customers' ? 'is-active' : '' ?>" href="<?= e(url('/admin/customers')) ?>">
      <i class="fa-solid fa-user-group" aria-hidden="true"></i>
      <span>Người dùng</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'refunds' ? 'is-active' : '' ?>" href="<?= e(url('/admin/refunds')) ?>">
      <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
      <span>Đổi trả</span>
    </a>
    <a class="admin-nav__item <?= $currentAdminSection === 'reports' ? 'is-active' : '' ?>" href="<?= e(url('/admin/reports')) ?>">
      <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
      <span>Báo cáo</span>
    </a>
  </nav>

  <div class="admin-sidebar__footer">
    <div class="admin-user-card">
      <div class="admin-user-card__avatar">
        <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
      </div>
      <div class="admin-user-card__meta">
        <strong><?= e($adminUser['full_name'] ?? 'Admin') ?></strong>
        <span><?= e(ucfirst($adminUser['role_name'] ?? 'staff')) ?></span>
      </div>
      <a class="admin-user-card__action" href="<?= e(url('/logout')) ?>" aria-label="Đăng xuất">
        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
      </a>
    </div>
    <a class="admin-store-link" href="<?= e(url('/')) ?>">
      <i class="fa-solid fa-store" aria-hidden="true"></i>
      <span>Quay lại cửa hàng</span>
    </a>
  </div>
</aside>