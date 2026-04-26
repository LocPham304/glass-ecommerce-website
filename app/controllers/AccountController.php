<?php

namespace App\Controllers;

use App\Models\AfterSales;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Core\Controller;

class AccountController extends Controller
{
    public function profile(): void
    {
        require_auth();

        $userModel = new User();
        $productModel = new Product();
        $orderModel = new Order();
        $userId = auth_user()['id'];

        $this->view('account/profile', [
            'pageTitle' => 'ClearVision | Profile',
            'bodyClass' => 'profile-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/profile.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => (new Cart())->countItemsByUserId($userId),
            'user' => $userModel->findById($userId),
            'defaultAddress' => $userModel->getDefaultAddressByUserId($userId),
            'accountStats' => $userModel->getOrderStats($userId),
            'recentOrders' => array_slice($orderModel->getOrdersByUserId($userId), 0, 5),
        ]);
    }

    public function updateProfile(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $userModel = new User();

        $userModel->updateProfile($userId, [
            'full_name' => post_value('full_name'),
            'phone' => post_value('phone'),
            'gender' => post_value('gender'),
            'date_of_birth' => post_value('date_of_birth'),
        ]);

        if (post_value('address_line') !== '' || post_value('receiver_name') !== '') {
            $userModel->upsertDefaultAddress($userId, [
                'receiver_name' => post_value('receiver_name', post_value('full_name')),
                'receiver_phone' => post_value('receiver_phone', post_value('phone')),
                'province' => post_value('province'),
                'district' => post_value('district'),
                'ward' => post_value('ward'),
                'address_line' => post_value('address_line'),
            ]);
        }

        $user = $userModel->findById($userId);
        login_user([
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_name' => $user['role_name'],
        ]);

        flash('success', 'Đã cập nhật thông tin tài khoản.');
        redirect('/profile');
    }

    public function orders(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $productModel = new Product();
        $orderModel = new Order();

        $this->view('account/orders', [
            'pageTitle' => 'ClearVision | My Orders',
            'bodyClass' => 'orders-history-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/my-orders.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => (new Cart())->countItemsByUserId($userId),
            'orders' => $orderModel->getOrdersByUserId($userId),
        ]);
    }

    public function orderDetail(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $orderId = query_value('id');
        $productModel = new Product();
        $order = (new Order())->getOrderByIdForUser($orderId, $userId);

        if (!$order) {
            flash('error', 'Không tìm thấy đơn hàng.');
            redirect('/orders');
        }

        $this->view('account/order_detail', [
            'pageTitle' => 'ClearVision | Order Detail',
            'bodyClass' => 'order-detail-body',
            'pageStyles' => [
                'assets/css/homepage.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => (new Cart())->countItemsByUserId($userId),
            'order' => $order,
        ]);
    }

    public function afterSales(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $afterSalesModel = new AfterSales();
        $productModel = new Product();
        $orderModel = new Order();
        $userModel = new User();

        $this->view('account/after_sales', [
            'pageTitle' => 'ClearVision | Return Request',
            'bodyClass' => 'refund-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/return-refund.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
                'assets/js/return-refund.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => (new Cart())->countItemsByUserId($userId),
            'user' => $userModel->findById($userId),
            'orders' => $orderModel->getOrdersByUserId($userId),
            'afterSalesRequests' => $afterSalesModel->getRequestsByUserId($userId),
        ]);
    }

    public function submitAfterSales(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $orderId = post_value('order_id');
        $orderCode = strtoupper(post_value('order_code'));

        if ($orderId === '' && $orderCode !== '') {
            foreach ((new Order())->getOrdersByUserId($userId) as $userOrder) {
                if (strtoupper((string) ($userOrder['order_code'] ?? '')) === $orderCode) {
                    $orderId = $userOrder['id'];
                    break;
                }
            }
        }

        if ($orderId === '') {
            flash('error', 'Vui lòng chọn đơn hàng cần gửi yêu cầu.');
            redirect('/after-sales');
        }

        $order = (new Order())->getOrderByIdForUser($orderId, $userId);
        if (!$order) {
            flash('error', 'Đơn hàng không hợp lệ hoặc không thuộc tài khoản của bạn.');
            redirect('/after-sales');
        }

        if (!in_array($order['order_status'], ['delivered', 'after_sales'], true)) {
            flash('error', 'Chỉ có thể gửi yêu cầu hậu mãi cho đơn đã hoàn thành.');
            redirect('/after-sales');
        }

        $afterSalesModel = new AfterSales();
        if ($afterSalesModel->hasActiveRequest($userId, $orderId)) {
            flash('error', 'Đơn hàng này đang có yêu cầu hậu mãi đang xử lý.');
            redirect('/after-sales');
        }

        $afterSalesModel->createRequest($userId, $orderId, [
            'request_type' => post_value('request_type', 'exchange'),
            'reason' => post_value('reason'),
            'description' => post_value('description'),
        ]);

        flash('success', 'Đã gửi yêu cầu đổi trả / hoàn tiền.');
        redirect('/after-sales');
    }
}