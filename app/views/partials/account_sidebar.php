<?php
$sidebarUser = $user ?? auth_user();
$accountSection = $accountSection ?? 'profile';
?>

<aside class="profile-sidebar">
  <div class="sidebar-card sidebar-account">
    <div class="sidebar-account__head">
      <img
        src="<?= e(asset('assets/images/profile/profile-avatar.png')) ?>"
        alt="<?= e($sidebarUser['full_name'] ?? 'User') ?>"
      />
      <div>
        <h2><?= e($sidebarUser['full_name'] ?? 'Tài khoản') ?></h2>
        <p><?= e($sidebarUser['email'] ?? 'Khách hàng') ?></p>
      </div>
    </div>

    <nav class="sidebar-menu" aria-label="Tài khoản">
      <a class="sidebar-menu__item <?= $accountSection === 'profile' ? 'is-active' : '' ?>" href="<?= e(url('/profile')) ?>">
        <i class="fa-regular fa-user" aria-hidden="true"></i>
        <span>Thông tin cá nhân</span>
      </a>
      <a class="sidebar-menu__item <?= $accountSection === 'orders' ? 'is-active' : '' ?>" href="<?= e(url('/orders')) ?>">
        <i class="fa-regular fa-rectangle-list" aria-hidden="true"></i>
        <span>Đơn hàng của tôi</span>
      </a>
      <a class="sidebar-menu__item <?= $accountSection === 'after_sales' ? 'is-active' : '' ?>" href="<?= e(url('/after-sales')) ?>">
        <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
        <span>Đổi trả / hoàn tiền</span>
      </a>
      <a class="sidebar-menu__item sidebar-menu__item--logout" href="<?= e(url('/logout')) ?>">
        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
        <span>Đăng xuất</span>
      </a>
    </nav>
  </div>

  <div class="sidebar-card sidebar-support">
    <h3>Cần hỗ trợ?</h3>
    <p>Đội ngũ CSKH của chúng tôi luôn sẵn sàng giải đáp thắc mắc của bạn.</p>
    <a class="sidebar-support__button" href="#footer">Liên hệ CSKH</a>
  </div>
</aside>
