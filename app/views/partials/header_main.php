<?php $navigationCategories = $navigationCategories ?? []; ?>
<?php $currentUser = auth_user(); ?>
<?php $cartCount = $headerCartCount ?? 0; ?>
<?php $returnLink = url('/after-sales'); ?>
<?php
$categoryLabels = [
  'gong-kinh' => 'Gọng kính',
  'trong-kinh' => 'Tròng kính',
  'kinh-ram' => 'Kính râm',
  'phu-kien' => 'Phụ kiện',
];
?>

<header class="site-header">
  <div class="site-container header-inner">
    <a class="brand" href="<?= e(url('/')) ?>" aria-label="ClearVision">
      <img
        class="brand__mark"
        src="<?= e(asset('assets/images/common/logo.png')) ?>"
        alt="ClearVision logo"
      />
      <span class="brand__text">
        <span class="brand__text-main">CLEAR</span>
        <span class="brand__text-accent">VISION</span>
      </span>
    </a>

    <button
      class="menu-toggle"
      type="button"
      aria-expanded="false"
      aria-controls="site-nav"
      aria-label="Mở menu"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="site-nav" id="site-nav" aria-label="Điều hướng người dùng">
      <a href="<?= e(url('/')) ?>">Trang chủ</a>
      <div class="nav-dropdown">
        <button
          class="nav-dropdown__toggle"
          type="button"
          aria-expanded="false"
        >
          <span>Sản phẩm</span>
          <i class="fa-solid fa-angle-down" aria-hidden="true"></i>
        </button>
        <div class="nav-dropdown__menu">
          <a href="<?= e(url('/shop')) ?>">Tất cả sản phẩm</a>
          <?php foreach ($navigationCategories as $category): ?>
            <a href="<?= e(url('/shop') . '?category=' . urlencode($category['id'])) ?>">
              <?= e($categoryLabels[$category['slug'] ?? ''] ?? ($category['name'] ?? 'Sản phẩm')) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <a href="<?= e(url('/about')) ?>">Về chúng tôi</a>
      <a href="<?= e(url('/warranty')) ?>">Chính sách</a>
      <a href="<?= e($returnLink) ?>">Đổi trả &amp; Hoàn tiền</a>
    </nav>

    <div class="header-tools">
      <form class="search-box" action="<?= e(url('/shop')) ?>" method="GET" aria-label="Tìm kiếm">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path
            d="M10.5 4a6.5 6.5 0 1 0 4.08 11.56l4.43 4.43 1.41-1.41-4.43-4.43A6.5 6.5 0 0 0 10.5 4Zm0 2a4.5 4.5 0 1 1 0 9a4.5 4.5 0 0 1 0-9Z"
          ></path>
        </svg>
        <input
          type="search"
          name="keyword"
          placeholder="Tìm kiếm..."
          aria-label="Tìm kiếm sản phẩm"
          value="<?= e(query_value('keyword')) ?>"
        />
      </form>

      <a
        class="icon-button cart-button"
        href="<?= e(url('/cart')) ?>"
        aria-label="Giỏ hàng"
        title="Giỏ hàng"
      >
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path
            d="M7 4H3v2h2.2l1.8 8.04A2 2 0 0 0 8.95 16h8.63a2 2 0 0 0 1.94-1.52L21 8H8.1l-.44-2H7Zm2.5 14A1.5 1.5 0 1 0 11 19.5 1.5 1.5 0 0 0 9.5 18Zm8 0A1.5 1.5 0 1 0 19 19.5 1.5 1.5 0 0 0 17.5 18Z"
          ></path>
        </svg>
        <span class="cart-count"><?= e((string) $cartCount) ?></span>
      </a>

      <a
        class="icon-button profile-button"
        href="<?= e($currentUser ? (has_role(['admin', 'manager', 'sales', 'operations']) ? url('/admin') : url('/profile')) : url('/login')) ?>"
        aria-label="Tài khoản"
        title="<?= e($currentUser ? ($currentUser['full_name'] ?? 'Tài khoản') : 'Đăng nhập') ?>"
      >
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path
            d="M12 12a4 4 0 1 0-4-4a4 4 0 0 0 4 4Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"
          ></path>
        </svg>
      </a>
    </div>
  </div>
</header>
