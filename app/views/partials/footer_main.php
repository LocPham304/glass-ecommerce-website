<?php $footerCategories = $navigationCategories ?? []; ?>
<?php $returnLink = url('/after-sales'); ?>
<?php
$categoryLabels = [
  'gong-kinh' => 'Gọng kính',
  'trong-kinh' => 'Tròng kính',
  'kinh-ram' => 'Kính râm',
  'phu-kien' => 'Phụ kiện',
];
?>

<footer class="site-footer" id="footer">
  <div class="site-container footer-top">
    <div class="footer-brand">
      <a class="brand brand--footer" href="<?= e(url('/')) ?>" aria-label="ClearVision">
        <img
          class="brand__mark"
          src="<?= e(asset('assets/images/common/logo.png')) ?>"
          alt="ClearVision logo"
        />
        <span class="brand__text">
          <span class="brand__text-main">Clear</span>
          <span class="brand__text-accent">Vision</span>
        </span>
      </a>
      <p>
        Thương hiệu kính mắt trực tuyến giúp khách hàng dễ dàng chọn gọng kính,
        tròng kính và đặt mua mọi lúc mọi nơi.
      </p>
    </div>

    <div class="footer-column">
      <h3>Sản phẩm</h3>
      <a href="<?= e(url('/shop')) ?>">Tất cả sản phẩm</a>
      <?php foreach (array_slice($footerCategories, 0, 4) as $category): ?>
        <a href="<?= e(url('/shop') . '?category=' . urlencode($category['id'])) ?>">
          <?= e($categoryLabels[$category['slug'] ?? ''] ?? ($category['name'] ?? 'Sản phẩm')) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="footer-column">
      <h3>Hỗ trợ</h3>
      <a href="<?= e(url('/about')) ?>">Về chúng tôi</a>
      <a href="<?= e(url('/warranty')) ?>">Chính sách bảo hành</a>
      <a href="<?= e($returnLink) ?>">Đổi trả &amp; Hoàn tiền</a>
      <a href="<?= e(url('/shop')) ?>">Danh mục sản phẩm</a>
    </div>

    <div class="footer-column footer-column--contact">
      <h3>Liên hệ</h3>
      <p>123 Đường ABC, Phường Sài Gòn, TP. Hồ Chí Minh</p>
      <p>1900 1234</p>
      <p>contact@clearvision.vn</p>
    </div>
  </div>

  <div class="site-container footer-bottom">
    <p>&copy; 2026 ClearVision. Tất cả các quyền được bảo lưu.</p>
    <div class="footer-bottom__links">
      <a href="<?= e(url('/warranty')) ?>">Điều khoản dịch vụ</a>
      <a href="<?= e($returnLink) ?>">Đổi trả &amp; Hoàn tiền</a>
    </div>
  </div>
</footer>
