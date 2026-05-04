<main class="warranty-main">
  <section class="policy-hero">
    <div class="site-container policy-hero__grid">
      <div class="policy-hero__copy">
        <p class="policy-kicker">CLEARVISION CARE</p>
        <h1><span>Chính sách</span> <strong>Bảo hành</strong></h1>
        <p>
          Chúng tôi cam kết mang lại trải nghiệm hậu mãi tận tâm để đôi mắt của bạn
          luôn được chăm sóc tốt nhất với chính sách rõ ràng, minh bạch.
        </p>
        <div class="policy-hero__actions">
          <a class="btn-primary-custom" href="#support">Đặt lịch</a>
          <a class="btn-outline-custom" href="<?= e(url('/orders')) ?>">Tra cứu đơn</a>
        </div>
      </div>

      <div class="policy-hero__visual">
        <img
          src="<?= e(asset('assets/images/warranty-policy/hero-frame.png')) ?>"
          alt="Khách hàng ClearVision"
        />
      </div>
    </div>
  </section>

  <section class="policy-section policy-section--cards">
    <div class="site-container">
      <div class="section-heading-center coverage-heading">
        <h2>Phạm vi &amp; Thời gian bảo hành</h2>
      </div>

      <div class="coverage-grid">
        <article class="coverage-card">
          <div class="coverage-card__header">
            <div class="coverage-card__icon">
              <img class="brand__mark" src="<?= e(asset('assets/images/common/logo.png')) ?>" alt="ClearVision logo" />
            </div>
            <div class="coverage-card__intro">
              <h3>Gọng kính</h3>
              <p class="coverage-card__description">Bảo hành 06 - 12 tháng cho các lỗi kỹ thuật từ phía nhà sản xuất.</p>
            </div>
          </div>
          <ul>
            <li>Sứt mối hàn, bong tróc xi</li>
            <li>Lỗi lò xo, gãy bản lề tự nhiên</li>
          </ul>
        </article>

        <article class="coverage-card">
          <div class="coverage-card__header">
            <div class="coverage-card__icon coverage-card__icon--eye" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M12 5C6.8 5 2.73 8.11 1 12c1.73 3.89 5.8 7 11 7s9.27-3.11 11-7c-1.73-3.89-5.8-7-11-7Zm0 12c-3.56 0-6.54-2-8.22-5C5.46 9 8.44 7 12 7s6.54 2 8.22 5C18.54 15 15.56 17 12 17Zm0-8a3 3 0 1 0 3 3 3 3 0 0 0-3-3Zm0 4.2A1.2 1.2 0 1 1 13.2 12 1.2 1.2 0 0 1 12 13.2Z"></path>
              </svg>
            </div>
            <div class="coverage-card__intro">
              <h3>Tròng kính</h3>
              <p class="coverage-card__description">Bảo hành lớp phủ vàng 06 tháng đối với các thương hiệu chính hãng.</p>
            </div>
          </div>
          <ul>
            <li>Bong tróc lớp chống chói (AR)</li>
            <li>Nổ lớp váng tự nhiên</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="policy-section">
    <div class="site-container conditions-grid">
      <div class="conditions-copy">
        <h2>Điều kiện bảo hành</h2>
        <ul class="condition-list">
          <li><span class="condition-index">1</span>Sản phẩm vẫn còn trong thời hạn bảo hành ghi trên hệ thống.</li>
          <li><span class="condition-index">2</span>Có hóa đơn mua hàng hoặc cung cấp đúng mã đơn hàng / số điện thoại mua hàng.</li>
          <li><span class="condition-index">3</span>Hư hỏng được xác định do lỗi kỹ thuật của nhà sản xuất, không phải tác động ngoại lực.</li>
        </ul>
      </div>

      <div class="leaf-card">
        <img
          src="<?= e(asset('assets/images/warranty-policy/warranty-conditions.png')) ?>"
          alt="Minh họa điều kiện bảo hành"
        />
      </div>
    </div>
  </section>

  <section class="policy-section policy-section--soft">
    <div class="site-container">
      <div class="section-heading-center">
        <h2>Trường hợp không bảo hành</h2>
        <p>Các trường hợp dưới đây nằm ngoài phạm vi hỗ trợ miễn phí theo chính sách.</p>
      </div>

      <div class="exceptions-grid">
        <article class="exception-card exception-card--amber">
          <img src="<?= e(asset('assets/images/warranty-policy/icon-breakage.png')) ?>" alt="" />
          <h3>Tác động vật lý</h3>
          <p>Rơi vỡ, biến dạng gọng hoặc gãy kính do va chạm mạnh.</p>
        </article>
        <article class="exception-card exception-card--rose">
          <div class="exception-icon exception-icon--stripes" aria-hidden="true"></div>
          <h3>Trầy xước nặng</h3>
          <p>Bề mặt tròng kính trầy sâu hoặc bong lớp phủ do sử dụng sai cách.</p>
        </article>
        <article class="exception-card exception-card--sand">
          <img src="<?= e(asset('assets/images/warranty-policy/icon-temperature.png')) ?>" alt="" />
          <h3>Nhiệt độ cao</h3>
          <p>Sản phẩm bị ảnh hưởng bởi môi trường nóng, hóa chất hoặc ánh nắng gay gắt.</p>
        </article>
        <article class="exception-card exception-card--pink">
          <img src="<?= e(asset('assets/images/warranty-policy/icon-return.png')) ?>" alt="" />
          <h3>Tự ý sửa chữa</h3>
          <p>Sản phẩm đã được chỉnh sửa hoặc thay linh kiện tại nơi không thuộc ClearVision.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="process-section" id="process">
    <div class="site-container process-grid">
      <div class="process-main">
        <h2>Quy trình bảo hành</h2>
        <div class="process-steps">
          <div class="process-steps__line" aria-hidden="true"></div>
          <article class="process-step">
            <div class="process-step__icon" aria-hidden="true"><i class="fa-regular fa-paper-plane"></i></div>
            <h3>Gửi thông tin</h3>
            <p>Cung cấp mã đơn hàng hoặc số điện thoại mua hàng.</p>
          </article>
          <article class="process-step">
            <div class="process-step__icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></div>
            <h3>Kiểm tra</h3>
            <p>Đội ngũ kỹ thuật xác minh lỗi và tình trạng sản phẩm.</p>
          </article>
          <article class="process-step">
            <div class="process-step__icon" aria-hidden="true"><i class="fa-regular fa-message"></i></div>
            <h3>Kết quả</h3>
            <p>Thông báo phương án sửa chữa, đổi hàng hoặc phản hồi chi tiết.</p>
          </article>
          <article class="process-step">
            <div class="process-step__icon" aria-hidden="true"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <h3>Xử lý</h3>
            <p>Thay mới hoặc sửa chữa trong thời gian phù hợp.</p>
          </article>
        </div>
      </div>

      <aside class="process-side">
        <div class="process-side__icon" aria-hidden="true"><i class="fa-regular fa-clock"></i></div>
        <h3>Thời gian xử lý</h3>
        <p>Thời gian kiểm tra và xử lý trung bình từ <strong>03 đến 07 ngày làm việc</strong> tùy tình trạng linh kiện.</p>
        <a href="<?= e(url('/after-sales')) ?>">
          <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
          Gửi yêu cầu hậu mãi
        </a>
      </aside>
    </div>
  </section>

  <section class="support-section" id="support">
    <div class="site-container">
      <div class="section-heading-center">
        <h2>Cần hỗ trợ thêm?</h2>
        <p>Đội ngũ chăm sóc khách hàng của ClearVision luôn sẵn sàng giải đáp mọi thắc mắc của bạn.</p>
      </div>

      <div class="support-grid">
        <article class="support-card">
          <div class="support-card__icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></div>
          <span class="support-card__label">Hotline bảo hành</span>
          <strong>1900 1234</strong>
        </article>
        <article class="support-card">
          <div class="support-card__icon" aria-hidden="true"><i class="fa-regular fa-envelope"></i></div>
          <span class="support-card__label">Email hỗ trợ</span>
          <strong>support@clearvision.vn</strong>
        </article>
      </div>

      <div class="support-actions">
        <a class="btn-primary-custom" href="<?= e(url('/after-sales')) ?>">Liên hệ ngay</a>
      </div>
    </div>
  </section>
</main>
