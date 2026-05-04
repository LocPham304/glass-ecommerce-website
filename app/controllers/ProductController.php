<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Core\Controller;

class ProductController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $filters = [
            'keyword' => query_value('keyword'),
            'category_id' => query_value('category'),
            'sort' => query_value('sort', 'latest'),
        ];

        $products = $productModel->getCatalogProducts($filters);
        $categories = $productModel->getCategories();
        $cartCount = is_authenticated() ? (new Cart())->countItemsByUserId(auth_user()['id']) : 0;

        $this->view('products/index_shop', [
            'pageTitle' => 'ClearVision | Shop',
            'bodyClass' => 'shop-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/shop.css?v=20260504-2',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $categories,
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'headerCartCount' => $cartCount,
        ]);
    }

    public function show(): void
    {
        $productId = query_value('id');

        if ($productId === '') {
            $this->abort(404, 'Không tìm thấy sản phẩm.');
        }

        $productModel = new Product();
        $categories = $productModel->getCategories();
        $product = $productModel->findDetailById($productId);

        if ($product === null) {
            $this->abort(404, 'Không tìm thấy sản phẩm.');
        }

        $relatedProducts = $productModel->getRelatedProducts(
            $product['category_id'],
            $product['id'],
            4
        );
        $cartCount = is_authenticated() ? (new Cart())->countItemsByUserId(auth_user()['id']) : 0;

        $this->view('products/show', [
            'pageTitle' => 'ClearVision | ' . $product['name'],
            'bodyClass' => 'product-detail-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/product-detail.css?v=20260504-9',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
                'assets/js/mvc-product-detail.js?v=20260504-6',
            ],
            'navigationCategories' => $categories,
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'headerCartCount' => $cartCount,
        ]);
    }
}
