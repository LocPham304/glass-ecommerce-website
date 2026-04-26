<?php
$eligibleOrders = array_values(array_filter($orders ?? [], static function (array $order): bool {
    return in_array($order['order_status'] ?? '', ['delivered', 'after_sales'], true);
}));

$currentUser = $user ?? auth_user() ?? [];
$orderCodeMap = [];
foreach ($eligibleOrders as $order) {
    $orderCodeMap[(string) $order['order_code']] = (string) $order['id'];
}

$requestTypeLabels = [
    'exchange' => 'Đổi hàng',
    'refund' => 'Hoàn tiền',
    'warranty' => 'Bảo hành',
];

$requestStatusLabels = [
    'pending' => 'Chờ xử lý',
    'processing' => 'Đang xử lý',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy',
];
?>

<main class="refund-main">
  <section class="refund-hero">
    <div class="site-container refund-hero__inner">
      <h1>Đổi trả - Hoàn tiền</h1>
      <p>
        Chúng tôi luôn nỗ lực mang lại trải nghiệm tốt nhất. Nếu sản phẩm không đúng như mong đợi,
        chỉ cần điền thông tin đơn hàng để được hỗ trợ nhanh chóng.
      </p>
    </div>
  </section>

  <section class="refund-section refund-request">
    <div class="site-container refund-request__grid">
      <div class="refund-form-card">
        <h2>
          <i class="fa-solid fa-list-check" aria-hidden="true"></i>
          Thông tin yêu cầu
        </h2>

        <form class="refund-form" method="POST" action="<?= e(url('/after-sales')) ?>">
          <div class="refund-form__row refund-form__row--two">
            <label class="field">
              <span>Số điện thoại <em>*</em></span>
              <input
                type="tel"
                value="<?= e($currentUser['phone'] ?? '') ?>"
                placeholder="0901 234 567"
                readonly
              />
            </label>

            <label class="field">
              <span>Mã đơn hàng <em>*</em></span>
              <input
                type="text"
                name="order_code"
                list="eligible-order-codes"
                placeholder="VD: CV123456"
                <?= $eligibleOrders === [] ? 'disabled' : '' ?>
              />
              <datalist id="eligible-order-codes">
                <?php foreach ($eligibleOrders as $order): ?>
                  <option value="<?= e($order['order_code']) ?>">
                    #<?= e($order['order_code']) ?> - <?= e(format_currency($order['total_amount'])) ?>
                  </option>
                <?php endforeach; ?>
              </datalist>
            </label>
          </div>

          <input type="hidden" name="order_id" value="" />

          <label class="field">
            <span>Email nhận thông báo</span>
            <input
              type="email"
              value="<?= e($currentUser['email'] ?? '') ?>"
              placeholder="customer@example.com"
              readonly
            />
          </label>

          <div class="field">
            <span>Loại yêu cầu <em>*</em></span>
            <div class="radio-group">
              <label><input type="radio" name="request_type" value="exchange" checked /> Đổi hàng</label>
              <label><input type="radio" name="request_type" value="refund" /> Hoàn tiền</label>
              <label><input type="radio" name="request_type" value="warranty" /> Bảo hành</label>
            </div>
          </div>

          <label class="field">
            <span>Lý do yêu cầu <em>*</em></span>
            <textarea
              rows="4"
              name="reason"
              placeholder="Ví dụ: Gọng kính bị lỏng, tròng kính bị trầy..."
            ></textarea>
          </label>

          <label class="field">
            <span>Ghi chú thêm</span>
            <textarea
              rows="3"
              name="description"
              placeholder="Thông tin bổ sung (nếu có)"
            ></textarea>
          </label>

          <?php if ($eligibleOrders === []): ?>
            <p class="small text-secondary mb-0">
              Hiện chưa có đơn hoàn thành nào đủ điều kiện để gửi yêu cầu đổi trả hoặc hoàn tiền.
            </p>
          <?php endif; ?>

          <button class="submit-button" type="submit" <?= $eligibleOrders === [] ? 'disabled' : '' ?>>
            Gửi yêu cầu
            <i class="fa-regular fa-paper-plane" aria-hidden="true"></i>
          </button>
        </form>
      </div>

      <aside class="refund-help">
        <article class="help-panel help-panel--dark">
          <h3>
            <i class="fa-regular fa-lightbulb" aria-hidden="true"></i>
            Hướng dẫn nhanh
          </h3>
          <ul class="help-panel__list">
            <li>
              <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
              <span>Vui lòng nhập đúng số điện thoại đã sử dụng khi đặt hàng.</span>
            </li>
            <li>
              <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
              <span>Bộ phận CSKH sẽ liên hệ với bạn trong vòng 24-48h làm việc.</span>
            </li>
            <li>
              <i class="fa-regular fa-clipboard" aria-hidden="true"></i>
              <span>Vui lòng giữ nguyên bao bì, tem mác và hóa đơn của sản phẩm.</span>
            </li>
          </ul>
        </article>

        <article class="help-panel">
          <h3>Cần hỗ trợ gấp?</h3>
          <a class="help-chip help-chip--hotline" href="tel:19001234">
            <i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
            Hotline: 1900 1234
          </a>
          <a class="help-chip help-chip--chat" href="#">
            <i class="fa-regular fa-message" aria-hidden="true"></i>
            Chat qua Facebook
          </a>
        </article>

        <article class="help-panel">
          <h3>Yêu cầu đã gửi</h3>
          <?php if (($afterSalesRequests ?? []) === []): ?>
            <p class="mb-0">Bạn chưa có yêu cầu nào.</p>
          <?php else: ?>
            <?php foreach (array_slice($afterSalesRequests, 0, 3) as $request): ?>
              <div class="border rounded-4 p-3 mb-3 bg-white">
                <strong>#<?= e($request['order_code']) ?></strong>
                <p class="mb-1">
                  <?= e($requestTypeLabels[$request['request_type'] ?? ''] ?? ucfirst((string) $request['request_type'])) ?>
                  ·
                  <?= e($requestStatusLabels[$request['status'] ?? ''] ?? (string) $request['status']) ?>
                </p>
                <p class="small text-secondary mb-0"><?= e($request['reason'] ?: 'Chưa có mô tả thêm') ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </article>
      </aside>
    </div>
  </section>

  <section class="refund-section">
    <div class="site-container">
      <div class="section-heading-center refund-heading">
        <h2>Quy trình xử lý</h2>
      </div>

      <div class="refund-steps">
        <article class="refund-step">
          <div class="refund-step__icon">
            <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
          </div>
          <h3>1. GỬI YÊU CẦU</h3>
          <p>Hoàn thành biểu mẫu và gửi yêu cầu.</p>
        </article>
        <article class="refund-step">
          <div class="refund-step__icon">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
          </div>
          <h3>2. KIỂM TRA</h3>
          <p>Bộ phận hỗ trợ xác minh thông tin.</p>
        </article>
        <article class="refund-step">
          <div class="refund-step__icon">
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
          </div>
          <h3>3. LIÊN HỆ</h3>
          <p>CSKH sẽ liên hệ phương án xử lý.</p>
        </article>
        <article class="refund-step">
          <div class="refund-step__icon">
            <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
          </div>
          <h3>4. KẾT QUẢ</h3>
          <p>Gửi xác nhận đổi hoặc hoàn tiền.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="refund-section refund-section--soft">
    <div class="site-container">
      <div class="refund-benefits">
        <article class="benefit-card">
          <div class="benefit-card__icon">
            <i class="fa-solid fa-box-open" aria-hidden="true"></i>
          </div>
          <h3>Đổi trả sản phẩm lỗi</h3>
          <p>
            Đổi mới 1-1 trong vòng 7 ngày nếu sản phẩm có lỗi từ nhà sản xuất
            hoặc không đúng mô tả.
          </p>
        </article>

        <article class="benefit-card">
          <div class="benefit-card__icon">
            <i class="fa-regular fa-money-bill-1" aria-hidden="true"></i>
          </div>
          <h3>Hoàn tiền đủ điều kiện</h3>
          <p>
            Hoàn tiền 100% giá trị sản phẩm nếu không đúng cam kết hoặc thuộc
            trường hợp đã xác minh.
          </p>
        </article>

        <article class="benefit-card">
          <div class="benefit-card__icon">
            <i class="fa-regular fa-shield-heart" aria-hidden="true"></i>
          </div>
          <h3>Hỗ trợ minh bạch</h3>
          <p>
            Mọi yêu cầu đều được cập nhật rõ ràng qua email hoặc hotline trong
            suốt quá trình xử lý.
          </p>
        </article>
      </div>
    </div>
  </section>

  <section class="refund-section faq-section">
    <div class="site-container faq-wrap">
      <div class="section-heading-center refund-heading">
        <h2>Câu hỏi thường gặp</h2>
      </div>

      <div class="faq-list">
        <article class="faq-item is-open">
          <button class="faq-trigger" type="button" aria-expanded="true">
            <span>Không có tài khoản có đăng được không?</span>
            <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
          </button>
          <div class="faq-content">
            <p>
              Bạn có thể xem chính sách công khai mà không cần đăng nhập, nhưng để gửi yêu cầu xử lý
              thực tế trên hệ thống, vui lòng đăng nhập bằng tài khoản đã dùng để mua hàng.
            </p>
          </div>
        </article>

        <article class="faq-item">
          <button class="faq-trigger" type="button" aria-expanded="false">
            <span>Bao lâu thì tôi được phản hồi yêu cầu?</span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="faq-content">
            <p>
              Chúng tôi thường phản hồi trong vòng 24 giờ làm việc kể từ thời điểm nhận đủ thông tin hợp lệ.
            </p>
          </div>
        </article>

        <article class="faq-item">
          <button class="faq-trigger" type="button" aria-expanded="false">
            <span>Tôi có cần giữ hóa đơn mua hàng không?</span>
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="faq-content">
            <p>
              Có. Hóa đơn hoặc mã đơn hàng giúp xác minh nhanh hơn và rút ngắn thời gian xử lý yêu cầu.
            </p>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>

<script>
  window.afterSalesOrderCodeMap = <?= json_encode($orderCodeMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
</script>