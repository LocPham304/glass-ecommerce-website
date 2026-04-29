<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <p class="admin-create-breadcrumb">Khuyến mại <span>/</span> Tạo voucher</p>
    <h1>Tạo voucher</h1>
  </div>

  <a class="admin-secondary-link admin-secondary-link--topbar" href="<?= e(url('/admin/promotions')) ?>">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
    Quay lại danh sách
  </a>
</header>

<section class="admin-content admin-content--create">
  <form class="create-product-layout" data-promotion-create-form method="POST" action="<?= e(url('/admin/promotions/create')) ?>">
    <section class="admin-panel create-panel create-panel--main">
      <div class="create-section">
        <div class="create-section__heading">
          <h2>Thông tin voucher</h2>
          <p>Tạo mã để người dùng nhập trực tiếp tại trang thanh toán hoặc giỏ hàng.</p>
        </div>

        <div class="create-form-grid">
          <label class="create-field">
            <span>Tên chiến dịch <em>*</em></span>
            <input type="text" name="name" placeholder="Ví dụ: Combo tháng 4" required />
          </label>

          <label class="create-field">
            <span>Mã voucher <em>*</em></span>
            <input type="text" name="code" placeholder="COMBO20" required />
          </label>

          <label class="create-field">
            <span>Loại giảm giá <em>*</em></span>
            <select name="discount_type" required data-discount-type>
              <option value="">Chọn loại giảm giá</option>
              <option value="percent">Phần trăm</option>
              <option value="cash">Số tiền cố định</option>
              <option value="ship">Miễn phí vận chuyển</option>
            </select>
          </label>

          <label class="create-field">
            <span>Giá trị giảm <em>*</em></span>
            <input type="number" name="discount_value" placeholder="20" data-discount-value required />
          </label>

          <label class="create-field">
            <span>Giảm tối đa</span>
            <input type="number" name="max_discount_value" placeholder="150000" data-max-discount />
          </label>
        </div>
      </div>

      <div class="create-section">
        <div class="create-section__heading">
          <h2>Điều kiện áp mã</h2>
          <p>Thiết lập ngưỡng đơn hàng, số lần sử dụng và thời gian hiệu lực của voucher.</p>
        </div>

        <div class="create-form-grid">
          <label class="create-field">
            <span>Đơn tối thiểu</span>
            <input type="number" name="min_order_value" placeholder="899000" />
          </label>

          <label class="create-field">
            <span>Giới hạn lượt dùng</span>
            <input type="number" name="usage_limit" placeholder="200" />
          </label>

          <label class="create-field">
            <span>Hiệu lực từ <em>*</em></span>
            <input type="date" name="start_at" required />
          </label>

          <label class="create-field">
            <span>Hiệu lực đến <em>*</em></span>
            <input type="date" name="expired_at" required />
          </label>

          <label class="create-field">
            <span>Trạng thái</span>
            <select name="status">
              <option value="active">Đang chạy</option>
              <option value="scheduled">Sắp diễn ra</option>
              <option value="draft">Lưu nháp</option>
            </select>
          </label>
        </div>
      </div>
    </section>

    <aside class="create-product-sidebar">
      <section class="admin-panel create-panel promotion-preview-panel">
        <div class="create-section__heading">
          <h2>Xem nhanh voucher</h2>
        </div>

        <div class="promotion-preview-card" data-promotion-preview>
          <p class="promotion-preview-card__label">MÃ ƯU ĐÃI</p>
          <strong data-preview-code>COMBO20</strong>
          <h3 data-preview-title>Giảm 20% cho combo gọng + tròng</h3>
          <ul>
            <li data-preview-value>Giảm giá: 20%</li>
            <li data-preview-max>Tối đa: 150.000đ</li>
            <li>Áp dụng tại trang thanh toán</li>
          </ul>
        </div>
      </section>

      <section class="admin-panel create-panel create-panel--sticky">
        <div class="create-section__heading">
          <h2>Hành động</h2>
        </div>

        <div class="create-actions">
          <button class="admin-primary-button admin-primary-button--wide" type="submit">
            <i class="fa-solid fa-ticket" aria-hidden="true"></i>
            Phát hành voucher
          </button>
        </div>
      </section>
    </aside>
  </form>
</section>