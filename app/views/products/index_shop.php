<?php
$products = $products ?? [];
$categories = $categories ?? [];
$filters = $filters ?? [];
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');

$categoryLabels = [
    'gong-kinh' => 'Gọng kính',
    'trong-kinh' => 'Tròng kính',
    'kinh-ram' => 'Kính râm',
    'phu-kien' => 'Phụ kiện',
];

$typeLabels = [
    'ready_stock' => 'Đơn có sẵn',
    'pre_order' => 'Pre-order',
    'prescription' => 'Đơn cắt kính theo toa',
];
?>

<main class="shop-main">
  <section class="shop-hero">
    <div class="site-container">
      <div class="breadcrumb">
        <a href="<?= e(url('/')) ?>">Trang chủ</a>
        <span aria-hidden="true">›</span>
        <span>Danh mục sản phẩm</span>
      </div>
      <div class="shop-hero__header">
        <h1 class="shop-title">Tất cả sản phẩm</h1>
        <p id="results-text">Hiển thị <?= count($products) ?> sản phẩm</p>
      </div>
    </div>
  </section>

  <section class="shop-layout">
    <div class="site-container">
      <div class="shop-layout__grid">
        <form class="shop-sidebar" method="GET" action="<?= e(url('/shop')) ?>">
          <div class="filter-group">
            <h2>Từ khóa</h2>
            <input
              class="form-control"
              type="search"
              name="keyword"
              placeholder="Tên sản phẩm hoặc thương hiệu..."
              value="<?= e($filters['keyword'] ?? '') ?>"
            />
          </div>

          <div class="filter-group">
            <h2>Danh mục</h2>
            <select class="form-select" name="category">
              <option value="">Tất cả danh mục</option>
              <?php foreach ($categories as $category): ?>
                <option
                  value="<?= e($category['id']) ?>"
                  <?= ($filters['category_id'] ?? '') === $category['id'] ? 'selected' : '' ?>
                >
                  <?= e($categoryLabels[$category['slug'] ?? ''] ?? ($category['name'] ?? 'Danh mục')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-group">
            <h2>Sắp xếp</h2>
            <select class="form-select" name="sort">
              <option value="latest" <?= ($filters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
              <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
              <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
              <option value="name_asc" <?= ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-dark flex-fill" type="submit">Áp dụng</button>
            <a class="btn btn-outline-secondary flex-fill" href="<?= e(url('/shop')) ?>">Đặt lại</a>
          </div>
        </form>

        <div class="shop-content">
          <div class="shop-toolbar">
            <div>
              <strong><?= count($products) ?></strong> sản phẩm phù hợp
            </div>
          </div>

          <?php if ($products === []): ?>
            <div class="alert alert-light border">
              Không tìm thấy sản phẩm nào.
            </div>
          <?php else: ?>
            <div class="shop-products-grid" id="shop-products-grid">
              <?php foreach ($products as $product): ?>
                <?php
                $categoryLabel = $categoryLabels[$product['category_slug'] ?? ''] ?? 'Sản phẩm';
                $typeLabel = $typeLabels[$product['product_type'] ?? ''] ?? 'Sản phẩm';
                $productDetailUrl = url('/product') . '?id=' . urlencode($product['id']);
                $defaultVariantId = (string) ($product['default_variant_id'] ?? '');
                $currentPrice = (float) ($product['price'] ?? 0);
                $originalPrice = (float) ($product['original_price'] ?? 0);
                $hasSalePrice = $originalPrice > $currentPrice;
                ?>
                <article class="shop-product-card">
                  <a
                    class="shop-product-card__detail-link"
                    href="<?= e($productDetailUrl) ?>"
                    aria-label="Xem chi tiết <?= e($product['name']) ?>"
                  ></a>
                  <a
                    class="shop-product-card__media"
                    href="<?= e($productDetailUrl) ?>"
                  >
                    <img
                      src="<?= e(media_url($product['image_url'] ?? null)) ?>"
                      alt="<?= e($product['name']) ?>"
                      onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';"
                    />
                  </a>

                  <div class="shop-product-card__body">
                    <p class="mb-1 text-secondary small">
                      <?= e($categoryLabel) ?>
                    </p>
                    <h3 class="shop-product-card__name">
                      <a href="<?= e($productDetailUrl) ?>">
                        <?= e($product['name']) ?>
                      </a>
                    </h3>
                    <div class="shop-product-card__footer">
                      <div class="shop-product-card__price-group">
                        <p class="shop-product-card__price"><?= e(format_currency($currentPrice)) ?></p>
                        <?php if ($hasSalePrice): ?>
                          <p class="shop-product-card__price-original"><?= e(format_currency($originalPrice)) ?></p>
                        <?php endif; ?>
                      </div>
                      <?php if ($defaultVariantId !== ''): ?>
                        <form class="shop-product-card__add-form" method="POST" action="<?= e(url('/cart/add')) ?>">
                          <input type="hidden" name="variant_id" value="<?= e($defaultVariantId) ?>" />
                          <input type="hidden" name="product_id" value="<?= e($product['id']) ?>" />
                          <input type="hidden" name="quantity" value="1" />
                          <input type="hidden" name="redirect_to" value="/shop" />
                          <button
                            class="product-card__add"
                            type="submit"
                            aria-label="Thêm <?= e($product['name']) ?> vào giỏ"
                          >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                              <path d="M11 5h2v14h-2zM5 11h14v2H5z"></path>
                            </svg>
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                    <!-- <?php if (!empty($product['size'])): ?>
                      <p class="mb-0 text-secondary small">Size: <?= e($product['size']) ?></p>
                    <?php endif; ?> -->
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</main>
