<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use Core\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (is_authenticated()) {
            redirect(has_role(['admin', 'manager', 'sales', 'operations']) ? '/admin' : '/profile');
        }

        $this->view('auth/login', [
            'pageTitle' => 'ClearVision | Login',
            'bodyClass' => 'login-body',
            'layout' => 'layouts/auth',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/auth.css',
            ],
        ]);
    }

    public function login(): void
    {
        $email = post_value('email');
        $password = post_value('password');

        if ($email === '' || $password === '') {
            flash('error', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
            flash('error', 'Email hoặc mật khẩu không đúng.');
            redirect('/login');
        }

        login_user([
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_name' => $user['role_name'],
        ]);

        flash('success', 'Đăng nhập thành công.');
        redirect(has_role(['admin', 'manager', 'sales', 'operations']) ? '/admin' : '/profile');
    }

    public function showRegister(): void
    {
        if (is_authenticated()) {
            redirect(has_role(['admin', 'manager', 'sales', 'operations']) ? '/admin' : '/profile');
        }

        $this->view('auth/register', [
            'pageTitle' => 'ClearVision | Register',
            'bodyClass' => 'register-body',
            'layout' => 'layouts/auth',
            'pageStyles' => [
                'assets/css/homepage.css',
                'assets/css/auth.css',
            ],
        ]);
    }

    public function register(): void
    {
        $fullName = post_value('full_name');
        $phone = post_value('phone');
        $email = post_value('email');
        $password = post_value('password');
        $passwordConfirm = post_value('password_confirm');

        if ($fullName === '' || $email === '' || $password === '') {
            flash('error', 'Vui lòng nhập đầy đủ thông tin bắt buộc.');
            redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Mật khẩu xác nhận chưa khớp.');
            redirect('/register');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            flash('error', 'Email này đã tồn tại.');
            redirect('/register');
        }

        $user = $userModel->createCustomer([
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email,
            'password' => $password,
        ]);

        login_user([
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_name' => $user['role_name'],
        ]);

        flash('success', 'Tạo tài khoản thành công.');
        redirect('/profile');
    }

    public function logout(): void
    {
        logout_user();
        flash('success', 'Bạn đã đăng xuất.');
        redirect('/login');
    }
}