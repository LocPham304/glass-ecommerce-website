<?php $accountSection = 'orders'; ?>

<main class="orders-history-main">
  <section class="profile-section">
    <div class="site-container profile-layout">
      <?php require BASE_PATH . '/app/views/partials/account_sidebar.php'; ?>

      <div class="orders-history-content">
        <div class="orders-history-topbar">
          <div>
            <p class="profile-breadcrumb">TÀI KHOẢN <span>›</span> ĐƠN HÀNG CỦA TÔI</p>
            <h1>Đơn hàng của tôi</h1>
          </div>
        </div>

        <div class="order-list">
          <?php if ($orders === []): ?>
            <div class="alert alert-light border">Bạn chưa có đơn hàng nào.</div>
          <?php endif; ?>

          <?php foreach ($orders as $order): ?>
            <?php $status = build_status_badge($order['order_status']); ?>
            <article class="order-card">
              <div class="order-card__head">
                <div class="order-card__meta">
                  <h2>#<?= e($order['order_code']) ?></h2>
                  <p>Ngày đặt: <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></p>
                </div>
                <span class="history-status"><?= e($status['label']) ?></span>
              </div>
              <div class="order-card__summary">
                <p><?= e($order['first_item_name'] ?? 'Đơn hàng') ?> · <?= e((string) $order['items_count']) ?> sản phẩm</p>
                <div class="order-card__summary-right">
                  <div class="order-total">
                    <span>Thành tiền:</span>
                    <strong><?= e(format_currency($order['total_amount'])) ?></strong>
                  </div>
                  <div class="order-actions">
                    <a class="order-button order-button--primary" href="<?= e(url('/order') . '?id=' . urlencode($order['id'])) ?>">Xem chi tiết</a>
                    <a class="order-button" href="<?= e(url('/after-sales')) ?>">Đổi trả</a>
                  </div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</main>