<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Core\Controller;

class CartController extends Controller
{
    public function index(): void
    {
        require_auth();

        $cartModel = new Cart();
        $productModel = new Product();
        $summary = $cartModel->getSummary(auth_user()['id']);

        $this->view('cart/index_clean', [
            'pageTitle' => 'ClearVision | Cart',
            'bodyClass' => 'cart-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/cart.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => $cartModel->countItemsByUserId(auth_user()['id']),
            'cartSummary' => $summary,
        ]);
    }

    public function add(): void
    {
        $wantsJson = $this->wantsJsonResponse();

        if (!is_authenticated()) {
            if ($wantsJson) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.',
                    'login_url' => url('/login'),
                ], 401);
            }

            require_auth();
        }

        $variantId = post_value('variant_id');
        $quantity = max(1, (int) post_value('quantity', '1'));
        $selectedColor = post_value('selected_color');
        $selectedSize = post_value('selected_size');
        $redirectTo = $this->getSafeRedirectPath(post_value('redirect_to'));

        if ($variantId === '') {
            if ($wantsJson) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Không xác định được biến thể sản phẩm.',
                ], 422);
            }

            flash('error', 'Không xác định được biến thể sản phẩm.');
            redirect($redirectTo !== '' ? $redirectTo : '/shop');
        }

        $cartModel = new Cart();

        try {
            $cartModel->addItem(auth_user()['id'], $variantId, $quantity, $selectedColor, $selectedSize);
        } catch (\RuntimeException $exception) {
            if ($wantsJson) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'cart_count' => $cartModel->countItemsByUserId(auth_user()['id']),
                ], 422);
            }

            flash('error', $exception->getMessage());
            $productId = post_value('product_id');
            redirect($redirectTo !== '' ? $redirectTo : ($productId !== '' ? '/product?id=' . urlencode($productId) : '/shop'));
        }

        if ($wantsJson) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'cart_count' => $cartModel->countItemsByUserId(auth_user()['id']),
            ]);
        }

        flash('success', 'Đã thêm sản phẩm vào giỏ hàng.');
        redirect($redirectTo !== '' ? $redirectTo : '/cart');
    }

    public function update(): void
    {
        require_auth();

        $cartItemId = post_value('cart_item_id');
        $quantity = (int) post_value('quantity', '1');

        try {
            (new Cart())->updateItemQuantity(auth_user()['id'], $cartItemId, $quantity);
        } catch (\RuntimeException $exception) {
            flash('error', $exception->getMessage());
            redirect('/cart');
        }

        flash('success', 'Đã cập nhật giỏ hàng.');
        redirect('/cart');
    }

    public function remove(): void
    {
        require_auth();

        $cartItemId = post_value('cart_item_id');
        (new Cart())->removeItem(auth_user()['id'], $cartItemId);
        flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        redirect('/cart');
    }

    protected function getSafeRedirectPath(string $path): string
    {
        if ($path === '' || str_starts_with($path, '//')) {
            return '';
        }

        return preg_match('#^/[A-Za-z0-9/_?=&%.\-#]*$#', $path) === 1 ? $path : '';
    }

    protected function wantsJsonResponse(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return post_value('ajax') === '1'
            || query_value('ajax') === '1'
            || str_contains($accept, 'application/json')
            || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function jsonResponse(array $payload, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );

        echo $json !== false ? $json : '{"success":false,"message":"Không thể tạo phản hồi JSON."}';
        exit;
    }
}