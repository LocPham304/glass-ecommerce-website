<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Core\Controller;

class CheckoutController extends Controller
{
    public function index(): void
    {
        require_auth();

        $uploadedPrescriptionImage = null;
        try {
            $uploadedPrescriptionImage = $this->storePrescriptionImageUpload();
        } catch (\RuntimeException $exception) {
            $this->deleteUploadedFile($uploadedPrescriptionImage);
            flash('error', $exception->getMessage());
            redirect('/checkout');
        }

        $userModel = new User();
        $cartModel = new Cart();
        $productModel = new Product();
        $user = $userModel->findById(auth_user()['id']);
        $defaultAddress = $userModel->getDefaultAddressByUserId(auth_user()['id']);
        $summary = $cartModel->getSummary(auth_user()['id']);

        if ($summary['items'] === []) {
            flash('error', 'Giỏ hàng đang trống.');
            redirect('/cart');
        }

        $this->view('checkout/index_merged', [
            'pageTitle' => 'ClearVision | Checkout',
            'bodyClass' => 'checkout-body',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/checkout.css',
                'assets/css/checkout-merged.css',
            ],
            'pageScripts' => [
                'assets/js/mvc-site.js',
                'assets/js/checkout-page.js',
            ],
            'navigationCategories' => $productModel->getCategories(),
            'headerCartCount' => $cartModel->countItemsByUserId(auth_user()['id']),
            'user' => $user,
            'defaultAddress' => $defaultAddress,
            'cartSummary' => $summary,
        ]);
    }

    public function store(): void
    {
        require_auth();

        $userId = auth_user()['id'];
        $receiverName = post_value('receiver_name');
        $receiverPhone = post_value('receiver_phone');
        $province = post_value('province');
        $ward = post_value('ward');
        $addressLine = post_value('address_line');

        if ($receiverName === '' || $receiverPhone === '' || $province === '' || $ward === '' || $addressLine === '') {
            flash('error', 'Vui lòng nhập đầy đủ người nhận, số điện thoại và địa chỉ giao hàng.');
            redirect('/checkout');
        }

        try {
            $userModel = new User();
            $addressId = $userModel->upsertDefaultAddress($userId, [
                'receiver_name' => $receiverName,
                'receiver_phone' => $receiverPhone,
                'province' => $province,
                'district' => post_value('district'),
                'ward' => $ward,
                'address_line' => $addressLine,
            ]);

            $orderModel = new Order();
            $orderId = $orderModel->placeOrder($userId, $addressId, [
                'order_type' => post_value('order_type', 'ready_stock'),
                'voucher_code' => post_value('voucher_code'),
                'payment_method' => post_value('payment_method', 'cod'),
                'note' => post_value('note'),
                'prescription_image' => $uploadedPrescriptionImage,
                'expected_arrival_date' => post_value('expected_arrival_date'),
                'sphere_left' => post_value('sphere_left'),
                'sphere_right' => post_value('sphere_right'),
                'cylinder_left' => post_value('cylinder_left'),
                'cylinder_right' => post_value('cylinder_right'),
                'axis_left' => post_value('axis_left'),
                'axis_right' => post_value('axis_right'),
                'pd' => post_value('pd'),
                'add_power' => post_value('add_power'),
            ]);
        } catch (\Throwable $exception) {
            $this->deleteUploadedFile($uploadedPrescriptionImage);
            flash('error', $exception->getMessage());
            redirect('/checkout');
        }

        flash('success', 'Đặt hàng thành công.');
        redirect('/order?id=' . urlencode($orderId));
    }

    public function voucherPreview(): void
    {
        require_auth();

        header('Content-Type: application/json; charset=UTF-8');

        $subtotal = (float) post_value('subtotal', '0');
        $voucherCode = post_value('voucher_code');

        try {
            $preview = (new Order())->previewVoucher($voucherCode, $subtotal);

            echo json_encode([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công.',
                'data' => $preview,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\RuntimeException $exception) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    protected function storePrescriptionImageUpload(): ?string
    {
        $file = $_FILES['prescription_image'] ?? null;
        if (!$file || !isset($file['error']) || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Tải lên phiếu khám mắt không thành công. Vui lòng thử lại.');
        }

        if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Phiếu khám mắt chỉ được tối đa 5MB.');
        }

        $extension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \RuntimeException('Phiếu khám mắt chỉ hỗ trợ JPG, PNG hoặc WEBP.');
        }

        $targetDirectory = BASE_PATH . '/uploads/prescriptions';
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException('Không thể tạo thư mục lưu phiếu khám mắt.');
        }

        $targetFileName = sprintf('%s.%s', generate_id('RX'), $extension);
        $targetPath = $targetDirectory . '/' . $targetFileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Không thể lưu phiếu khám mắt đã tải lên.');
        }

        return 'uploads/prescriptions/' . $targetFileName;
    }

    protected function deleteUploadedFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $fullPath = BASE_PATH . '/' . ltrim($relativePath, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}