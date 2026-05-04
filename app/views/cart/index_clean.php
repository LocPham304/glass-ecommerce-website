<?php
$cartSummary = $cartSummary ?? ['items' => [], 'subtotal' => 0, 'count' => 0];
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');
$itemCount = count($cartSummary['items'] ?? []);
?>

<main class="cart-main">
  <div class="site-container">
    <section class="cart-layout">
      <div class="cart-page">
      <div class="cart-header">
        <h1>Giỏ hàng của bạn</h1>
        <p><?= e((string) $itemCount) ?> sản phẩm</p>
      </div>

      <?php if (($cartSummary['items'] ?? []) === []): ?>
        <div class="cart-empty">
          <p>Giỏ hàng của bạn đang trống.</p>
          <a class="checkout-button cart-empty__action" href="<?= e(url('/shop')) ?>">Mua sắm ngay</a>
        </div>
      <?php else: ?>
        <div class="cart-stack">
          <?php foreach ($cartSummary['items'] as $item): ?>
            <?php
            $decreaseQuantity = max(1, (int) $item['quantity'] - 1);
            $increaseQuantity = (int) $item['quantity'] + 1;
            ?>
            <article class="cart-line">
              <a
                class="cart-line__media"
                href="<?= e(url('/product') . '?id=' . urlencode($item['product_id'])) ?>"
              >
                <img
                  src="<?= e(media_url($item['image_url'] ?? null)) ?>"
                  alt="<?= e($item['product_name']) ?>"
                  onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';"
                />
              </a>

              <div class="cart-line__body">
                <h2 class="cart-line__title">
                  <a href="<?= e(url('/product') . '?id=' . urlencode($item['product_id'])) ?>">
                    <?= e($item['product_name']) ?>
                  </a>
                </h2>
                <p class="cart-line__brand"><?= e($item['brand_name']) ?></p>
                <p class="cart-line__meta">
                  SKU: <?= e($item['sku']) ?>
                  <?php if (!empty($item['selected_color'])): ?>
                    · Màu: <?= e($item['selected_color']) ?>
                  <?php endif; ?>
                  <?php if (!empty($item['selected_size'])): ?>
                    · Size: <?= e($item['selected_size']) ?>
                  <?php endif; ?>
                </p>
              </div>

              <div class="cart-line__actions">
                <div class="cart-line__qty" aria-label="Điều chỉnh số lượng">
                  <form method="POST" action="<?= e(url('/cart/update')) ?>">
                    <input type="hidden" name="cart_item_id" value="<?= e($item['id']) ?>" />
                    <input type="hidden" name="quantity" value="<?= e((string) $decreaseQuantity) ?>" />
                    <button class="cart-line__qty-button" type="submit" aria-label="Giảm số lượng">-</button>
                  </form>

                  <span class="cart-line__qty-value"><?= e((string) $item['quantity']) ?></span>

                  <form method="POST" action="<?= e(url('/cart/update')) ?>">
                    <input type="hidden" name="cart_item_id" value="<?= e($item['id']) ?>" />
                    <input type="hidden" name="quantity" value="<?= e((string) $increaseQuantity) ?>" />
                    <button class="cart-line__qty-button" type="submit" aria-label="Tăng số lượng">+</button>
                  </form>
                </div>

                <strong class="cart-line__price"><?= e(format_currency($item['line_total'])) ?></strong>

                <form method="POST" action="<?= e(url('/cart/remove')) ?>">
                  <input type="hidden" name="cart_item_id" value="<?= e($item['id']) ?>" />
                  <button class="cart-line__remove" type="submit" aria-label="Xóa sản phẩm">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M7 21a2 2 0 0 1-2-2V7h14v12a2 2 0 0 1-2 2Zm10-12H7v10h10ZM9 4h6l1 1h4v2H4V5h4Z"></path>
                    </svg>
                  </button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <a class="continue-link" href="<?= e(url('/shop')) ?>">← Tiếp tục mua sắm</a>
      <?php endif; ?>

      </div>

      <aside class="cart-summary">
        <h2>TÓM TẮT ĐƠN HÀNG</h2>
        <div class="summary-row">
          <span>Tạm tính</span>
          <strong><?= e(format_currency($cartSummary['subtotal'])) ?></strong>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-total">
          <div>
            <p>Tổng cộng</p>
            <span>(Đã bao gồm VAT nếu có)</span>
          </div>
          <strong><?= e(format_currency($cartSummary['subtotal'])) ?></strong>
        </div>
        <a class="checkout-button text-center" href="<?= e(url('/checkout')) ?>">Tiến hành thanh toán</a>
        <div class="payment-icons" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm2 0v2h14V7Zm0 6v4h14v-4Z"></path></svg>
          <svg viewBox="0 0 24 24"><path d="M3 10h18v2H3Zm2 4h2v5H5Zm4 0h2v5H9Zm4 0h2v5h-2Zm4 0h2v5h-2ZM12 3l9 4v1H3V7Z"></path></svg>
        </div>
      </aside>
    </section>
  </div>
</main>