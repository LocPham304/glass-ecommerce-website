<?php $stats = $dashboardStats ?? []; ?>

<header class="admin-topbar">
  <h1>Tổng quan</h1>
</header>

<section class="admin-content">
  <section class="admin-stats" aria-label="Thống kê nhanh">
    <article class="admin-stat-card"><p>ĐƠN HÀNG</p><strong><?= e((string) ($stats['total_orders'] ?? 0)) ?></strong></article>
    <article class="admin-stat-card"><p>DOANH THU</p><strong><?= e(format_currency($stats['total_revenue'] ?? 0)) ?></strong></article>
    <article class="admin-stat-card"><p>SẢN PHẨM</p><strong><?= e((string) ($stats['total_products'] ?? 0)) ?></strong></article>
    <article class="admin-stat-card"><p>KHÁCH HÀNG</p><strong><?= e((string) ($stats['total_users'] ?? 0)) ?></strong></article>
  </section>
</section>
