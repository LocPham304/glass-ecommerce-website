<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $categories = $productModel->getCategories();
        $featuredProducts = $productModel->getFeaturedProducts(8);
        $cartCount = is_authenticated() ? (new Cart())->countItemsByUserId(auth_user()['id']) : 0;

        $this->view('home/index_template', [
            'pageTitle' => 'ClearVision | Homepage',
            'bodyClass' => 'homepage-body',
            'pageStyles' => [
                'assets/css/homepage.css?v=20260418-1',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $categories,
            'featuredProducts' => $featuredProducts,
            'headerCartCount' => $cartCount,
        ]);
    }

    public function about(): void
    {
        $this->renderStaticPage(
            'home/about',
            'ClearVision | About',
            'about-body',
            [
                'assets/css/homepage.css',
                'assets/css/about-us.css',
            ]
        );
    }

    public function warranty(): void
    {
        $this->renderStaticPage(
            'home/warranty',
            'ClearVision | Warranty',
            'warranty-body',
            [
                'assets/css/homepage.css',
                'assets/css/warranty-policy.css',
            ]
        );
    }

    protected function renderStaticPage(
        string $view,
        string $pageTitle,
        string $bodyClass,
        array $pageStyles
    ): void {
        $productModel = new Product();
        $cartCount = is_authenticated() ? (new Cart())->countItemsByUserId(auth_user()['id']) : 0;

        $this->view($view, [
            'pageTitle' => $pageTitle,
            'bodyClass' => $bodyClass,
            'pageStyles' => $pageStyles,
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => $cartCount,
        ]);
    }
}
