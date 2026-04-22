<div class="page-shell auth-page">
  <main class="auth-main">
    <div class="site-container auth-container">
      <section class="auth-card auth-card--register" aria-labelledby="register-title">
        <h1 class="auth-title" id="register-title">Tạo tài khoản</h1>
        <form class="row g-3 auth-form" method="POST" action="<?= e(url('/register')) ?>">
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="register-name">Họ và tên</label>
            <input class="form-control form-control-lg" id="register-name" name="full_name" type="text" />
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="register-phone">Số điện thoại</label>
            <input class="form-control form-control-lg" id="register-phone" name="phone" type="tel" />
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold" for="register-email">Email</label>
            <input class="form-control form-control-lg" id="register-email" name="email" type="email" />
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="register-password">Mật khẩu</label>
            <input class="form-control form-control-lg" id="register-password" name="password" type="password" />
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="register-confirm">Xác nhận mật khẩu</label>
            <input class="form-control form-control-lg" id="register-confirm" name="password_confirm" type="password" />
          </div>
          <div class="col-12">
            <button class="btn btn-warning btn-lg text-white fw-semibold w-100 auth-submit" type="submit">
              Tạo tài khoản
            </button>
          </div>
          <p class="mb-0 text-secondary text-center">
            Đã có tài khoản?
            <a href="<?= e(url('/login')) ?>" class="createAcc">Đăng nhập ngay</a>
          </p>
        </form>
      </section>
    </div>
  </main>
</div>