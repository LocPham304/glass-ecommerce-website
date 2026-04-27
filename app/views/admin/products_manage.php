<?php
$productCount = count($products);
$fallbackImage = asset('assets/images/about-us/eyewear-display.png');
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Quản lý sản phẩm</h1>
  </div>

  <label class="admin-products-search" aria-label="Tìm kiếm nhanh">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input
      type="search"
      placeholder="Tìm kiếm nhanh..."
      data-product-search
    />
  </label>

  <a
    class="admin-primary-button admin-primary-button--topbar"
    href="<?= e(url('/admin/products/create')) ?>"
  >
    <i class="fa-solid fa-plus" aria-hidden="true"></i>
    Thêm sản phẩm
  </a>
</header>

<section class="admin-content admin-content--products">
  <section class="admin-panel product-filter-panel">
    <div class="product-filter-grid">
      <label class="product-filter-field">
        <span>TÌM KIẾM TÊN/SKU</span>
        <input
          type="search"
          placeholder="Ví dụ: RB3016..."
          data-filter-name
        />
      </label>

      <label class="product-filter-field">
        <span>DANH MỤC</span>
        <select data-filter-category>
          <option value="all">Tất cả danh mục</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= e($category['name']) ?>"><?= e($category['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="product-filter-field">
        <span>TRẠNG THÁI</span>
        <select data-filter-status>
          <option value="all">Tất cả trạng thái</option>
          <option value="Đang bán">Đang bán</option>
          <option value="Ngừng bán">Ngừng bán</option>
        </select>
      </label>

      <label class="product-filter-field">
        <span>KHO HÀNG</span>
        <select data-filter-stock>
          <option value="all">Tất cả tồn kho</option>
          <option value="in">Còn hàng</option>
          <option value="low">Sắp hết</option>
          <option value="out">Hết hàng</option>
        </select>
      </label>
    </div>

    <div class="product-toolbar">
      <p class="product-toolbar__summary" data-product-summary>
        Hiển thị 1 - <?= e((string) $productCount) ?> trong số <?= e((string) $productCount) ?> sản phẩm
      </p>
    </div>
  </section>

  <section class="admin-panel product-table-panel">
    <?php if ($products === []): ?>
      <div class="product-empty-state">
        <i class="fa-solid fa-box-open" aria-hidden="true"></i>
        <h2>Chưa có sản phẩm nào</h2>
        <p>Danh sách sẽ hiện ở đây ngay khi hệ thống có dữ liệu sản phẩm.</p>
      </div>
    <?php else: ?>
      <div class="product-table" role="table" aria-label="Danh sách sản phẩm">
        <div class="product-table__head" role="row">
          <span class="check-cell">
            <input
              type="checkbox"
              data-check-all
              aria-label="Chọn tất cả"
            />
          </span>
          <span>HÌNH ẢNH</span>
          <span>TÊN SẢN PHẨM / SKU</span>
          <span>DANH MỤC</span>
          <span>GIÁ BÁN</span>
          <span>TRẠNG THÁI</span>
          <span>HÀNH ĐỘNG</span>
        </div>

        <div class="product-table__body" data-product-table>
          <?php foreach ($products as $index => $product): ?>
            <?php
            $isActive = (int) ($product['is_active'] ?? 0) === 1 || $product['is_active'] === null;
            $statusLabel = $isActive ? 'Đang bán' : 'Ngừng bán';
            $statusClass = $isActive ? 'status-pill--active' : 'status-pill--muted';
            $stockQuantity = (int) ($product['stock_quantity'] ?? 0);
            $stockState = $stockQuantity <= 0 ? 'out' : ($stockQuantity <= 5 ? 'low' : 'in');
            $categorySlug = (string) ($product['category_slug'] ?? '');
            $chipClass = 'product-chip--sky';

            if (str_contains($categorySlug, 'combo')) {
                $chipClass = 'product-chip--amber';
            } elseif (str_contains($categorySlug, 'gong') || str_contains($categorySlug, 'frame')) {
                $chipClass = 'product-chip--violet';
            }

            $imageUrl = media_url($product['image_url'] ?? null);
            $productUrl = url('/product?id=' . rawurlencode((string) $product['id']));
            $productEditUrl = url('/admin/products/edit?id=' . rawurlencode((string) $product['id']));
            $productName = (string) ($product['name'] ?? 'Sáº£n pháº©m');
            ?>
            <article
              class="product-row"
              data-name="<?= e($productName) ?>"
              data-sku="<?= e((string) ($product['sku'] ?? '')) ?>"
              data-category="<?= e((string) ($product['category_name'] ?? '')) ?>"
              data-status="<?= e($statusLabel) ?>"
              data-stock="<?= e($stockState) ?>"
              data-edit-url="<?= e($productEditUrl) ?>"
              data-product-name="<?= e($productName) ?>"
            >
              <div class="check-cell">
                <input type="checkbox" aria-label="Chọn sản phẩm <?= e((string) ($index + 1)) ?>" />
              </div>
              <div class="product-thumb">
                <img
                  src="<?= e($imageUrl) ?>"
                  alt="<?= e((string) ($product['name'] ?? 'Sản phẩm')) ?>"
                  onerror="this.onerror=null;this.src='<?= e($fallbackImage) ?>';"
                />
              </div>
              <div class="product-info">
                <h2><?= e((string) ($product['name'] ?? 'Sản phẩm')) ?></h2>
                <p class="product-meta">
                  SKU: <?= e((string) (($product['sku'] ?? '') !== '' ? $product['sku'] : '-')) ?>
                  <span class="product-meta__divider">|</span>
                  Tồn kho: <?= e((string) $stockQuantity) ?>
                </p>
              </div>
              <div>
                <span class="product-chip <?= e($chipClass) ?>"><?= e((string) ($product['category_name'] ?? '-')) ?></span>
              </div>
              <div class="product-price"><?= e(format_currency($product['price'] ?? 0)) ?></div>
              <div>
                <span class="status-pill <?= e($statusClass) ?>"><?= e($statusLabel) ?></span>
              </div>
              <div class="product-actions">
                <button type="button" aria-label="Sửa" data-coming-soon="Chỉnh sửa sản phẩm">
                  <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </button>
                <a href="<?= e($productUrl) ?>" aria-label="Xem chi tiết">
                  <i class="fa-regular fa-eye" aria-hidden="true"></i>
                </a>
                <form
                  method="POST"
                  action="<?= e(url('/admin/products/delete')) ?>"
                  class="product-delete-form"
                  data-delete-product-form
                  data-product-name="<?= e($productName) ?>"
                >
                  <input type="hidden" name="product_id" value="<?= e((string) ($product['id'] ?? '')) ?>" />
                  <button type="submit" aria-label="Xóa sản phẩm <?= e($productName) ?>">
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product-table-footer">
        <div class="rows-per-page">
          <span>Hiển thị</span>
          <button type="button"><?= e((string) $productCount) ?></button>
          <span>dòng</span>
        </div>

        <nav class="product-pagination" aria-label="Phân trang">
          <button type="button" aria-label="Trang trước" disabled>
            <i class="fa-solid fa-angle-left" aria-hidden="true"></i>
          </button>
          <button class="is-active" type="button">1</button>
          <button type="button" aria-label="Trang sau" disabled>
            <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
          </button>
        </nav>
      </div>
    <?php endif; ?>
  </section>
</section>