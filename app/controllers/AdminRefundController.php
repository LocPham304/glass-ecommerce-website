<?php

namespace App\Controllers;

use App\Models\AfterSales;
use Core\Controller;

class AdminRefundController extends Controller
{
    public function index(): void
    {
        require_admin();

        $this->view('admin/refunds_manage', [
            'pageTitle' => 'ClearVision | Quản lý đổi trả',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-returns.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-returns-manage.js',
            ],
            'currentAdminSection' => 'refunds',
            'requests' => (new AfterSales())->getAllRequests(),
        ]);
    }

    public function update(): void
    {
        require_admin();

        (new AfterSales())->updateRequest(post_value('request_id'), auth_user()['id'], [
            'status' => post_value('status', 'processing'),
            'refund_amount' => (float) post_value('refund_amount', '0'),
            'refund_method' => post_value('refund_method', 'bank_transfer'),
            'refund_status' => post_value('refund_status', 'processed'),
            'note' => post_value('note'),
        ]);

        flash('success', 'Đã cập nhật yêu cầu đổi trả.');
        redirect('/admin/refunds');
    }
}
