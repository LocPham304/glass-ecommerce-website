<?php
$cartSummary = $cartSummary ?? ['items' => [], 'subtotal' => 0];
$defaultAddress = $defaultAddress ?? [];
$user = $user ?? [];
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');
$recommendedOrderType = $cartSummary['recommended_order_type'] ?? 'ready_stock';
$isMixedCart = count($cartSummary['product_types'] ?? []) > 1;
?>

<main class="checkout-main">
  <div class="site-container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('/cart')) ?>">Giỏ hàng</a>
      <span aria-hidden="true">›</span>
      <span>Thanh toán</span>
    </nav>

    <form class="checkout-layout" method="POST" action="<?= e(url('/checkout')) ?>" enctype="multipart/form-data">
      <input type="hidden" name="order_type" value="<?= e($recommendedOrderType ?: 'ready_stock') ?>" />
      <div class="checkout-form-area">
        <section class="form-card">
          <h2>THÔNG TIN KHÁCH HÀNG</h2>
          <div class="field-grid">
            <label class="field-row">
              <span>Họ và tên <em>*</em></span>
              <input type="text" name="receiver_name" value="<?= e($defaultAddress['receiver_name'] ?? $user['full_name'] ?? '') ?>" required />
            </label>
            <label class="field-row">
              <span>Số điện thoại <em>*</em></span>
              <input type="tel" name="receiver_phone" value="<?= e($defaultAddress['receiver_phone'] ?? $user['phone'] ?? '') ?>" required />
            </label>
            <label class="field-row">
              <span>Email</span>
              <input type="email" value="<?= e($user['email'] ?? '') ?>" disabled />
            </label>
          </div>
        </section>

        <section class="form-card">
          <h2>ĐỊA CHỈ GIAO HÀNG</h2>
          <div class="field-grid">
            <label class="field-row">
              <span>Tỉnh / Thành phố <em>*</em></span>
              <select
                name="province"
                data-address-province
                data-selected="<?= e($defaultAddress['province'] ?? '') ?>"
                required
              >
                <option value="">Đang tải dữ liệu...</option>
              </select>
            </label>
            <input type="hidden" name="district" value="" data-address-district />
            <label class="field-row">
              <span>Phường / Xã <em>*</em></span>
              <select
                name="ward"
                data-address-ward
                data-selected="<?= e($defaultAddress['ward'] ?? '') ?>"
                required
                disabled
              >
                <option value="">Vui lòng chọn tỉnh / thành phố trước</option>
              </select>
            </label>
            <label class="field-row">
              <span>Địa chỉ <em>*</em></span>
              <input type="text" name="address_line" value="<?= e($defaultAddress['address_line'] ?? '') ?>" required />
            </label>
            <label class="field-row field-row--full">
              <span>Ghi chú</span>
              <textarea rows="4" name="note"></textarea>
            </label>
          </div>
        </section>

        <section class="form-card form-card--note">
          <h2>Phiếu khám mắt</h2>
          <?php if ($isMixedCart): ?>
            <div class="alert alert-warning">
              Giỏ hàng hiện đang chứa nhiều loại sản phẩm khác nhau. Hệ thống chỉ hỗ trợ một loại đơn cho mỗi lần checkout,
              vui lòng tách giỏ hàng trước khi đặt.
            </div>
          <?php endif; ?>
          <p>
            Phiếu khám mắt được cung cấp bởi kỹ thuật viên đo khúc xạ. Bao gồm các thông số quan trọng như số độ cận,
            viễn loạn, số đo PD... giúp chúng tôi cắt kính chính xác và thoải mái nhất cho đôi mắt của bạn.
          </p>
          <input
            id="prescription-image"
            class="visually-hidden"
            type="file"
            name="prescription_image"
            accept=".jpg,.jpeg,.png,.webp"
            data-prescription-input
          />
          <button class="upload-button" type="button" data-prescription-trigger>
            Upload ảnh chụp phiếu khám mắt
          </button>
          <p class="upload-feedback" data-prescription-filename>Chưa chọn tệp nào.</p>
        </section>
      </div>

      <aside class="checkout-summary">
        <h2>ĐƠN HÀNG CỦA BẠN</h2>
        <div class="summary-items">
          <?php foreach ($cartSummary['items'] as $item): ?>
            <article class="summary-item">
              <img src="<?= e(media_url($item['image_url'] ?? null)) ?>" alt="<?= e($item['product_name']) ?>" onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';" />
              <div class="summary-item__content">
                <h3><?= e($item['product_name']) ?></h3>
                <p><?= e(order_type_label($item['product_type'] ?? null)) ?><?= !empty($item['selected_color']) ? ' · Màu: ' . e($item['selected_color']) : '' ?><?= !empty($item['selected_size']) ? ' · Size: ' . e($item['selected_size']) : '' ?></p>
                <span>SL: <?= e((string) $item['quantity']) ?></span>
              </div>
              <strong><?= e(format_currency($item['line_total'])) ?></strong>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="coupon-box">
          <label for="voucher-code">Nhập mã giảm giá</label>
          <div class="coupon-box__row">
            <input id="voucher-code" type="text" name="voucher_code" placeholder="Mã giảm giá..." data-voucher-code />
            <button type="button" data-apply-voucher>Áp dụng</button>
          </div>
          <p class="coupon-feedback" data-voucher-feedback hidden></p>
        </div>

        <div class="summary-pricing">
          <div><span>Tạm tính</span><strong data-summary-subtotal><?= e(format_currency($cartSummary['subtotal'])) ?></strong></div>
          <div><span>Phí vận chuyển</span><strong>Miễn phí</strong></div>
          <div class="discount-row" data-summary-discount-row hidden><span>Giảm giá</span><strong data-summary-discount>-0đ</strong></div>
        </div>

        <div class="summary-total">
          <span>Tổng cộng</span>
          <strong data-summary-total><?= e(format_currency($cartSummary['subtotal'])) ?></strong>
        </div>

        <div class="payment-methods">
          <p>PHƯƠNG THỨC THANH TOÁN</p>
          <label class="payment-option is-active"><input type="radio" name="payment_method" value="cod" checked /><span class="payment-option__content">Thanh toán khi nhận hàng (COD)</span></label>
        </div>

        <button
          class="place-order-button"
          type="submit"
          data-checkout-subtotal="<?= e((string) $cartSummary['subtotal']) ?>"
          data-voucher-preview-url="<?= e(url('/checkout/voucher-preview')) ?>"
          <?= $isMixedCart ? 'disabled' : '' ?>
        >
          ĐẶT HÀNG NGAY
        </button>
      </aside>
    </form>
  </div>
</main>