<?php

namespace App\Controllers;

use App\Models\AfterSales;
use App\Models\Order;
use App\Models\User;
use Core\Controller;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        require_admin();

        $orderModel = new Order();

        $this->view('admin/dashboard', [
            'pageTitle' => 'ClearVision | Dashboard',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
            ],
            'currentAdminSection' => 'dashboard',
            'dashboardStats' => $orderModel->getDashboardStats(),
        ]);
    }

    public function customers(): void
    {
        require_admin();

        $this->view('admin/customers', [
            'pageTitle' => 'ClearVision | Quản lý người dùng',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-users.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-users.js',
            ],
            'currentAdminSection' => 'customers',
            'customers' => (new User())->getAllUsers(),
        ]);
    }

    public function reports(): void
    {
        require_admin();

        $orderModel = new Order();

        $this->view('admin/reports_dashboard', [
            'pageTitle' => 'ClearVision | Báo cáo',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-reports.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-reports-dashboard.js',
            ],
            'currentAdminSection' => 'reports',
            'dashboardStats' => $orderModel->getDashboardStats(),
            'revenueReport' => $orderModel->getRevenueReport(),
            'reportPayload' => $orderModel->getAdminReportPayload(),
            'refundRequests' => (new AfterSales())->getAllRequests(),
        ]);
    }
}