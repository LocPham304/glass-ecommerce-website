<div class="page-shell auth-page">
  <main class="auth-main">
    <div class="site-container auth-container">
      <section class="auth-card auth-card--login" aria-labelledby="login-title">
        <h1 class="auth-title" id="login-title">Đăng nhập</h1>
        <form class="auth-form d-grid gap-3" method="POST" action="<?= e(url('/login')) ?>">
          <div>
            <label class="form-label fw-semibold" for="login-email">Email</label>
            <input class="form-control form-control-lg" id="login-email" name="email" type="email" />
          </div>
          <div>
            <label class="form-label fw-semibold" for="login-password">Mật khẩu</label>
            <input class="form-control form-control-lg" id="login-password" name="password" type="password" />
          </div>
          <button class="btn btn-warning btn-lg text-white fw-semibold auth-submit" type="submit">
            Đăng nhập
          </button>
          <p class="mb-0 text-secondary text-center">
            Chưa có tài khoản?
            <a href="<?= e(url('/register')) ?>" class="createAcc">Tạo tài khoản mới</a>
          </p>
        </form>
      </section>
    </div>
  </main>
</div>