<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;

class AdminUserController extends Controller
{
    protected function normalizeRoleName(string $roleName): string
    {
        return $roleName === 'staff' ? 'sales' : $roleName;
    }

    public function create(): void
    {
        require_admin();

        $this->view('admin/user_create', [
            'pageTitle' => 'ClearVision | Thêm người dùng',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-user-create.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-user-create.js',
            ],
            'currentAdminSection' => 'customers',
            'roles' => (new User())->getRoles(),
            'formMode' => 'create',
            'user' => null,
            'defaultAddress' => null,
        ]);
    }

    public function edit(): void
    {
        require_admin();

        $userId = query_value('id');
        if ($userId === '') {
            flash('error', 'Không tìm thấy người dùng cần chỉnh sửa.');
            redirect('/admin/customers');
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        if ($user === null) {
            flash('error', 'Không tìm thấy người dùng cần chỉnh sửa.');
            redirect('/admin/customers');
        }

        $this->view('admin/user_create', [
            'pageTitle' => 'ClearVision | Chỉnh sửa người dùng',
            'bodyClass' => 'admin-body',
            'layout' => 'layouts/admin',
            'pageStyles' => [
                'assets/css/admin.css',
                'assets/css/admin-products.css',
                'assets/css/admin-user-create.css',
            ],
            'pageScripts' => [
                'assets/js/admin.js',
                'assets/js/admin-user-create.js',
            ],
            'currentAdminSection' => 'customers',
            'roles' => $userModel->getRoles(),
            'formMode' => 'edit',
            'user' => $user,
            'defaultAddress' => $userModel->getDefaultAddressByUserId($userId),
        ]);
    }

    public function store(): void
    {
        require_admin();

        $fullName = post_value('full_name');
        $email = post_value('email');
        $phone = post_value('phone');
        $roleName = $this->normalizeRoleName(post_value('role_name'));
        $password = post_value('password');
        $passwordConfirm = post_value('password_confirm');

        if ($fullName === '' || $email === '' || $roleName === '' || $password === '') {
            flash('error', 'Vui lòng nhập đủ họ tên, email, vai trò và mật khẩu.');
            redirect('/admin/users/create');
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Mật khẩu xác nhận chưa khớp.');
            redirect('/admin/users/create');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            flash('error', 'Email này đã tồn tại.');
            redirect('/admin/users/create');
        }

        $userModel->createManagedUser([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'role_name' => $roleName,
            'password' => $password,
            'gender' => post_value('gender'),
            'date_of_birth' => post_value('date_of_birth'),
            'status' => post_value('status', 'active'),
            'address_line' => post_value('address_line'),
        ]);

        flash('success', 'Đã tạo tài khoản người dùng mới.');
        redirect('/admin/customers');
    }

    public function update(): void
    {
        require_admin();

        $userId = post_value('user_id');
        $fullName = post_value('full_name');
        $email = post_value('email');
        $phone = post_value('phone');
        $roleName = $this->normalizeRoleName(post_value('role_name'));
        $password = post_value('password');
        $passwordConfirm = post_value('password_confirm');
        $redirectPath = '/admin/users/edit?id=' . urlencode($userId);

        if ($userId === '') {
            flash('error', 'Không tìm thấy người dùng cần cập nhật.');
            redirect('/admin/customers');
        }

        if ($fullName === '' || $email === '' || $roleName === '') {
            flash('error', 'Vui lòng nhập đủ họ tên, email và vai trò.');
            redirect($redirectPath);
        }

        if ($password !== '' && $password !== $passwordConfirm) {
            flash('error', 'Mật khẩu xác nhận chưa khớp.');
            redirect($redirectPath);
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        if ($user === null) {
            flash('error', 'Không tìm thấy người dùng cần cập nhật.');
            redirect('/admin/customers');
        }

        if ($userModel->findByEmailExcept($email, $userId)) {
            flash('error', 'Email này đã tồn tại.');
            redirect($redirectPath);
        }

        try {
            $userModel->updateManagedUser($userId, [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'role_name' => $roleName,
                'password' => $password,
                'gender' => post_value('gender'),
                'date_of_birth' => post_value('date_of_birth'),
                'status' => post_value('status', 'active'),
                'address_line' => post_value('address_line'),
            ]);
        } catch (\Throwable $throwable) {
            flash('error', $throwable->getMessage());
            redirect($redirectPath);
        }

        flash('success', 'Đã cập nhật người dùng thành công.');
        redirect('/admin/customers');
    }

    public function delete(): void
    {
        require_admin();

        $userId = post_value('user_id');
        if ($userId === '') {
            flash('error', 'Không tìm thấy người dùng cần xóa.');
            redirect('/admin/customers');
        }

        $currentUserId = auth_user()['id'] ?? null;
        if ($currentUserId === $userId) {
            flash('error', 'Bạn không thể xóa tài khoản đang đăng nhập.');
            redirect('/admin/customers');
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        if ($user === null) {
            flash('error', 'Không tìm thấy người dùng cần xóa.');
            redirect('/admin/customers');
        }

        try {
            $userModel->deleteManagedUser($userId);
        } catch (\Throwable $throwable) {
            flash('error', 'Không thể xóa người dùng này vì đã có dữ liệu nghiệp vụ liên quan. Bạn có thể chuyển trạng thái sang Tạm khóa.');
            redirect('/admin/customers');
        }

        flash('success', 'Đã xóa người dùng "' . (string) ($user['full_name'] ?? $userId) . '".');
        redirect('/admin/customers');
    }
}