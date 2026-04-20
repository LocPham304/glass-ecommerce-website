<?php
$product = $product ?? [];
$defaultVariant = $product['default_variant'] ?? null;
$images = $product['images'] ?? [];
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');
$primaryImage = media_url($product['primary_image'] ?? null);
$relatedProducts = $relatedProducts ?? [];

$colors = [];
$sizes = [];
$variantImages = [];
foreach (($product['variants'] ?? []) as $variant) {
    foreach (parse_option_list($variant['color'] ?? null) as $color) {
        if (!in_array($color, $colors, true)) {
            $colors[] = $color;
        }
    }

    foreach (parse_option_list($variant['size'] ?? null) as $size) {
        if (!in_array($size, $sizes, true)) {
            $sizes[] = $size;
        }
    }
}

$defaultColor = parse_option_list($defaultVariant['color'] ?? null)[0] ?? ($colors[0] ?? '');
$defaultSize = parse_option_list($defaultVariant['size'] ?? null)[0] ?? ($sizes[0] ?? '');

foreach ($images as $image) {
    if (!empty($image['variant_id']) && !isset($variantImages[$image['variant_id']])) {
        $variantImages[$image['variant_id']] = media_url($image['image_url'] ?? null);
    }
}

$variantsForJs = array_map(static function (array $variant) use ($product, $variantImages, $primaryImage): array {
    $image = $variantImages[$variant['id']] ?? $primaryImage;

    return [
        'id' => $variant['id'],
        'sku' => (string) ($variant['sku'] ?? ''),
        'variant_name' => (string) ($variant['variant_name'] ?? ''),
        'color' => (string) ($variant['color'] ?? ''),
        'size' => (string) ($variant['size'] ?? ''),
        'color_options' => parse_option_list($variant['color'] ?? null),
        'size_options' => parse_option_list($variant['size'] ?? null),
        'price' => (float) ($variant['price'] ?? 0),
        'stock_quantity' => (int) ($variant['stock_quantity'] ?? 0),
        'product_type' => (string) ($product['product_type'] ?? ''),
        'image_url' => $image,
    ];
}, $product['variants'] ?? []);
?>

<main class="detail-main">
  <div class="site-container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('/')) ?>">Trang chủ</a>
      <span aria-hidden="true">›</span>
      <a href="<?= e(url('/shop')) ?>">Sản phẩm</a>
      <span aria-hidden="true">›</span>
      <span><?= e($product['name']) ?></span>
    </nav>

    <section class="product-hero">
      <div class="product-gallery">
        <div class="gallery-main">
          <img
            id="main-product-image"
            src="<?= e($primaryImage) ?>"
            alt="<?= e($product['name']) ?>"
            onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';"
          />
          <button class="gallery-nav gallery-nav--prev" type="button" data-gallery-nav="prev" aria-label="Ảnh trước">
            <span aria-hidden="true">&lsaquo;</span>
          </button>
          <button class="gallery-nav gallery-nav--next" type="button" data-gallery-nav="next" aria-label="Ảnh tiếp theo">
            <span aria-hidden="true">&rsaquo;</span>
          </button>
          <button class="gallery-zoom-button" type="button" data-image-zoom-open aria-label="Phóng to ảnh sản phẩm">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M10.5 4a6.5 6.5 0 0 1 5.14 10.48l4.44 4.44-1.42 1.42-4.44-4.44A6.5 6.5 0 1 1 10.5 4Zm0 2a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Zm1 1.5v2h2v2h-2v2h-2v-2h-2v-2h2v-2Z"></path>
            </svg>
          </button>
        </div>
        <div class="gallery-thumbs-shell">
          <button class="gallery-thumbs-nav gallery-thumbs-nav--prev" type="button" data-thumb-nav="prev" aria-label="Nhóm ảnh trước">
            <span aria-hidden="true">&lsaquo;</span>
          </button>
          <div class="gallery-thumbs-viewport">
            <div class="gallery-thumbs" data-gallery-thumbs-track>
          <?php if ($images === []): ?>
            <button class="gallery-thumb is-active" type="button" data-image="<?= e($primaryImage) ?>">
              <img src="<?= e($primaryImage) ?>" alt="<?= e($product['name']) ?>" onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';" />
            </button>
          <?php else: ?>
            <?php foreach ($images as $index => $image): ?>
              <button
                class="gallery-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                type="button"
                data-image="<?= e(media_url($image['image_url'] ?? null)) ?>"
                data-variant-id="<?= e($image['variant_id'] ?? '') ?>"
                aria-label="Ảnh sản phẩm <?= $index + 1 ?>"
              >
                <img src="<?= e(media_url($image['image_url'] ?? null)) ?>" alt="<?= e($product['name']) ?>" onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';" />
              </button>
            <?php endforeach; ?>
          <?php endif; ?>
            </div>
          </div>
          <button class="gallery-thumbs-nav gallery-thumbs-nav--next" type="button" data-thumb-nav="next" aria-label="Nhóm ảnh tiếp theo">
            <span aria-hidden="true">&rsaquo;</span>
          </button>
        </div>
        <div class="image-zoom-modal" data-image-zoom-modal hidden>
          <button class="image-zoom-modal__backdrop" type="button" data-image-zoom-close aria-label="Đóng xem ảnh"></button>
          <div class="image-zoom-modal__dialog" role="dialog" aria-modal="true" aria-label="Xem ảnh sản phẩm phóng to">
            <div class="image-zoom-modal__toolbar">
              <button type="button" data-image-zoom-action="out" aria-label="Thu nhỏ">-</button>
              <button type="button" data-image-zoom-action="reset" aria-label="Đặt lại zoom">100%</button>
              <button type="button" data-image-zoom-action="in" aria-label="Phóng to">+</button>
              <button type="button" data-image-zoom-close aria-label="Đóng">×</button>
            </div>
            <div class="image-zoom-modal__stage">
              <img data-image-zoom-target src="<?= e($primaryImage) ?>" alt="<?= e($product['name']) ?>" />
            </div>
          </div>
        </div>
      </div>

      <div class="product-summary">
        <p class="product-category"><?= e($product['category_name']) ?></p>
        <h1><?= e($product['name']) ?></h1>

        <p class="product-price" id="product-price">
          <?= e(format_currency($defaultVariant['price'] ?? 0)) ?>
        </p>

        <div class="summary-group">
          <p class="summary-label">Thương hiệu: <?= e($product['brand_name']) ?></p>
          <p class="summary-label">Loại đơn phù hợp: <strong id="product-type-label"><?= e(order_type_label($product['product_type'] ?? null)) ?></strong></p>
          <?php if ($colors !== []): ?>
            <div class="color-options">
              <?php foreach ($colors as $index => $color): ?>
                <button
                  class="color-option <?= $color === $defaultColor || ($defaultColor === '' && $index === 0) ? 'is-active' : '' ?>"
                  type="button"
                  style="--color: <?= e(color_to_hex($color)) ?>;"
                  data-color="<?= e($color) ?>"
                  title="<?= e($color) ?>"
                  aria-label="<?= e($color) ?>"
                >
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if ($sizes !== []): ?>
          <div class="summary-group">
            <p class="summary-label">Kích cỡ</p>
            <div class="size-options">
              <?php foreach ($sizes as $index => $size): ?>
                <button class="size-option <?= $size === $defaultSize || ($defaultSize === '' && $index === 0) ? 'is-active' : '' ?>" type="button" data-size="<?= e($size) ?>">
                  <?= e($size) ?>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($defaultVariant !== null): ?>
          <div class="summary-group">
          </div>
        <?php endif; ?>

        <?php if ($defaultVariant !== null): ?>
          <form
            class="purchase-row"
            method="POST"
            action="<?= e(url('/cart/add')) ?>"
            data-variants='<?= e(json_encode($variantsForJs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]') ?>'
          >
            <input type="hidden" name="variant_id" value="<?= e($defaultVariant['id']) ?>" />
            <input type="hidden" name="product_id" value="<?= e($product['id']) ?>" />
            <input type="hidden" name="selected_color" id="selected-color-input" value="<?= e($defaultColor) ?>" />
            <input type="hidden" name="selected_size" id="selected-size-input" value="<?= e($defaultSize) ?>" />
            <input type="hidden" name="quantity" id="quantity-input" value="1" />
            <div class="quantity-picker" aria-label="Số lượng">
              <button type="button" class="quantity-btn" data-qty="decrease">-</button>
              <span id="quantity-value">1</span>
              <button type="button" class="quantity-btn" data-qty="increase">+</button>
            </div>

            <button class="btn-outline-custom" type="submit" id="add-to-cart-button">Thêm vào giỏ hàng</button>
            <a class="btn-primary-custom btn-buy-now" href="<?= e(url('/cart')) ?>">Xem giỏ hàng</a>
          </form>
        <?php endif; ?>
      </div>
    </section>

    <section class="detail-sections">
      <article class="info-card">
          <h2>Thông tin nổi bật</h2>
          <div class="feature-list">
            <p><strong>Loại sản phẩm:</strong> <?= e($product['product_type'] ?: $product['category_name']) ?></p>
            <p><strong>Thương hiệu:</strong> <?= e($product['brand_name']) ?></p>
            <p><strong>Danh mục:</strong> <?= e($product['category_name']) ?></p>
            <?php if ($defaultVariant !== null): ?>
              <p><strong>SKU mặc định:</strong> <?= e($defaultVariant['sku']) ?></p>
              <p><strong>Tồn kho:</strong> <?= e((string) $defaultVariant['stock_quantity']) ?></p>
              <?php if (!empty($defaultVariant['material'])): ?>
                <p><strong>Chất liệu:</strong> <?= e($defaultVariant['material']) ?></p>
              <?php endif; ?>
            <?php endif; ?>
          </div>
      </article>

      <aside class="detail-aside">
        <article class="policy-card">
          <h3>Chính sách bán hàng</h3>
          <div class="policy-list">
            <div class="policy-item">
              <img src="<?= e(asset('assets/images/product-detail/icon-delivery.png')) ?>" alt="" aria-hidden="true" />
              <div>
                <p class="policy-item__title">Giao hàng miễn phí</p>
                <p class="policy-item__text">Cho đơn hàng từ 500.000đ</p>
              </div>
            </div>
            <div class="policy-item">
              <img src="<?= e(asset('assets/images/product-detail/icon-warranty.png')) ?>" alt="" aria-hidden="true" />
              <div>
                <p class="policy-item__title">Bảo hành 12 tháng</p>
                <p class="policy-item__text">Lỗi từ nhà sản xuất</p>
              </div>
            </div>
            <div class="policy-item">
              <img src="<?= e(asset('assets/images/product-detail/icon-return.png')) ?>" alt="" aria-hidden="true" />
              <div>
                <p class="policy-item__title">Đổi trả 7 ngày</p>
                <p class="policy-item__text">Nếu không vừa ý</p>
              </div>
            </div>
          </div>
        </article>
      </aside>

      <article class="info-card product-description-card">
        <h2>Mô tả sản phẩm</h2>
        <p class="product-description-text">
          <?= e($product['description'] ?: 'Sản phẩm hiện chưa có mô tả chi tiết. Bạn có thể bổ sung ở bảng products.') ?>
        </p>
      </article>
    </section>

    <section class="related-section">
      <div class="section-heading">
        <div>
          <h2>Sản phẩm liên quan</h2>
        </div>
      </div>
      <div class="related-grid" id="related-grid">
        <?php if ($relatedProducts === []): ?>
          <div class="alert alert-light border rounded-4 mb-0">
            Chưa có sản phẩm liên quan trong cùng danh mục.
          </div>
        <?php else: ?>
          <?php foreach ($relatedProducts as $relatedProduct): ?>
            <?php
              $relatedProductUrl = url('/product') . '?id=' . urlencode($relatedProduct['id']);
              $relatedVariantId = (string) ($relatedProduct['default_variant_id'] ?? '');
            ?>
            <article class="related-card">
              <a
                class="related-card__detail-link"
                href="<?= e($relatedProductUrl) ?>"
                aria-label="Xem chi tiết <?= e($relatedProduct['name']) ?>"
              ></a>
              <a
                class="related-card__media"
                href="<?= e($relatedProductUrl) ?>"
              >
                <img
                  src="<?= e(media_url($relatedProduct['image_url'] ?? null)) ?>"
                  alt="<?= e($relatedProduct['name']) ?>"
                  onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';"
                />
              </a>
              <div class="related-card__body">
                <h3 class="related-card__name">
                  <a href="<?= e($relatedProductUrl) ?>">
                    <?= e($relatedProduct['name']) ?>
                  </a>
                </h3>
                <div class="related-card__footer">
                  <p class="related-card__price"><?= e(format_currency($relatedProduct['price'])) ?></p>
                  <?php if ($relatedVariantId !== ''): ?>
                    <form class="related-card__add-form" method="POST" action="<?= e(url('/cart/add')) ?>">
                      <input type="hidden" name="variant_id" value="<?= e($relatedVariantId) ?>" />
                      <input type="hidden" name="product_id" value="<?= e($relatedProduct['id']) ?>" />
                      <input type="hidden" name="quantity" value="1" />
                      <input type="hidden" name="redirect_to" value="<?= e('/product?id=' . urlencode($product['id'])) ?>" />
                      <button
                        class="product-card__add"
                        type="submit"
                        aria-label="Thêm <?= e($relatedProduct['name']) ?> vào giỏ"
                      >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                          <path d="M11 5h2v14h-2zM5 11h14v2H5z"></path>
                        </svg>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>