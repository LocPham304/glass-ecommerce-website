<main class="about-main">
  <section class="about-hero">
    <div class="site-container about-hero__grid">
      <div class="about-hero__copy">
        <p class="about-kicker">CLEARVISION STORY</p>
        <h1>Về <span>ClearVision</span> - Hệ thống mua kính mắt trực tuyến hiện đại</h1>
        <p>
          Trải nghiệm mua sắm kính mắt dễ dàng, phong cách dành riêng cho bạn,
          tối ưu với chính sách minh bạch và quy trình đặt hàng theo từng nhu cầu.
        </p>
        <div class="about-hero__actions">
          <a class="btn-primary-custom" href="<?= e(url('/shop')) ?>">Khám phá ngay</a>
          <a class="btn-dark-custom" href="<?= e(url('/warranty')) ?>">Xem chính sách</a>
        </div>
      </div>

      <div class="about-hero__visual">
        <img
          src="<?= e(asset('assets/images/about-us/eyewear-display.png')) ?>"
          alt="Modern Eyewear Display"
        />
      </div>
    </div>
  </section>

  <section class="about-section">
    <div class="site-container">
      <div class="section-heading-center mission-heading">
        <h2>Sứ mệnh &amp; Tầm nhìn</h2>
      </div>

      <div class="about-dual-grid">
        <article class="about-card">
          <div class="about-card__icon">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </div>
          <div>
            <h3>Sứ mệnh</h3>
            <p>
              Mang đến trải nghiệm mua kính mắt trực tuyến thuận tiện, minh bạch,
              hỗ trợ cả mua có sẵn, pre-order và đơn cắt kính theo toa.
            </p>
          </div>
        </article>

        <article class="about-card">
          <div class="about-card__icon">
            <i class="fa-regular fa-eye"></i>
          </div>
          <div>
            <h3>Tầm nhìn</h3>
            <p>
              Trở thành hệ thống kính mắt được khách hàng tin chọn nhờ dịch vụ tận tâm,
              hậu mãi rõ ràng và trải nghiệm cá nhân hóa chỉn chu.
            </p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="about-section about-section--tight">
    <div class="site-container white-background">
      <div class="section-heading-center values-heading">
        <h2>Giá trị cốt lõi</h2>
      </div>

      <div class="values-grid">
        <article class="value-item">
          <div class="value-item__icon">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h3>Minh bạch</h3>
          <p>Cam kết rõ ràng về sản phẩm, bảo hành, đổi trả và tiến độ xử lý đơn hàng.</p>
        </article>
        <article class="value-item">
          <div class="value-item__icon">
            <i class="fa-regular fa-gem"></i>
          </div>
          <h3>Chất lượng</h3>
          <p>Mỗi sản phẩm đều được quản lý dữ liệu, tồn kho và trạng thái xử lý nhất quán.</p>
        </article>
        <article class="value-item">
          <div class="value-item__icon">
            <i class="fa-regular fa-heart"></i>
          </div>
          <h3>Khách hàng là trung tâm</h3>
          <p>Khách hàng có thể tự quản lý tài khoản, đơn hàng và yêu cầu hậu mãi ngay trên hệ thống.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="about-section">
    <div class="site-container">
      <div class="section-heading-center values-heading">
        <h2>Điểm nổi bật của hệ thống</h2>
      </div>

      <div class="features-grid">
        <article class="feature-card">
          <div class="feature-card__icon">
            <i class="fa-solid fa-bolt"></i>
          </div>
          <h3>Mua online nhanh chóng</h3>
          <p>Quy trình mua hàng đơn giản, dữ liệu giỏ hàng và checkout đồng bộ với MySQL.</p>
        </article>
        <article class="feature-card">
          <div class="feature-card__icon">
            <i class="fa-solid fa-truck-fast"></i>
          </div>
          <h3>Theo dõi đơn hàng dễ dàng</h3>
          <p>Hiển thị lịch sử trạng thái, tracking vận chuyển và thông tin pre-order rõ ràng.</p>
        </article>
        <article class="feature-card">
          <div class="feature-card__icon">
            <i class="fa-solid fa-glasses"></i>
          </div>
          <h3>Đặt kính theo yêu cầu</h3>
          <p>Hỗ trợ nhập thông số mắt, duyệt toa và theo dõi workflow prescription.</p>
        </article>
        <article class="feature-card">
          <div class="feature-card__icon">
            <i class="fa-regular fa-star"></i>
          </div>
          <h3>Hậu mãi minh bạch</h3>
          <p>Yêu cầu đổi trả, bảo hành, hoàn tiền được ghi nhận và xử lý trực tiếp trên hệ thống.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="about-section about-section--team" id="team">
    <div class="site-container">
      <div class="section-heading-center">
        <h2>Đội ngũ phát triển</h2>
      </div>

      <div class="team-grid">
        <article class="team-member">
          <img src="<?= e(asset('assets/images/about-us/member-nguyen-van-a.png')) ?>" alt="Nguyễn Văn A" />
          <h3>Nguyễn Văn A</h3>
          <p>Founder</p>
        </article>
        <article class="team-member">
          <img src="<?= e(asset('assets/images/about-us/member-tran-thi-b.png')) ?>" alt="Trần Thị B" />
          <h3>Trần Thị B</h3>
          <p>Marketing Lead</p>
        </article>
        <article class="team-member">
          <img src="<?= e(asset('assets/images/about-us/member-le-van-c.png')) ?>" alt="Lê Văn C" />
          <h3>Lê Văn C</h3>
          <p>Product Manager</p>
        </article>
        <article class="team-member">
          <img src="<?= e(asset('assets/images/about-us/member-pham-thi-d.png')) ?>" alt="Phạm Thị D" />
          <h3>Phạm Thị D</h3>
          <p>Design Lead</p>
        </article>
        <article class="team-member">
          <img src="<?= e(asset('assets/images/about-us/member-hoang-van-e.png')) ?>" alt="Hoàng Văn E" />
          <h3>Hoàng Văn E</h3>
          <p>Developer</p>
        </article>
      </div>
    </div>
  </section>

  <section class="about-cta">
    <div class="site-container">
      <div class="about-cta__banner">
        <div class="about-cta__copy">
          <h2>Sẵn sàng chọn cho mình chiếc kính hoàn hảo?</h2>
          <p>Khám phá bộ sưu tập mới nhất với ưu đãi lên đến 20%.</p>
        </div>
        <div class="about-cta__actions">
          <a class="cta-light-button" href="<?= e(url('/shop')) ?>">Khám phá sản phẩm</a>
          <a class="cta-dark-button" href="<?= e(url('/warranty')) ?>">Xem chính sách</a>
        </div>
      </div>
    </div>
  </section>
</main>
