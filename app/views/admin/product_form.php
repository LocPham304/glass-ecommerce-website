<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$product = $product ?? [];
$formAction = $isEdit ? url('/admin/products/update') : url('/admin/products/create');
$pageHeading = $isEdit ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm';
$breadcrumbAction = $isEdit ? 'Chỉnh sửa' : 'Tạo mới';
$submitLabel = $isEdit ? 'Lưu cập nhật' : 'Xuất bản sản phẩm';
$draftLabel = $isEdit ? 'Lưu bản nháp' : 'Lưu nháp';
$statusValue = (string) ($product['is_active'] ?? '1');
$productImages = $product['images'] ?? [];
$hasImage = $productImages !== [] || !empty($product['image_url']);
$imagePreviewUrl = $productImages !== []
  ? media_url($productImages[0]['image_url'] ?? null)
  : ($hasImage ? media_url($product['image_url']) : '');

$colorOptions = [
  'Đen',
  'Trắng',
  'Xám',
  'Bạc',
  'Vàng Gold',
  'Nâu',
  'Đồi mồi',
  'Trong suốt',
  'Xanh dương',
  'Xanh lá',
  'Đỏ',
  'Hồng',
];

$sizeOptions = [
  'XS',
  'S',
  'M',
  'L',
  'XL',
  'Free size',
];

$materialOptions = [
  'Acetate',
  'Titanium',
  'Kim loại',
  'Hợp kim',
  'Thép không gỉ',
  'Nhựa TR90',
  'Polycarbonate',
];

$frameStyleOptions = [
  'Clubmaster',
  'Vuông',
  'Tròn',
  'Oval',
  'Aviator',
  'Wayfarer',
  'Mắt mèo',
  'Browline',
  'Không viền',
  'Nửa viền',
];

$selectedColors = parse_option_list($product['color'] ?? null);
$selectedSizes = parse_option_list($product['size'] ?? null);
$selectedMaterials = parse_option_list($product['material'] ?? null);
$selectedFrameStyles = parse_option_list($product['frame_style'] ?? null);

$colorOptions = array_values(array_unique(array_merge($colorOptions, $selectedColors)));
$sizeOptions = array_values(array_unique(array_merge($sizeOptions, $selectedSizes)));
$materialOptions = array_values(array_unique(array_merge($materialOptions, $selectedMaterials)));
$frameStyleOptions = array_values(array_unique(array_merge($frameStyleOptions, $selectedFrameStyles)));
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <p class="admin-create-breadcrumb">Sản phẩm <span>/</span> <?= e($breadcrumbAction) ?></p>
    <h1><?= e($pageHeading) ?></h1>
  </div>

  <a class="admin-secondary-link admin-secondary-link--topbar" href="<?= e(url('/admin/products')) ?>">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
    Quay lại danh sách
  </a>
</header>

<section class="admin-content admin-content--create">
  <form
    class="create-product-layout"
    data-product-create-form
    method="POST"
    action="<?= e($formAction) ?>"
    enctype="multipart/form-data"
  >
    <input type="hidden" name="submit_mode" value="publish" data-submit-mode />
    <?php if ($isEdit): ?>
      <input type="hidden" name="product_id" value="<?= e((string) ($product['id'] ?? '')) ?>" />
    <?php endif; ?>

    <section class="admin-panel create-panel create-panel--main">
      <div class="create-section">
        <div class="create-section__heading">
          <h2>Thông tin cơ bản</h2>
        </div>

        <div class="create-form-grid">
          <label class="create-field create-field--full">
            <span>Tên sản phẩm <em>*</em></span>
            <input
              type="text"
              name="name"
              placeholder="Ví dụ: Ray-Ban Clubmaster Classic"
              value="<?= e((string) ($product['name'] ?? '')) ?>"
              required
            />
          </label>

          <label class="create-field">
            <span>SKU <em>*</em></span>
            <input
              type="text"
              name="sku"
              placeholder="RB-3016-W0365"
              value="<?= e((string) ($product['sku'] ?? '')) ?>"
              required
            />
          </label>

          <label class="create-field">
            <span>Tên biến thể</span>
            <input
              type="text"
              name="variant_name"
              placeholder="Phiên bản tiêu chuẩn"
              value="<?= e((string) ($product['variant_name'] ?? '')) ?>"
            />
          </label>

          <label class="create-field">
            <span>Danh mục <em>*</em></span>
            <select name="category_id" required>
              <option value="">Chọn danh mục</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['id']) ?>" <?= (string) $category['id'] === (string) ($product['category_id'] ?? '') ? 'selected' : '' ?>>
                  <?= e($category['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="create-field create-field--full">
            <span>Mô tả ngắn</span>
            <textarea
              name="description"
              rows="4"
              placeholder="Mô tả nổi bật để hiển thị ở trang chi tiết sản phẩm."
            ><?= e((string) ($product['description'] ?? '')) ?></textarea>
          </label>
        </div>
      </div>

      <div class="create-section">
        <div class="create-section__heading">
          <h2>Giá bán và tồn kho</h2>
        </div>

        <div class="create-form-grid">
          <label class="create-field">
            <span>Giá bán <em>*</em></span>
            <input type="number" name="price" min="0" step="1000" placeholder="4250000" value="<?= e((string) ($product['price'] ?? '')) ?>" required />
          </label>

          <label class="create-field">
            <span>Giá niêm yết</span>
            <input type="number" name="original_price" min="0" step="1000" placeholder="2650000" value="<?= e((string) ($product['original_price'] ?? '')) ?>" />
          </label>

          <label class="create-field">
            <span>Số lượng tồn <em>*</em></span>
            <input type="number" name="stock_quantity" min="0" placeholder="120" value="<?= e((string) ($product['stock_quantity'] ?? '')) ?>" required />
          </label>

          <div class="create-field">
            <span>Màu sắc</span>
            <div class="variant-checkbox-grid">
              <?php foreach ($colorOptions as $option): ?>
                <label class="variant-checkbox">
                  <input
                    type="checkbox"
                    name="color[]"
                    value="<?= e($option) ?>"
                    <?= in_array($option, $selectedColors, true) ? 'checked' : '' ?>
                  />
                  <span><?= e($option) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="create-field">
            <span>Kích thước</span>
            <div class="variant-checkbox-grid variant-checkbox-grid--compact">
              <?php foreach ($sizeOptions as $option): ?>
                <label class="variant-checkbox">
                  <input
                    type="checkbox"
                    name="size[]"
                    value="<?= e($option) ?>"
                    <?= in_array($option, $selectedSizes, true) ? 'checked' : '' ?>
                  />
                  <span><?= e($option) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="create-field">
            <span>Chất liệu</span>
            <div class="variant-checkbox-grid">
              <?php foreach ($materialOptions as $option): ?>
                <label class="variant-checkbox">
                  <input
                    type="checkbox"
                    name="material[]"
                    value="<?= e($option) ?>"
                    <?= in_array($option, $selectedMaterials, true) ? 'checked' : '' ?>
                  />
                  <span><?= e($option) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="create-field">
            <span>Kiểu gọng</span>
            <div class="variant-checkbox-grid">
              <?php foreach ($frameStyleOptions as $option): ?>
                <label class="variant-checkbox">
                  <input
                    type="checkbox"
                    name="frame_style[]"
                    value="<?= e($option) ?>"
                    <?= in_array($option, $selectedFrameStyles, true) ? 'checked' : '' ?>
                  />
                  <span><?= e($option) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="create-section">
        <div class="create-section__heading">
          <h2>Cấu hình hiển thị</h2>
        </div>

        <div class="create-form-grid">
          <label class="create-field">
            <span>Loại sản phẩm</span>
            <select name="product_type">
              <option value="ready_stock" <?= ($product['product_type'] ?? 'ready_stock') === 'ready_stock' ? 'selected' : '' ?>>Đơn có sẵn</option>
              <option value="pre_order" <?= ($product['product_type'] ?? '') === 'pre_order' ? 'selected' : '' ?>>Pre-order</option>
              <option value="prescription" <?= ($product['product_type'] ?? '') === 'prescription' ? 'selected' : '' ?>>Cắt kính theo toa</option>
            </select>
          </label>

          <label class="create-field">
            <span>Trạng thái</span>
            <select name="status">
              <option value="active" <?= $statusValue === '1' ? 'selected' : '' ?>>Đang bán</option>
              <option value="inactive" <?= $statusValue === '0' ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
          </label>

          <label class="create-field">
            <span>Loại tròng</span>
            <input type="text" name="lens_type" placeholder="Blue Light, UV400..." value="<?= e((string) ($product['lens_type'] ?? '')) ?>" />
          </label>
        </div>
      </div>
    </section>

    <aside class="create-product-sidebar">
      <section class="admin-panel create-panel">
        <div class="create-section__heading">
          <h2>Ảnh sản phẩm</h2>
          <p>Tải tối đa 10 ảnh cho mỗi sản phẩm. Hỗ trợ PNG, JPG, JPEG, WEBP, GIF.</p>
        </div>

        <label class="image-uploader" for="product-image-input">
          <input
            id="product-image-input"
            type="file"
            name="product_images[]"
            accept=".png,.jpg,.jpeg,.webp,.gif,image/png,image/jpeg,image/webp,image/gif"
            multiple
            data-product-image
          />
          <div
            class="image-uploader__preview<?= $hasImage ? ' has-image' : '' ?>"
            data-image-preview
            <?= $hasImage ? 'style="background-image:url(\'' . e($imagePreviewUrl) . '\')"' : '' ?>
          >
            <i class="fa-regular fa-image" aria-hidden="true"></i>
            <strong><?= $hasImage ? 'Ảnh hiện tại' : 'Chọn ảnh sản phẩm' ?></strong>
            <span><?= $hasImage ? 'Tải bộ ảnh mới để thay thế gallery hiện tại' : 'Kéo thả hoặc nhấn để tải lên tối đa 10 ảnh' ?></span>
          </div>
        </label>

        <p class="image-uploader__hint">Ảnh đầu tiên sẽ được dùng làm ảnh bìa. Khi sửa sản phẩm, upload mới sẽ thay thế toàn bộ gallery cũ.</p>

        <div class="product-image-gallery" data-image-gallery>
          <?php if ($productImages !== []): ?>
            <?php foreach ($productImages as $index => $image): ?>
              <figure class="product-image-gallery__item">
                <img
                  src="<?= e(media_url($image['image_url'] ?? null)) ?>"
                  alt="Ảnh sản phẩm <?= e((string) ($index + 1)) ?>"
                />
                <figcaption><?= $index === 0 ? 'Ảnh bìa' : 'Ảnh ' . e((string) ($index + 1)) ?></figcaption>
              </figure>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </section>

      <section class="admin-panel create-panel create-panel--sticky">
        <div class="create-section__heading">
          <h2>Hành động</h2>
        </div>

        <div class="create-actions">
          <button class="admin-primary-button admin-primary-button--wide" type="submit">
            <?= e($submitLabel) ?>
          </button>
        </div>
      </section>
    </aside>
  </form>
</section>
