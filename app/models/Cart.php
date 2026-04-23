<?php

namespace App\Models;

use Core\Model;
use RuntimeException;

class Cart extends Model
{
    protected bool $cartItemOptionColumnsReady = false;

    public function getOrCreateCart(string $userId): array
    {
        $statement = $this->db->prepare('
            SELECT *
            FROM carts
            WHERE user_id = :user_id
            LIMIT 1
        ');
        $statement->execute([
            'user_id' => $userId,
        ]);

        $cart = $statement->fetch();
        if ($cart) {
            return $cart;
        }

        $cartId = generate_id('CRT');
        $insert = $this->db->prepare('
            INSERT INTO carts (id, user_id, created_at, updated_at)
            VALUES (:id, :user_id, :created_at, :updated_at)
        ');
        $insert->execute([
            'id' => $cartId,
            'user_id' => $userId,
            'created_at' => now_sql(),
            'updated_at' => now_sql(),
        ]);

        return [
            'id' => $cartId,
            'user_id' => $userId,
        ];
    }

    public function addItem(string $userId, string $variantId, int $quantity = 1, string $selectedColor = '', string $selectedSize = ''): void
    {
        $this->ensureCartItemOptionColumns();

        $cart = $this->getOrCreateCart($userId);

        $variantStatement = $this->db->prepare('
            SELECT
                pv.price,
                pv.stock_quantity,
                pv.color,
                pv.size,
                p.product_type
            FROM product_variants pv
            INNER JOIN products p ON p.id = pv.product_id
            WHERE pv.id = :id
            LIMIT 1
        ');
        $variantStatement->execute([
            'id' => $variantId,
        ]);
        $variant = $variantStatement->fetch();

        if (!$variant) {
            throw new RuntimeException('Không tìm thấy biến thể sản phẩm.');
        }

        $selectedColor = $this->normalizeSelectedOption($selectedColor, (string) ($variant['color'] ?? ''));
        $selectedSize = $this->normalizeSelectedOption($selectedSize, (string) ($variant['size'] ?? ''));

        $existingStatement = $this->db->prepare('
            SELECT id, quantity
            FROM cart_items
            WHERE cart_id = :cart_id
              AND product_variant_id = :variant_id
              AND COALESCE(selected_color, "") = :selected_color
              AND COALESCE(selected_size, "") = :selected_size
            LIMIT 1
        ');
        $existingStatement->execute([
            'cart_id' => $cart['id'],
            'variant_id' => $variantId,
            'selected_color' => $selectedColor,
            'selected_size' => $selectedSize,
        ]);

        $existingItem = $existingStatement->fetch();
        $nextQuantity = max(1, $quantity);

        if ($existingItem) {
            $nextQuantity += (int) $existingItem['quantity'];
        }

        if (
            ($variant['product_type'] ?? 'ready_stock') === 'ready_stock'
            && (int) $variant['stock_quantity'] < $nextQuantity
        ) {
            throw new RuntimeException('Số lượng tồn kho hiện không đủ cho sản phẩm này.');
        }

        if ($existingItem) {
            $update = $this->db->prepare('
                UPDATE cart_items
                SET quantity = :quantity
                WHERE id = :id
            ');
            $update->execute([
                'id' => $existingItem['id'],
                'quantity' => $nextQuantity,
            ]);
        } else {
            $insert = $this->db->prepare('
                INSERT INTO cart_items (
                    id, cart_id, product_variant_id, selected_color, selected_size, quantity, unit_price
                ) VALUES (
                    :id, :cart_id, :variant_id, :selected_color, :selected_size, :quantity, :unit_price
                )
            ');
            $insert->execute([
                'id' => generate_id('CTI'),
                'cart_id' => $cart['id'],
                'variant_id' => $variantId,
                'selected_color' => $selectedColor !== '' ? $selectedColor : null,
                'selected_size' => $selectedSize !== '' ? $selectedSize : null,
                'quantity' => $nextQuantity,
                'unit_price' => $variant['price'],
            ]);
        }

        $this->touchCart($cart['id']);
    }

    public function getItemsByUserId(string $userId): array
    {
        $this->ensureCartItemOptionColumns();

        $cart = $this->getOrCreateCart($userId);

        $statement = $this->db->prepare('
            SELECT
                ci.id,
                ci.quantity,
                ci.unit_price,
                pv.id AS variant_id,
                pv.sku,
                pv.variant_name,
                pv.color,
                pv.size,
                ci.selected_color,
                ci.selected_size,
                pv.stock_quantity,
                p.product_type,
                p.id AS product_id,
                p.name AS product_name,
                b.name AS brand_name,
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id OR pi.variant_id = pv.id
                    ORDER BY pi.sort_order IS NULL, pi.sort_order, pi.id
                    LIMIT 1
                ) AS image_url
            FROM cart_items ci
            INNER JOIN product_variants pv ON pv.id = ci.product_variant_id
            INNER JOIN products p ON p.id = pv.product_id
            INNER JOIN brands b ON b.id = p.brand_id
            WHERE ci.cart_id = :cart_id
            ORDER BY ci.id DESC
        ');
        $statement->execute([
            'cart_id' => $cart['id'],
        ]);

        $items = $statement->fetchAll();

        foreach ($items as &$item) {
            $item['selected_color'] = $this->resolveDisplayOption(
                $item['selected_color'] ?? null,
                $item['color'] ?? null
            );
            $item['selected_size'] = $this->resolveDisplayOption(
                $item['selected_size'] ?? null,
                $item['size'] ?? null
            );
        }
        unset($item);

        return $items;
    }

    public function countItemsByUserId(string $userId): int
    {
        $cart = $this->getOrCreateCart($userId);

        $statement = $this->db->prepare('
            SELECT COALESCE(SUM(quantity), 0) AS total_quantity
            FROM cart_items
            WHERE cart_id = :cart_id
        ');
        $statement->execute([
            'cart_id' => $cart['id'],
        ]);

        $row = $statement->fetch() ?: [];

        return (int) ($row['total_quantity'] ?? 0);
    }

    public function updateItemQuantity(string $userId, string $cartItemId, int $quantity): void
    {
        $cart = $this->getOrCreateCart($userId);

        if ($quantity <= 0) {
            $this->removeItem($userId, $cartItemId);
            return;
        }

        $itemStatement = $this->db->prepare('
            SELECT
                pv.stock_quantity,
                p.product_type
            FROM cart_items ci
            INNER JOIN product_variants pv ON pv.id = ci.product_variant_id
            INNER JOIN products p ON p.id = pv.product_id
            WHERE ci.id = :id
              AND ci.cart_id = :cart_id
            LIMIT 1
        ');
        $itemStatement->execute([
            'id' => $cartItemId,
            'cart_id' => $cart['id'],
        ]);

        $item = $itemStatement->fetch();
        if (
            $item
            && ($item['product_type'] ?? 'ready_stock') === 'ready_stock'
            && (int) $item['stock_quantity'] < $quantity
        ) {
            throw new RuntimeException('Số lượng cập nhật vượt quá tồn kho hiện tại.');
        }

        $statement = $this->db->prepare('
            UPDATE cart_items
            SET quantity = :quantity
            WHERE id = :id
              AND cart_id = :cart_id
        ');
        $statement->execute([
            'id' => $cartItemId,
            'cart_id' => $cart['id'],
            'quantity' => $quantity,
        ]);

        $this->touchCart($cart['id']);
    }

    public function removeItem(string $userId, string $cartItemId): void
    {
        $cart = $this->getOrCreateCart($userId);

        $statement = $this->db->prepare('
            DELETE FROM cart_items
            WHERE id = :id
              AND cart_id = :cart_id
        ');
        $statement->execute([
            'id' => $cartItemId,
            'cart_id' => $cart['id'],
        ]);

        $this->touchCart($cart['id']);
    }

    public function getSummary(string $userId): array
    {
        $items = $this->getItemsByUserId($userId);
        $subtotal = 0.0;

        foreach ($items as &$item) {
            $item['line_total'] = (float) $item['unit_price'] * (int) $item['quantity'];
            $subtotal += $item['line_total'];
        }

        $productTypes = array_values(array_unique(array_filter(array_map(
            static fn (array $item): string => (string) ($item['product_type'] ?? ''),
            $items
        ))));

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'count' => count($items),
            'product_types' => $productTypes,
            'recommended_order_type' => count($productTypes) === 1 ? $productTypes[0] : null,
        ];
    }

    public function clearByUserId(string $userId): void
    {
        $cart = $this->getOrCreateCart($userId);

        $statement = $this->db->prepare('
            DELETE FROM cart_items
            WHERE cart_id = :cart_id
        ');
        $statement->execute([
            'cart_id' => $cart['id'],
        ]);

        $this->touchCart($cart['id']);
    }

    protected function touchCart(string $cartId): void
    {
        $statement = $this->db->prepare('
            UPDATE carts
            SET updated_at = :updated_at
            WHERE id = :id
        ');
        $statement->execute([
            'id' => $cartId,
            'updated_at' => now_sql(),
        ]);
    }

    protected function ensureCartItemOptionColumns(): void
    {
        if ($this->cartItemOptionColumnsReady) {
            return;
        }

        if (!$this->cartItemsColumnExists('selected_color')) {
            $this->db->exec('ALTER TABLE cart_items ADD COLUMN selected_color varchar(80) NULL AFTER product_variant_id');
        }

        if (!$this->cartItemsColumnExists('selected_size')) {
            $this->db->exec('ALTER TABLE cart_items ADD COLUMN selected_size varchar(80) NULL AFTER selected_color');
        }

        $this->backfillCartItemOptions();
        $this->cartItemOptionColumnsReady = true;
    }

    protected function cartItemsColumnExists(string $column): bool
    {
        $statement = $this->db->prepare('
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = "cart_items"
              AND COLUMN_NAME = :column
            LIMIT 1
        ');
        $statement->execute(['column' => $column]);

        return (bool) $statement->fetch();
    }

    protected function backfillCartItemOptions(): void
    {
        $this->db->exec('
            UPDATE cart_items ci
            INNER JOIN product_variants pv ON pv.id = ci.product_variant_id
            SET
                ci.selected_color = CASE
                    WHEN ci.selected_color IS NULL THEN NULLIF(TRIM(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(pv.color, ";", ","), "|", ","), "/", ","), ",", 1)), "")
                    ELSE ci.selected_color
                END,
                ci.selected_size = CASE
                    WHEN ci.selected_size IS NULL THEN NULLIF(TRIM(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(pv.size, ";", ","), "|", ","), "/", ","), ",", 1)), "")
                    ELSE ci.selected_size
                END
            WHERE ci.selected_color IS NULL
               OR ci.selected_size IS NULL
        ');
    }

    protected function normalizeSelectedOption(string $selectedOption, string $availableOptions): string
    {
        $selectedOption = trim($selectedOption);
        $options = parse_option_list($availableOptions);

        if ($options === []) {
            return '';
        }

        return in_array($selectedOption, $options, true) ? $selectedOption : $options[0];
    }

    protected function resolveDisplayOption(?string $selectedOption, ?string $availableOptions): string
    {
        $selectedOption = trim((string) $selectedOption);
        if ($selectedOption !== '') {
            return $selectedOption;
        }

        return parse_option_list($availableOptions)[0] ?? '';
    }
}