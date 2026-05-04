<?php

namespace App\Controllers;

use App\Models\Voucher;
use Core\Controller;

class AdminPromotionController extends Controller
{
    public function index(): void
    {
        require_admin();

        $this->view('admin/promotions', [
            'pageTitle' => 'ClearVision | Khuyến mãi',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-promotions.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-promotions.js',
            ],
            'currentAdminSection' => 'promotions',
            'vouchers' => (new Voucher())->getAllVouchers(),
        ]);
    }

    public function create(): void
    {
        require_admin();

        $this->view('admin/promotion_create_fixed', [
            'pageTitle' => 'ClearVision | Tạo voucher',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-product-create.css',
                'assets/css/admin-promotion-create.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-promotion-create-fixed.js',
            ],
            'currentAdminSection' => 'promotions',
        ]);
    }

    public function store(): void
    {
        require_admin();

        $name = post_value('name');
        $code = strtoupper(post_value('code'));
        $discountType = post_value('discount_type');
        $discountValue = (float) post_value('discount_value', '0');
        $startAt = post_value('start_at');
        $expiredAt = post_value('expired_at');
        $status = post_value('status', 'active');

        if ($name === '' || $code === '' || $discountType === '' || $discountValue <= 0 || $startAt === '' || $expiredAt === '') {
            flash('error', 'Vui lòng nhập đủ thông tin voucher bắt buộc.');
            redirect('/admin/promotions/create');
        }

        (new Voucher())->create([
            'name' => $name,
            'code' => $code,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_value' => (float) post_value('min_order_value', '0'),
            'max_discount_value' => (float) post_value('max_discount_value', '0'),
            'start_at' => $startAt !== '' ? $startAt . ' 00:00:00' : null,
            'expired_at' => $expiredAt !== '' ? $expiredAt . ' 23:59:59' : null,
            'usage_limit' => (int) post_value('usage_limit', '0'),
            'is_active' => $status !== 'draft' ? 1 : 0,
        ]);

        flash('success', 'Đã tạo voucher mới.');
        redirect('/admin/promotions');
    }
}