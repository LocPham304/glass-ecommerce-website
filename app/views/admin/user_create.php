<?php
$formMode = $formMode ?? 'create';
$user = $user ?? null;
$defaultAddress = $defaultAddress ?? null;
$isEdit = $formMode === 'edit';
$roleValue = (string) ($user['role_name'] ?? 'admin');
$formAction = $isEdit ? url('/admin/users/update') : url('/admin/users/create');
$headingTitle = $isEdit ? 'Chỉnh sửa người dùng' : 'Thêm người dùng';
$breadcrumbCurrent = $isEdit ? 'Chỉnh sửa' : 'Tạo mới';
$submitLabel = $isEdit ? 'Cập nhật người dùng' : 'Tạo người dùng';
$accountDescription = $isEdit
    ? 'Cập nhật thông tin tài khoản, vai trò, trạng thái hoặc đổi mật khẩu khi cần.'
    : 'Tạo tài khoản mới cho quản trị viên, nhân viên vận hành hoặc khách hàng.';
$passwordLabel = $isEdit ? 'Mật khẩu mới' : 'Mật khẩu tạm';
$passwordPlaceholder = $isEdit ? 'Để trống nếu không đổi mật khẩu' : 'Nhập mật khẩu tạm thời';
$confirmPlaceholder = $isEdit ? 'Nhập lại mật khẩu mới nếu có đổi' : 'Nhập lại mật khẩu';
$addressLine = (string) ($defaultAddress['address_line'] ?? '');
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <p class="admin-create-breadcrumb">Người dùng <span>/</span> <?= e($breadcrumbCurrent) ?></p>
    <h1><?= e($headingTitle) ?></h1>
  </div>

  <a class="admin-secondary-link admin-secondary-link--topbar" href="<?= e(url('/admin/customers')) ?>">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
    Quay lại danh sách
  </a>
</header>

<section class="admin-content admin-content--create">
  <form class="create-user-layout" data-user-create-form data-form-mode="<?= e($formMode) ?>" method="POST" action="<?= e($formAction) ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="user_id" value="<?= e((string) ($user['id'] ?? '')) ?>" />
    <?php endif; ?>
    <section class="admin-panel create-panel create-panel--main">
      <div class="create-section">
        <div class="create-section__heading">
          <h2>Thông tin tài khoản</h2>
          <p><?= e($accountDescription) ?></p>
        </div>

        <div class="create-form-grid">
          <label class="create-field">
            <span>Họ và tên <em>*</em></span>
            <input type="text" name="full_name" value="<?= e((string) ($user['full_name'] ?? '')) ?>" placeholder="Ví dụ: Nguyễn Văn A" required />
          </label>
          <label class="create-field">
            <span>Email <em>*</em></span>
            <input type="email" name="email" value="<?= e((string) ($user['email'] ?? '')) ?>" placeholder="customer@clearvision.vn" required />
          </label>
          <label class="create-field">
            <span>Số điện thoại <em>*</em></span>
            <input type="tel" name="phone" value="<?= e((string) ($user['phone'] ?? '')) ?>" placeholder="0901 234 567" required />
          </label>
          <label class="create-field">
            <span>Vai trò <em>*</em></span>
            <select name="role_name" required data-user-role>
              <option value="admin" <?= $roleValue === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
              <option value="manager" <?= $roleValue === 'manager' ? 'selected' : '' ?>>Quản lý</option>
              <option value="sales" <?= $roleValue === 'sales' ? 'selected' : '' ?>>Nhân viên bán hàng</option>
              <option value="operations" <?= $roleValue === 'operations' ? 'selected' : '' ?>>Nhân viên vận hành</option>
              <option value="customer" <?= $roleValue === 'customer' ? 'selected' : '' ?>>Khách hàng</option>
            </select>
          </label>
          <label class="create-field">
            <span>Trạng thái</span>
            <select name="status">
              <option value="active" <?= (string) ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
              <option value="inactive" <?= (string) ($user['status'] ?? 'active') !== 'active' ? 'selected' : '' ?>>Tạm khóa</option>
            </select>
          </label>
          <label class="create-field">
            <span><?= e($passwordLabel) ?><?= $isEdit ? '' : ' <em>*</em>' ?></span>
            <input type="password" name="password" placeholder="<?= e($passwordPlaceholder) ?>" <?= $isEdit ? '' : 'required' ?> />
          </label>
          <label class="create-field">
            <span>Xác nhận mật khẩu<?= $isEdit ? '' : ' <em>*</em>' ?></span>
            <input type="password" name="password_confirm" placeholder="<?= e($confirmPlaceholder) ?>" <?= $isEdit ? '' : 'required' ?> />
          </label>
        </div>
      </div>

      <div class="create-section">
        <div class="create-section__heading">
          <h2>Thông tin bổ sung</h2>
          <p>Thêm thông tin liên hệ để đội vận hành dễ quản lý hơn.</p>
        </div>

        <div class="create-form-grid">
          <label class="create-field create-field--full">
            <span>Địa chỉ</span>
            <input type="text" name="address_line" value="<?= e($addressLine) ?>" placeholder="123 Lê Lợi, Quận 1, TP. HCM" />
          </label>
          <label class="create-field">
            <span>Giới tính</span>
            <input type="text" name="gender" value="<?= e((string) ($user['gender'] ?? '')) ?>" placeholder="Nam / Nữ / Khác" />
          </label>
          <label class="create-field">
            <span>Ngày sinh</span>
            <input type="date" name="date_of_birth" value="<?= e((string) ($user['date_of_birth'] ?? '')) ?>" />
          </label>
        </div>
      </div>
    </section>

    <aside class="create-user-sidebar">
      <section class="admin-panel create-panel">
        <div class="create-section__heading">
          <h2>Ảnh đại diện</h2>
        </div>

        <label class="image-uploader" for="user-avatar-input">
          <input id="user-avatar-input" type="file" accept="image/*" data-user-avatar />
          <div class="image-uploader__preview image-uploader__preview--avatar" data-avatar-preview>
            <i class="fa-regular fa-user" aria-hidden="true"></i>
            <strong>Chọn ảnh đại diện</strong>
            <span>Kéo thả hoặc nhấn để tải ảnh lên</span>
          </div>
        </label>
      </section>

      <section class="admin-panel create-panel create-panel--sticky">

        <div class="create-actions">
          <?php if (!$isEdit): ?>
            <button class="admin-secondary-button admin-secondary-button--wide" type="button" data-save-draft>
              <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
              Lưu nháp
            </button>
          <?php endif; ?>
          <button class="admin-primary-button admin-primary-button--wide" type="submit">
            <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
            <?= e($submitLabel) ?>
          </button>
        </div>
      </section>
    </aside>
  </form>
</section>