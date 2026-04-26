<?php
$accountStats = $accountStats ?? [];
$defaultAddress = $defaultAddress ?? [];
$recentOrders = $recentOrders ?? [];
$accountSection = 'profile';
?>

<main class="profile-main">
  <section class="profile-section">
    <div class="site-container profile-layout">
      <?php require BASE_PATH . '/app/views/partials/account_sidebar.php'; ?>

      <div class="profile-content">
        <div class="profile-heading">
          <p class="profile-breadcrumb">TRANG CHỦ <span>›</span> TÀI KHOẢN</p>
          <h1>Chào mừng trở lại, <?= e($user['full_name'] ?? 'bạn') ?>!</h1>
        </div>

        <section class="profile-stats" aria-label="Tổng quan tài khoản">
          <article class="stat-card stat-card--blue"><p>TỔNG ĐƠN HÀNG</p><strong><?= e((string) ($accountStats['total_orders'] ?? 0)) ?></strong></article>
          <article class="stat-card stat-card--green"><p>ĐƠN HOÀN TẤT</p><strong><?= e((string) ($accountStats['completed_orders'] ?? 0)) ?></strong></article>
          <article class="stat-card stat-card--orange"><p>ĐANG XỬ LÝ</p><strong><?= e((string) ($accountStats['processing_orders'] ?? 0)) ?></strong></article>
          <article class="stat-card stat-card--rose"><p>YÊU CẦU ĐỔI TRẢ</p><strong><?= e((string) ($accountStats['after_sales_count'] ?? 0)) ?></strong></article>
        </section>

        <section class="content-card personal-card">
          <div class="section-card__heading"><h2>Thông tin cá nhân</h2></div>
          <form class="personal-form" method="POST" action="<?= e(url('/profile')) ?>">
            <label class="profile-field"><span>Họ tên</span><input type="text" name="full_name" value="<?= e($user['full_name'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Email</span><input type="email" value="<?= e($user['email'] ?? '') ?>" disabled /></label>
            <label class="profile-field"><span>Số điện thoại</span><input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Giới tính</span><input type="text" name="gender" value="<?= e($user['gender'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Ngày sinh</span><input type="date" name="date_of_birth" value="<?= e($user['date_of_birth'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Người nhận</span><input type="text" name="receiver_name" value="<?= e($defaultAddress['receiver_name'] ?? $user['full_name'] ?? '') ?>" /></label>
            <label class="profile-field"><span>SĐT nhận</span><input type="text" name="receiver_phone" value="<?= e($defaultAddress['receiver_phone'] ?? $user['phone'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Tỉnh / Thành</span><input type="text" name="province" value="<?= e($defaultAddress['province'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Quận / Huyện</span><input type="text" name="district" value="<?= e($defaultAddress['district'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Phường / Xã</span><input type="text" name="ward" value="<?= e($defaultAddress['ward'] ?? '') ?>" /></label>
            <label class="profile-field"><span>Địa chỉ</span><input type="text" name="address_line" value="<?= e($defaultAddress['address_line'] ?? '') ?>" /></label>
            <div class="personal-card__actions">
              <button class="profile-primary-button" type="submit">Cập nhật thông tin</button>
            </div>
          </form>
        </section>

        <section class="content-card orders-card">
          <div class="section-card__heading section-card__heading--between">
            <h2>Đơn hàng gần đây</h2>
            <a href="<?= e(url('/orders')) ?>">Xem tất cả</a>
          </div>
          <?php foreach ($recentOrders as $order): ?>
            <?php $status = build_status_badge($order['order_status']); ?>
            <div class="orders-table__row" role="row">
              <strong>#<?= e($order['order_code']) ?></strong>
              <span><?= e(date('d/m/Y', strtotime($order['created_at']))) ?></span>
              <strong><?= e(format_currency($order['total_amount'])) ?></strong>
              <span class="order-status"><?= e($status['label']) ?></span>
              <a href="<?= e(url('/order') . '?id=' . urlencode($order['id'])) ?>"><i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            </div>
          <?php endforeach; ?>
        </section>
      </div>
    </div>
  </section>
</main>