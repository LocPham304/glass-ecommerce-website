<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureProductVariantPricingColumns();
    }

    public function getFeaturedProducts(int $limit = 8): array
    {
        return $this->getCatalogProducts([
            'limit' => $limit,
        ]);
    }

    public function getCatalogProducts(array $filters = []): array
    {
        $conditions = [
            '(p.is_active = 1 OR p.is_active IS NULL)',
            'EXISTS (
                SELECT 1
                FROM product_variants pv_exists
                WHERE pv_exists.product_id = p.id
                  AND (pv_exists.is_active = 1 OR pv_exists.is_active IS NULL)
            )',
        ];
        $params = [];

        if (!empty($filters['keyword'])) {
            $conditions[] = '(
                p.name LIKE :keyword_name
                OR b.name LIKE :keyword_brand
                OR EXISTS (
                    SELECT 1
                    FROM product_variants pv_search
                    WHERE pv_search.product_id = p.id
                      AND pv_search.sku LIKE :keyword_sku
                )
            )';
            $keyword = '%' . $filters['keyword'] . '%';
            $params['keyword_name'] = $keyword;
            $params['keyword_brand'] = $keyword;
            $params['keyword_sku'] = $keyword;
        }

        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $conditions[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = $filters['brand_id'];
        }

        if (!empty($filters['product_type'])) {
            $conditions[] = 'p.product_type = :product_type';
            $params['product_type'] = $filters['product_type'];
        }

        if (!empty($filters['exclude_id'])) {
            $conditions[] = 'p.id <> :exclude_id';
            $params['exclude_id'] = $filters['exclude_id'];
        }

        $orderBy = match ($filters['sort'] ?? 'latest') {
            'price_asc' => 'price ASC, p.name ASC',
            'price_desc' => 'price DESC, p.name ASC',
            'name_asc' => 'p.name ASC',
            default => 'p.created_at DESC, p.id DESC',
        };

        $limit = '';
        if (!empty($filters['limit'])) {
            $limitValue = max(1, (int) $filters['limit']);
            $limit = " LIMIT {$limitValue}";
        }

        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.product_type,
                p.category_id,
                c.name AS category_name,
                c.slug AS category_slug,
                p.brand_id,
                b.name AS brand_name,
                b.slug AS brand_slug,
                (
                    SELECT pv.id
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS default_variant_id,
                (
                    SELECT pv.price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS price,
                (
                    SELECT pv.original_price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS original_price,
                (
                    SELECT pv.color
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS color,
                (
                    SELECT pv.size
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS size,
                (
                    SELECT pv.material
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS material,
                (
                    SELECT pv.stock_quantity
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                      AND (pv.is_active = 1 OR pv.is_active IS NULL)
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS stock_quantity,
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_url
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            INNER JOIN brands b ON b.id = p.brand_id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$orderBy}
            {$limit}
        ";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return array_map([$this, 'normalizeProductCategoryFields'], $statement->fetchAll());
    }

    public function getCategories(): array
    {
        $statement = $this->db->query('
            SELECT id, name, slug
            FROM categories
            ORDER BY name ASC
        ');

        return array_map([$this, 'normalizeCategoryRecord'], $statement->fetchAll());
    }

    public function getBrands(): array
    {
        $statement = $this->db->query('
            SELECT id, name, slug
            FROM brands
            WHERE is_active = 1 OR is_active IS NULL
            ORDER BY name ASC
        ');

        return $statement->fetchAll();
    }

    public function findDetailById(string $productId): ?array
    {
        $statement = $this->db->prepare('
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.product_type,
                p.category_id,
                c.name AS category_name,
                c.slug AS category_slug,
                p.brand_id,
                b.name AS brand_name,
                b.slug AS brand_slug
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            INNER JOIN brands b ON b.id = p.brand_id
            WHERE p.id = :id
            LIMIT 1
        ');
        $statement->execute([
            'id' => $productId,
        ]);

        $product = $statement->fetch();

        if (!$product) {
            return null;
        }

        $product = $this->normalizeProductCategoryFields($product);
        $product['variants'] = $this->getVariantsByProductId($productId);
        $product['images'] = $this->getImagesByProductId($productId);
        $product['default_variant'] = $product['variants'][0] ?? null;
        $product['primary_image'] = $product['images'][0]['image_url'] ?? null;

        return $product;
    }

    public function getRelatedProducts(string $categoryId, string $excludeId, int $limit = 4): array
    {
        return $this->getCatalogProducts([
            'category_id' => $categoryId,
            'exclude_id' => $excludeId,
            'limit' => $limit,
        ]);
    }

    public function getVariantById(string $variantId): ?array
    {
        $statement = $this->db->prepare('
            SELECT pv.*, p.name AS product_name, p.id AS product_id
            FROM product_variants pv
            INNER JOIN products p ON p.id = pv.product_id
            WHERE pv.id = :id
            LIMIT 1
        ');
        $statement->execute([
            'id' => $variantId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function getAllAdminProducts(): array
    {
        $statement = $this->db->query('
            SELECT
                p.id,
                p.name,
                p.slug,
                p.product_type,
                p.is_active,
                c.name AS category_name,
                c.slug AS category_slug,
                b.name AS brand_name,
                (
                    SELECT pv.sku
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS sku,
                (
                    SELECT pv.price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS price,
                (
                    SELECT pv.original_price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS original_price,
                (
                    SELECT pv.stock_quantity
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS stock_quantity,
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_url
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            INNER JOIN brands b ON b.id = p.brand_id
            ORDER BY p.created_at DESC, p.id DESC
        ');

        return array_map([$this, 'normalizeProductCategoryFields'], $statement->fetchAll());
    }

    public function getAdminProductById(string $productId): ?array
    {
        $statement = $this->db->prepare('
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.product_type,
                p.category_id,
                p.brand_id,
                p.is_active,
                c.name AS category_name,
                b.name AS brand_name,
                (
                    SELECT pv.id
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS variant_id,
                (
                    SELECT pv.sku
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS sku,
                (
                    SELECT pv.variant_name
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS variant_name,
                (
                    SELECT pv.frame_style
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS frame_style,
                (
                    SELECT pv.lens_type
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS lens_type,
                (
                    SELECT pv.color
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS color,
                (
                    SELECT pv.size
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS size,
                (
                    SELECT pv.material
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS material,
                (
                    SELECT pv.price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS price,
                (
                    SELECT pv.original_price
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS original_price,
                (
                    SELECT pv.stock_quantity
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS stock_quantity,
                (
                    SELECT pv.image_3d_url
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                    ORDER BY pv.created_at IS NULL, pv.created_at, pv.id
                    LIMIT 1
                ) AS image_3d_url,
                (
                    SELECT pi.id
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_id,
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_url
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            INNER JOIN brands b ON b.id = p.brand_id
            WHERE p.id = :id
            LIMIT 1
        ');
        $statement->execute([
            'id' => $productId,
        ]);

        $product = $statement->fetch() ?: null;

        if (!$product) {
            return null;
        }

        $product = $this->normalizeProductCategoryFields($product);
        $product['images'] = $this->getImagesByProductId($productId);
        $product['primary_image'] = $product['images'][0]['image_url'] ?? ($product['image_url'] ?? null);

        return $product;
    }

    public function createProductWithVariant(array $data): string
    {
        $productId = generate_id('PRD');
        $variantId = generate_id('VAR');
        $isActive = (int) ($data['is_active'] ?? 1) === 1 ? 1 : 0;

        $this->db->beginTransaction();

        try {
            $productStatement = $this->db->prepare('
                INSERT INTO products (
                    id, category_id, brand_id, name, slug, product_type, description, is_active, created_at, updated_at
                ) VALUES (
                    :id, :category_id, :brand_id, :name, :slug, :product_type, :description, :is_active, :created_at, :updated_at
                )
            ');
            $productStatement->execute([
                'id' => $productId,
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'product_type' => $data['product_type'],
                'description' => $data['description'] ?: null,
                'is_active' => $isActive,
                'created_at' => now_sql(),
                'updated_at' => now_sql(),
            ]);

            $variantStatement = $this->db->prepare('
                INSERT INTO product_variants (
                    id, product_id, sku, variant_name, frame_style, lens_type, color, size, material,
                    price, original_price, stock_quantity, image_3d_url, is_active, created_at, updated_at
                ) VALUES (
                    :id, :product_id, :sku, :variant_name, :frame_style, :lens_type, :color, :size, :material,
                    :price, :original_price, :stock_quantity, :image_3d_url, :is_active, :created_at, :updated_at
                )
            ');
            $variantStatement->execute([
                'id' => $variantId,
                'product_id' => $productId,
                'sku' => $data['sku'],
                'variant_name' => $data['variant_name'] ?: null,
                'frame_style' => $data['frame_style'] ?: null,
                'lens_type' => $data['lens_type'] ?: null,
                'color' => $data['color'] ?: null,
                'size' => $data['size'] ?: null,
                'material' => $data['material'] ?: null,
                'price' => $data['price'],
                'original_price' => $data['original_price'] ?? null,
                'stock_quantity' => $data['stock_quantity'],
                'image_3d_url' => $data['image_3d_url'] ?: null,
                'is_active' => $isActive,
                'created_at' => now_sql(),
                'updated_at' => now_sql(),
            ]);

            $imageUrls = $data['image_urls'] ?? [];
            if ($imageUrls === [] && !empty($data['image_url'])) {
                $imageUrls = [(string) $data['image_url']];
            }

            $this->insertProductImages($productId, $variantId, $imageUrls);

            if ((int) $data['stock_quantity'] > 0) {
                $inventoryStatement = $this->db->prepare('
                    INSERT INTO inventory_transactions (
                        id, product_variant_id, transaction_type, quantity, note, created_by, created_at
                    ) VALUES (
                        :id, :product_variant_id, :transaction_type, :quantity, :note, :created_by, :created_at
                    )
                ');
                $inventoryStatement->execute([
                    'id' => generate_id('IVT'),
                    'product_variant_id' => $variantId,
                    'transaction_type' => 'initial_stock',
                    'quantity' => (int) $data['stock_quantity'],
                    'note' => 'Initial stock when creating product',
                    'created_by' => $data['created_by'] ?: null,
                    'created_at' => now_sql(),
                ]);
            }

            $this->db->commit();
            return $productId;
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function updateProductWithVariant(string $productId, array $data): void
    {
        $product = $this->getAdminProductById($productId);
        if ($product === null) {
            throw new \RuntimeException('Không tìm thấy sản phẩm cần cập nhật.');
        }

        $variantId = (string) ($product['variant_id'] ?? '');
        if ($variantId === '') {
            throw new \RuntimeException('Sản phẩm chưa có biến thể để cập nhật.');
        }

        $imageId = (string) ($product['image_id'] ?? '');
        $isActive = (int) ($data['is_active'] ?? 1) === 1 ? 1 : 0;
        $oldStock = (int) ($product['stock_quantity'] ?? 0);
        $newStock = (int) ($data['stock_quantity'] ?? 0);

        $this->db->beginTransaction();

        try {
            $productStatement = $this->db->prepare('
                UPDATE products
                SET
                    category_id = :category_id,
                    brand_id = :brand_id,
                    name = :name,
                    slug = :slug,
                    product_type = :product_type,
                    description = :description,
                    is_active = :is_active,
                    updated_at = :updated_at
                WHERE id = :id
            ');
            $productStatement->execute([
                'id' => $productId,
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'product_type' => $data['product_type'],
                'description' => $data['description'] ?: null,
                'is_active' => $isActive,
                'updated_at' => now_sql(),
            ]);

            $variantStatement = $this->db->prepare('
                UPDATE product_variants
                SET
                    sku = :sku,
                    variant_name = :variant_name,
                    frame_style = :frame_style,
                    lens_type = :lens_type,
                    color = :color,
                    size = :size,
                    material = :material,
                    price = :price,
                    original_price = :original_price,
                    stock_quantity = :stock_quantity,
                    image_3d_url = :image_3d_url,
                    is_active = :is_active,
                    updated_at = :updated_at
                WHERE id = :id
            ');
            $variantStatement->execute([
                'id' => $variantId,
                'sku' => $data['sku'],
                'variant_name' => $data['variant_name'] ?: null,
                'frame_style' => $data['frame_style'] ?: null,
                'lens_type' => $data['lens_type'] ?: null,
                'color' => $data['color'] ?: null,
                'size' => $data['size'] ?: null,
                'material' => $data['material'] ?: null,
                'price' => $data['price'],
                'original_price' => $data['original_price'] ?? null,
                'stock_quantity' => $newStock,
                'image_3d_url' => $data['image_3d_url'] ?: null,
                'is_active' => $isActive,
                'updated_at' => now_sql(),
            ]);

            $imageUrls = $data['image_urls'] ?? [];
            if ($imageUrls === [] && !empty($data['image_url'])) {
                $imageUrls = [(string) $data['image_url']];
            }

            if ($imageUrls !== []) {
                $deleteImagesStatement = $this->db->prepare('DELETE FROM product_images WHERE product_id = :product_id');
                $deleteImagesStatement->execute([
                    'product_id' => $productId,
                ]);

                $this->insertProductImages($productId, $variantId, $imageUrls);
            }

            if ($newStock !== $oldStock) {
                $inventoryStatement = $this->db->prepare('
                    INSERT INTO inventory_transactions (
                        id, product_variant_id, transaction_type, quantity, note, created_by, created_at
                    ) VALUES (
                        :id, :product_variant_id, :transaction_type, :quantity, :note, :created_by, :created_at
                    )
                ');
                $inventoryStatement->execute([
                    'id' => generate_id('IVT'),
                    'product_variant_id' => $variantId,
                    'transaction_type' => 'manual_adjustment',
                    'quantity' => $newStock - $oldStock,
                    'note' => 'Manual stock adjustment from admin product edit',
                    'created_by' => $data['created_by'] ?: null,
                    'created_at' => now_sql(),
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function deleteProductById(string $productId): array
    {
        $product = $this->getAdminProductById($productId);
        if ($product === null) {
            throw new \RuntimeException('Khong tim thay san pham can xoa.');
        }

        $variantIds = $this->getVariantIdsByProductId($productId);
        $imagePaths = array_values(array_unique(array_filter(
            array_map(
                static fn(array $image): string => trim((string) ($image['image_url'] ?? '')),
                $this->getImagesByProductId($productId)
            ),
            static fn(string $imagePath): bool => $imagePath !== ''
        )));

        $this->db->beginTransaction();

        try {
            if ($variantIds !== []) {
                if ($this->countVariantReferences('combo_items', $variantIds) > 0) {
                    throw new \RuntimeException('Khong the xoa san pham nay vi dang duoc su dung trong combo.');
                }

                $this->executeVariantIdStatement(
                    'UPDATE order_items SET product_variant_id = NULL WHERE product_variant_id IN (%s)',
                    $variantIds
                );
                $this->executeVariantIdStatement(
                    'DELETE FROM cart_items WHERE product_variant_id IN (%s)',
                    $variantIds
                );
                $this->executeVariantIdStatement(
                    'DELETE FROM inventory_transactions WHERE product_variant_id IN (%s)',
                    $variantIds
                );
                $this->executeVariantIdStatement(
                    'DELETE FROM product_images WHERE variant_id IN (%s)',
                    $variantIds
                );
                $this->executeVariantIdStatement(
                    'DELETE FROM product_variants WHERE id IN (%s)',
                    $variantIds
                );
            }

            $imageStatement = $this->db->prepare('DELETE FROM product_images WHERE product_id = :product_id');
            $imageStatement->execute([
                'product_id' => $productId,
            ]);

            $productStatement = $this->db->prepare('DELETE FROM products WHERE id = :id');
            $productStatement->execute([
                'id' => $productId,
            ]);

            $this->db->commit();

            return $imagePaths;
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    protected function getVariantsByProductId(string $productId): array
    {
        $statement = $this->db->prepare('
            SELECT
                id,
                sku,
                variant_name,
                frame_style,
                lens_type,
                color,
                size,
                material,
                price,
                original_price,
                stock_quantity,
                image_3d_url
            FROM product_variants
            WHERE product_id = :product_id
              AND (is_active = 1 OR is_active IS NULL)
            ORDER BY created_at IS NULL, created_at, id
        ');
        $statement->execute([
            'product_id' => $productId,
        ]);

        return $statement->fetchAll();
    }

    protected function insertProductImages(string $productId, string $variantId, array $imageUrls): void
    {
        $imageUrls = array_values(array_filter(array_map(
            static fn(mixed $imageUrl): string => trim((string) $imageUrl),
            $imageUrls
        )));

        if ($imageUrls === []) {
            return;
        }

        $imageStatement = $this->db->prepare('
            INSERT INTO product_images (
                id, product_id, variant_id, image_url, image_type, sort_order
            ) VALUES (
                :id, :product_id, :variant_id, :image_url, :image_type, :sort_order
            )
        ');

        foreach ($imageUrls as $index => $imageUrl) {
            $imageStatement->execute([
                'id' => generate_id('IMG'),
                'product_id' => $productId,
                'variant_id' => $variantId,
                'image_url' => $imageUrl,
                'image_type' => $index === 0 ? 'primary' : 'gallery',
                'sort_order' => $index + 1,
            ]);
        }
    }

    protected function normalizeCategoryRecord(array $category): array
    {
        $category['name'] = category_display_name(
            (string) ($category['slug'] ?? ''),
            (string) ($category['name'] ?? '')
        );

        return $category;
    }

    protected function normalizeProductCategoryFields(array $product): array
    {
        if (array_key_exists('category_slug', $product) || array_key_exists('category_name', $product)) {
            $product['category_name'] = category_display_name(
                (string) ($product['category_slug'] ?? ''),
                (string) ($product['category_name'] ?? '')
            );
        }

        return $product;
    }

    protected function getVariantIdsByProductId(string $productId): array
    {
        $statement = $this->db->prepare('
            SELECT id
            FROM product_variants
            WHERE product_id = :product_id
        ');
        $statement->execute([
            'product_id' => $productId,
        ]);

        return array_values(array_filter(array_map(
            static fn(array $variant): string => (string) ($variant['id'] ?? ''),
            $statement->fetchAll()
        )));
    }

    protected function countVariantReferences(string $table, array $variantIds): int
    {
        if ($variantIds === []) {
            return 0;
        }

        $placeholders = implode(', ', array_fill(0, count($variantIds), '?'));
        $statement = $this->db->prepare("
            SELECT COUNT(*) AS aggregate
            FROM {$table}
            WHERE product_variant_id IN ({$placeholders})
        ");
        $statement->execute($variantIds);
        $result = $statement->fetch();

        return (int) ($result['aggregate'] ?? 0);
    }

    protected function executeVariantIdStatement(string $sqlTemplate, array $variantIds): void
    {
        if ($variantIds === []) {
            return;
        }

        $placeholders = implode(', ', array_fill(0, count($variantIds), '?'));
        $statement = $this->db->prepare(sprintf($sqlTemplate, $placeholders));
        $statement->execute($variantIds);
    }

    protected function getImagesByProductId(string $productId): array
    {
        $statement = $this->db->prepare('
            SELECT
                id,
                product_id,
                variant_id,
                image_url,
                image_type,
                sort_order
            FROM product_images
            WHERE product_id = :product_id
               OR variant_id IN (
                    SELECT id
                    FROM product_variants
                    WHERE product_id = :product_id_variants
               )
            ORDER BY sort_order IS NULL, sort_order, id
        ');
        $statement->execute([
            'product_id' => $productId,
            'product_id_variants' => $productId,
        ]);

        return $statement->fetchAll();
    }

    protected function ensureProductVariantPricingColumns(): void
    {
        if ($this->productVariantsColumnExists('original_price')) {
            return;
        }

        $this->db->exec('ALTER TABLE product_variants ADD COLUMN original_price decimal(10,2) NULL AFTER price');
    }

    protected function productVariantsColumnExists(string $column): bool
    {
        $statement = $this->db->prepare('
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = "product_variants"
              AND COLUMN_NAME = :column
            LIMIT 1
        ');
        $statement->execute([
            'column' => $column,
        ]);

        return (bool) $statement->fetch();
    }
}
