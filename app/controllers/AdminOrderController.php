<?php

namespace App\Controllers;

use App\Models\Order;
use Core\Controller;

class AdminOrderController extends Controller
{
    public function index(): void
    {
        require_admin();

        $this->view('admin/orders', [
            'pageTitle' => 'ClearVision | Orders Manage',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-orders.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-orders.js',
            ],
            'currentAdminSection' => 'orders',
            'orders' => (new Order())->getAllOrders(),
        ]);
    }

    public function updateStatus(): void
    {
        require_admin();

        (new Order())->updateAdminOrder(post_value('order_id'), auth_user()['id'], [
            'order_status' => post_value('order_status'),
            'note' => post_value('note'),
            'carrier' => post_value('carrier'),
            'tracking_code' => post_value('tracking_code'),
            'shipping_status' => post_value('shipping_status'),
            'shipped_at' => post_value('shipped_at'),
            'delivered_at' => post_value('delivered_at'),
            'shipment_note' => post_value('shipment_note'),
            'expected_arrival_date' => post_value('expected_arrival_date'),
            'pre_order_status' => post_value('pre_order_status'),
            'supplier_note' => post_value('supplier_note'),
            'received_at' => post_value('received_at'),
            'prescription_status' => post_value('prescription_status'),
            'prescription_note' => post_value('prescription_note'),
            'workflow_step' => post_value('workflow_step'),
            'workflow_note' => post_value('workflow_note'),
        ]);
        flash('success', 'Đã cập nhật trạng thái đơn hàng.');
        redirect('/admin/orders');
    }
}
