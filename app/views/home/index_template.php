<?php
$featuredProducts = $featuredProducts ?? [];
$productImageFallback = asset('assets/images/about-us/eyewear-display.png');
?>

<main>
  <section class="hero-section" id="hero">
    <div class="hero-section__image">
      <img
        src="<?= e(asset('assets/images/homepage/supercombo.jpeg')) ?>"
        alt="Người mẫu đeo kính thời trang"
      />
    </div>
    <div class="site-container hero-section__content">
      <div class="hero-copy">
        <p class="eyebrow">Mùa hè 2024</p>
        <h1>Tìm Cặp Kính<br />Hoàn Hảo Của Bạn</h1>
        <p class="hero-description">
          Khám phá bộ sưu tập gọng kính, kính râm và các lựa chọn phù hợp cho nhu cầu
          mua có sẵn, pre-order hoặc cắt kính theo toa.
        </p>
        <a class="btn-primary-custom" href="#featured-products">Mua Ngay</a>
      </div>
    </div>
  </section>

  <section class="categories-section">
    <div class="site-container">
      <div class="categories-grid">
        <article class="category-card">
          <img
            src="<?= e(asset('assets/images/homepage/gongkinh.jpg')) ?>"
            alt="Gọng kính"
          />
          <div class="category-card__overlay">
            <h3>Gọng kính</h3>
            <a href="<?= e(url('/shop')) ?>">
              <span>Khám phá</span>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M13.17 5.59L11.76 7l4 4H4v2h11.76l-4 4l1.41 1.41L19.59 12z"></path>
              </svg>
            </a>
          </div>
        </article>

        <article class="category-card">
          <img
            src="<?= e(asset('assets/images/homepage/kinhram.jpg')) ?>"
            alt="Kính râm"
          />
          <div class="category-card__overlay">
            <h3>Kính râm</h3>
            <a href="<?= e(url('/shop')) ?>">
              <span>Khám phá</span>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M13.17 5.59L11.76 7l4 4H4v2h11.76l-4 4l1.41 1.41L19.59 12z"></path>
              </svg>
            </a>
          </div>
        </article>

        <article class="category-card">
          <img
            src="<?= e(asset('assets/images/homepage/trongkinh.jfif')) ?>"
            alt="Kính áp tròng"
          />
          <div class="category-card__overlay">
            <h3>Tròng kính</h3>
            <a href="<?= e(url('/shop')) ?>">
              <span>Khám phá</span>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M13.17 5.59L11.76 7l4 4H4v2h11.76l-4 4l1.41 1.41L19.59 12z"></path>
              </svg>
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="featured-products-section" id="featured-products">
    <div class="site-container">
      <div class="section-heading">
        <h2>Sản phẩm Bán chạy</h2>
        <a class="section-link" href="<?= e(url('/shop')) ?>">Xem tất cả</a>
      </div>

      <?php if ($featuredProducts === []): ?>
        <div class="alert alert-light border">
          Chưa có dữ liệu sản phẩm trong database. Bạn hãy thêm categories, brands,
          products, product_variants và product_images để homepage hiển thị đầy đủ.
        </div>
      <?php else: ?>
        <div class="products-grid" id="products-grid">
          <?php foreach ($featuredProducts as $product): ?>
            <?php
              $productDetailUrl = url('/product') . '?id=' . urlencode($product['id']);
              $defaultVariantId = (string) ($product['default_variant_id'] ?? '');
              $currentPrice = (float) ($product['price'] ?? 0);
              $originalPrice = (float) ($product['original_price'] ?? 0);
              $hasSalePrice = $originalPrice > $currentPrice;
            ?>
            <article class="product-card">
              <a
                class="product-card__detail-link"
                href="<?= e($productDetailUrl) ?>"
                aria-label="Xem chi tiết <?= e($product['name']) ?>"
              ></a>
              <div class="product-card__media">
                <div class="product-card__media-inner">
                  <img
                    src="<?= e(media_url($product['image_url'] ?? null)) ?>"
                    alt="<?= e($product['name']) ?>"
                    onerror="this.onerror=null;this.src='<?= e($productImageFallback) ?>';"
                  />
                </div>
              </div>

              <div class="product-card__body">
                <h3 class="product-card__name"><?= e($product['name']) ?></h3>
               
                <div class="product-card__footer">
                  <div class="product-card__price-group">
                    <p class="product-card__price"><?= e(format_currency($currentPrice)) ?></p>
                    <?php if ($hasSalePrice): ?>
                      <p class="product-card__price-original"><?= e(format_currency($originalPrice)) ?></p>
                    <?php endif; ?>
                  </div>
                  <?php if ($defaultVariantId !== ''): ?>
                    <form class="product-card__add-form" method="POST" action="<?= e(url('/cart/add')) ?>">
                      <input type="hidden" name="variant_id" value="<?= e($defaultVariantId) ?>" />
                      <input type="hidden" name="product_id" value="<?= e($product['id']) ?>" />
                      <input type="hidden" name="quantity" value="1" />
                      <input type="hidden" name="redirect_to" value="/#featured-products" />
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
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="promotion-section">
    <div class="site-container">
      <div class="promotion-banner">
        <div class="promotion-copy">
          <h2>Giảm 20% Cho Đơn Hàng Đầu Tiên</h2>
          <p>Nâng cấp phong cách của bạn với bộ sưu tập mới và ưu đãi áp dụng ngay khi checkout.</p>
        </div>
        <div class="promotion-code">
          <div class="promotion-code__pill">WELCOME20</div>
          <p>Áp dụng khi thanh toán. Thời gian có hạn.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="best-deals-section" id="best-deals">
    <div class="site-container best-deals-grid">
      <div class="best-deals-copy">
        <p class="eyebrow">ƯU ĐÃI CÓ HẠN</p>
        <h2>Ưu đãi Mua 2 Tặng 1</h2>
        <p class="best-deals-description">
          Homepage này đã được đưa về đúng nhịp bố cục template gốc.
          Bạn có thể thay toàn bộ ảnh và nội dung khuyến mãi thật sau mà không cần đổi layout.
        </p>
        <ul class="benefit-list">
          <li>
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M9.55 17.6 4.95 13l1.41-1.41 3.19 3.19 8.09-8.09L19.05 8z"></path>
            </svg>
            Bao gồm lớp phủ chống chói
          </li>
          <li>
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M9.55 17.6 4.95 13l1.41-1.41 3.19 3.19 8.09-8.09L19.05 8z"></path>
            </svg>
            Tròng kính chống trầy xước cao cấp
          </li>
        </ul>
        <a class="btn-secondary-custom" href="<?= e(url('/shop')) ?>">Mua Ngay</a>
      </div>

      <div class="best-deals-visual">
        <img
          src="<?= e(asset('assets/images/homepage/BundleOffer.png')) ?>"
          alt="Ưu đãi mua 2 tặng 1"
        />
      </div>
    </div>
  </section>

  <section class="reviews-section" id="customer-reviews">
    <div class="site-container">
      <div class="section-heading">
        <h2>Được tin dùng bởi hơn 50,000 khách hàng</h2>
      </div>
      <div class="reviews-grid" id="reviews-grid">
        <article class="review-card">
          <p class="review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</p>
          <p class="review-card__quote">
            "Mình có thể xem hàng có sẵn, thêm vào giỏ và kiểm tra trạng thái đơn ngay trên tài khoản
            mà không cần nhắn tin thủ công."
          </p>
          <div class="review-card__author">
            <span class="review-card__avatar" aria-hidden="true"></span>
            <div>
              <p class="review-card__name">Khách hàng online</p>
              <p class="review-card__meta">Mua kính có sẵn</p>
            </div>
          </div>
        </article>

        <article class="review-card">
          <p class="review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</p>
          <p class="review-card__quote">
            "Phần pre-order hiển thị rõ loại đơn và ngày hàng về dự kiến nên dễ theo dõi hơn rất nhiều."
          </p>
          <div class="review-card__author">
            <span class="review-card__avatar" aria-hidden="true"></span>
            <div>
              <p class="review-card__name">Nhóm khách chờ hàng</p>
              <p class="review-card__meta">Pre-order</p>
            </div>
          </div>
        </article>

        <article class="review-card">
          <p class="review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</p>
          <p class="review-card__quote">
            "Đơn cắt kính theo toa có đủ chỗ nhập thông số mắt và đội ngũ cửa hàng vẫn theo dõi
            được tiến trình xử lý ở phía quản trị."
          </p>
          <div class="review-card__author">
            <span class="review-card__avatar" aria-hidden="true"></span>
            <div>
              <p class="review-card__name">Khách cắt kính</p>
              <p class="review-card__meta">Prescription order</p>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>
