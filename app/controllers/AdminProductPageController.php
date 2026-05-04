<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Controller;

class AdminProductPageController extends Controller
{
    public function index(): void
    {
        require_admin();

        $productModel = new Product();

        $this->view('admin/products_manage', [
            'pageTitle' => 'ClearVision | Quản lý sản phẩm',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css?v=20260504-2',
                'assets/css/admin-products-manage.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-products-manage.js?v=20260418-1',
            ],
            'currentAdminSection' => 'products',
            'products' => $productModel->getAllAdminProducts(),
            'categories' => $productModel->getCategories(),
            'brands' => $productModel->getBrands(),
        ]);
    }

    public function create(): void
    {
        require_admin();

        $productModel = new Product();

        $this->view('admin/product_form', [
            'pageTitle' => 'ClearVision | Thêm sản phẩm',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-product-create.css',
                'assets/css/admin-product-create-form.css?v=20260504-2',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-product-create-form.js',
            ],
            'currentAdminSection' => 'products',
            'categories' => $productModel->getCategories(),
            'brands' => $productModel->getBrands(),
            'formMode' => 'create',
            'product' => null,
        ]);
    }

    public function edit(): void
    {
        require_admin();

        $productId = query_value('id');
        if ($productId === '') {
            flash('error', 'Không tìm thấy sản phẩm cần chỉnh sửa.');
            redirect('/admin/products');
        }

        $productModel = new Product();
        $product = $productModel->getAdminProductById($productId);

        if ($product === null) {
            flash('error', 'Không tìm thấy sản phẩm cần chỉnh sửa.');
            redirect('/admin/products');
        }

        $this->view('admin/product_form', [
            'pageTitle' => 'ClearVision | Chỉnh sửa sản phẩm',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-product-create.css',
                'assets/css/admin-product-create-form.css?v=20260504-2',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-product-create-form.js',
            ],
            'currentAdminSection' => 'products',
            'categories' => $productModel->getCategories(),
            'brands' => $productModel->getBrands(),
            'formMode' => 'edit',
            'product' => $product,
        ]);
    }

    public function store(): void
    {
        require_admin();

        $uploadedImages = [];

        try {
            $uploadedImages = $this->storeProductImageUploads();
        } catch (\RuntimeException $exception) {
            flash('error', $exception->getMessage());
            redirect('/admin/products/create');
        }

        $name = post_value('name');
        $categoryId = post_value('category_id');
        $productModel = new Product();
        $brandId = $this->resolveDefaultBrandId($productModel);
        $sku = post_value('sku');

        if ($name === '' || $categoryId === '' || $sku === '') {
            flash('error', 'Vui lòng nhập đủ tên sản phẩm, danh mục và SKU.');
            redirect('/admin/products/create');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $submitMode = post_value('submit_mode', 'publish');
        $status = post_value('status', 'active');
        $isActive = ($status === 'active' && $submitMode !== 'draft') ? 1 : 0;
        $price = (float) post_value('price', '0');

        try {
            $productModel->createProductWithVariant([
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'name' => $name,
                'slug' => $slug,
                'product_type' => post_value('product_type', 'ready_stock'),
                'description' => post_value('description'),
                'sku' => $sku,
                'variant_name' => post_value('variant_name'),
                'frame_style' => $this->postListValue('frame_style'),
                'lens_type' => post_value('lens_type'),
                'color' => $this->postListValue('color'),
                'size' => $this->postListValue('size'),
                'material' => $this->postListValue('material'),
                'price' => $price,
                'original_price' => $this->normalizeOriginalPrice(post_value('original_price'), $price),
                'stock_quantity' => (int) post_value('stock_quantity', '0'),
                'image_3d_url' => post_value('image_3d_url'),
                'image_urls' => $uploadedImages,
                'is_active' => $isActive,
                'created_by' => auth_user()['id'] ?? null,
            ]);
        } catch (\Throwable $throwable) {
            $this->deleteUploadedFiles($uploadedImages);
            flash('error', $throwable->getMessage());
            redirect('/admin/products/create');
        }

        flash(
            'success',
            $submitMode === 'draft'
                ? 'Đã lưu sản phẩm ở trạng thái nháp.'
                : 'Đã tạo sản phẩm mới.'
        );

        redirect('/admin/products');
    }

    public function update(): void
    {
        require_admin();

        $productId = post_value('product_id');
        if ($productId === '') {
            flash('error', 'Không tìm thấy sản phẩm cần cập nhật.');
            redirect('/admin/products');
        }

        $productModel = new Product();
        $existingProduct = $productModel->getAdminProductById($productId);

        if ($existingProduct === null) {
            flash('error', 'Không tìm thấy sản phẩm cần cập nhật.');
            redirect('/admin/products');
        }

        $uploadedImages = [];

        try {
            $uploadedImages = $this->storeProductImageUploads();
        } catch (\RuntimeException $exception) {
            flash('error', $exception->getMessage());
            redirect('/admin/products/edit?id=' . urlencode($productId));
        }

        $name = post_value('name');
        $categoryId = post_value('category_id');
        $brandId = (string) ($existingProduct['brand_id'] ?? '');
        if ($brandId === '') {
            $brandId = $this->resolveDefaultBrandId($productModel);
        }
        $sku = post_value('sku');

        if ($name === '' || $categoryId === '' || $sku === '') {
            flash('error', 'Vui lòng nhập đủ tên sản phẩm, danh mục và SKU.');
            redirect('/admin/products/edit?id=' . urlencode($productId));
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $submitMode = post_value('submit_mode', 'publish');
        $status = post_value('status', 'active');
        $isActive = ($status === 'active' && $submitMode !== 'draft') ? 1 : 0;
        $price = (float) post_value('price', '0');
        $existingImagePaths = array_values(array_unique(array_filter(array_map(
            static fn(array $image): string => trim((string) ($image['image_url'] ?? '')),
            $existingProduct['images'] ?? []
        ))));

        try {
            $productModel->updateProductWithVariant($productId, [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'name' => $name,
                'slug' => $slug,
                'product_type' => post_value('product_type', 'ready_stock'),
                'description' => post_value('description'),
                'sku' => $sku,
                'variant_name' => post_value('variant_name'),
                'frame_style' => $this->postListValue('frame_style'),
                'lens_type' => post_value('lens_type'),
                'color' => $this->postListValue('color'),
                'size' => $this->postListValue('size'),
                'material' => $this->postListValue('material'),
                'price' => $price,
                'original_price' => $this->normalizeOriginalPrice(post_value('original_price'), $price),
                'stock_quantity' => (int) post_value('stock_quantity', '0'),
                'image_3d_url' => post_value('image_3d_url'),
                'image_urls' => $uploadedImages,
                'is_active' => $isActive,
                'created_by' => auth_user()['id'] ?? null,
            ]);

            if ($uploadedImages !== []) {
                foreach ($existingImagePaths as $imagePath) {
                    if (str_starts_with(ltrim($imagePath, '/'), 'uploads/products/')) {
                        $this->deleteUploadedFile($imagePath);
                    }
                }
            }
        } catch (\Throwable $throwable) {
            $this->deleteUploadedFiles($uploadedImages);
            flash('error', $throwable->getMessage());
            redirect('/admin/products/edit?id=' . urlencode($productId));
        }

        flash(
            'success',
            $submitMode === 'draft'
                ? 'Đã cập nhật sản phẩm ở trạng thái nháp.'
                : 'Đã cập nhật sản phẩm thành công.'
        );

        redirect('/admin/products');
    }

    public function delete(): void
    {
        require_admin();

        $productId = post_value('product_id');
        if ($productId === '') {
            flash('error', 'Khong tim thay san pham can xoa.');
            redirect('/admin/products');
        }

        $productModel = new Product();
        $existingProduct = $productModel->getAdminProductById($productId);

        if ($existingProduct === null) {
            flash('error', 'Khong tim thay san pham can xoa.');
            redirect('/admin/products');
        }

        try {
            $deletedImages = $productModel->deleteProductById($productId);

            foreach ($deletedImages as $imagePath) {
                if (str_starts_with(ltrim((string) $imagePath, '/'), 'uploads/products/')) {
                    $this->deleteUploadedFile((string) $imagePath);
                }
            }
        } catch (\Throwable $throwable) {
            flash('error', $throwable->getMessage());
            redirect('/admin/products');
        }

        flash('success', 'Da xoa san pham "' . (string) ($existingProduct['name'] ?? $productId) . '" thanh cong.');
        redirect('/admin/products');
    }

    protected function storeProductImageUploads(): array
    {
        $files = $_FILES['product_images'] ?? null;
        if (!$files || !isset($files['error']) || !is_array($files['error'])) {
            return [];
        }

        $errors = $files['error'];
        $names = $files['name'] ?? [];
        $sizes = $files['size'] ?? [];
        $temporaryPaths = $files['tmp_name'] ?? [];
        $selectedCount = 0;

        foreach ($errors as $error) {
            if ((int) $error !== UPLOAD_ERR_NO_FILE) {
                $selectedCount += 1;
            }
        }

        if ($selectedCount === 0) {
            return [];
        }

        if ($selectedCount > 10) {
            throw new \RuntimeException('Moi san pham chi duoc tai toi da 10 anh.');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $targetDirectory = BASE_PATH . '/uploads/products';
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException('Khong the tao thu muc luu anh san pham.');
        }

        $uploadedPaths = [];

        try {
            foreach ($errors as $index => $error) {
                if ((int) $error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ((int) $error !== UPLOAD_ERR_OK) {
                    throw new \RuntimeException('Tai len anh san pham khong thanh cong. Vui long thu lai.');
                }

                if ((int) ($sizes[$index] ?? 0) > 5 * 1024 * 1024) {
                    throw new \RuntimeException('Moi anh san pham chi duoc toi da 5MB.');
                }

                $extension = strtolower((string) pathinfo((string) ($names[$index] ?? ''), PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions, true)) {
                    throw new \RuntimeException('Anh san pham chi ho tro JPG, JPEG, PNG, WEBP hoac GIF.');
                }

                $targetFileName = sprintf('%s.%s', generate_id('PRI'), $extension);
                $targetPath = $targetDirectory . '/' . $targetFileName;

                if (!move_uploaded_file((string) ($temporaryPaths[$index] ?? ''), $targetPath)) {
                    throw new \RuntimeException('Khong the luu anh san pham da tai len.');
                }

                $uploadedPaths[] = 'uploads/products/' . $targetFileName;
            }
        } catch (\Throwable $throwable) {
            $this->deleteUploadedFiles($uploadedPaths);
            throw $throwable;
        }

        return $uploadedPaths;

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Tải lên ảnh sản phẩm không thành công. Vui lòng thử lại.');
        }

        if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Ảnh sản phẩm chỉ được tối đa 5MB.');
        }

        $extension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \RuntimeException('Ảnh sản phẩm chỉ hỗ trợ JPG, PNG hoặc WEBP.');
        }

        $targetDirectory = BASE_PATH . '/uploads/products';
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException('Không thể tạo thư mục lưu ảnh sản phẩm.');
        }

        $targetFileName = sprintf('%s.%s', generate_id('PRI'), $extension);
        $targetPath = $targetDirectory . '/' . $targetFileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Không thể lưu ảnh sản phẩm đã tải lên.');
        }

        return 'uploads/products/' . $targetFileName;
    }

    protected function postListValue(string $key): string
    {
        $value = $_POST[$key] ?? '';

        if (!is_array($value)) {
            return trim((string) $value);
        }

        $items = [];
        foreach ($value as $item) {
            $item = trim((string) $item);
            if ($item === '' || in_array($item, $items, true)) {
                continue;
            }

            $items[] = $item;
        }

        return implode(', ', $items);
    }

    protected function resolveDefaultBrandId(Product $productModel): string
    {
        $brands = $productModel->getBrands();
        $brandId = (string) ($brands[0]['id'] ?? '');

        if ($brandId === '') {
            throw new \RuntimeException('Chưa có thương hiệu mặc định trong hệ thống để lưu sản phẩm.');
        }

        return $brandId;
    }

    protected function normalizeOriginalPrice(string $value, float $price): ?float
    {
        $originalPrice = (float) trim($value);

        if ($originalPrice <= 0 || $originalPrice <= $price) {
            return null;
        }

        return $originalPrice;
    }

    protected function deleteUploadedFiles(array $relativePaths): void
    {
        foreach ($relativePaths as $relativePath) {
            $this->deleteUploadedFile((string) $relativePath);
        }
    }

    protected function deleteUploadedFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $fullPath = BASE_PATH . '/' . ltrim($relativePath, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
