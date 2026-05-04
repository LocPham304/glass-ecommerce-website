<?php
$reportPayload = $reportPayload ?? [];
$years = array_keys($reportPayload);
$selectedYear = $years[0] ?? (string) date('Y');
?>

<header class="admin-topbar admin-topbar--products">
  <div class="admin-products-topbar__title">
    <h1>Báo cáo</h1>
  </div>

  <label class="admin-products-search admin-products-search--compact" aria-label="Chọn năm báo cáo">
    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
    <select data-report-year>
      <?php foreach ($years as $year): ?>
        <option value="<?= e((string) $year) ?>" <?= (string) $year === (string) $selectedYear ? 'selected' : '' ?>>
          Năm <?= e((string) $year) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <button
    class="admin-primary-button admin-primary-button--topbar"
    type="button"
    data-coming-soon="Xuất báo cáo"
  >
    <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
    Xuất báo cáo
  </button>
</header>

<section class="admin-content admin-content--reports">
  <div class="container-fluid px-0">
    <div class="row g-4 report-summary-row">
      <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm report-summary-card h-100">
          <div class="card-body">
            <div class="report-summary-card__icon">
              <i class="fa-regular fa-sun" aria-hidden="true"></i>
            </div>
            <p class="report-summary-card__label">Doanh thu hôm nay</p>
            <strong data-summary-daily>0đ</strong>
            <span>Doanh thu ghi nhận trong ngày hiện tại.</span>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm report-summary-card h-100">
          <div class="card-body">
            <div class="report-summary-card__icon">
              <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
            </div>
            <p class="report-summary-card__label">Doanh thu tháng</p>
            <strong data-summary-monthly>0đ</strong>
            <span>Tổng doanh thu theo tháng đang chọn.</span>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm report-summary-card h-100">
          <div class="card-body">
            <div class="report-summary-card__icon">
              <i class="fa-regular fa-chart-bar" aria-hidden="true"></i>
            </div>
            <p class="report-summary-card__label">Doanh thu năm</p>
            <strong data-summary-yearly>0đ</strong>
            <span>Tổng cộng doanh thu của cả năm báo cáo.</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 report-main-row">
      <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm report-panel-card h-100">
          <div class="card-body report-panel-card__body">
            <div class="report-panel-card__head">
              <div>
                <h2>Biểu đồ doanh thu</h2>
                <p>Dữ liệu được tổng hợp từ các đơn hàng thực tế trong hệ thống.</p>
              </div>

              <div class="btn-group report-range-group" role="group" aria-label="Mốc thời gian báo cáo">
                <button class="btn is-active" type="button" data-report-range="daily">Hằng ngày</button>
                <button class="btn" type="button" data-report-range="monthly">Hằng tháng</button>
                <button class="btn" type="button" data-report-range="yearly">Hằng năm</button>
              </div>
            </div>

            <div class="report-canvas-wrap">
              <canvas id="adminRevenueChart" height="320" aria-label="Biểu đồ doanh thu"></canvas>
              <div class="report-empty-chart" data-report-empty-chart hidden>
                <i class="fa-regular fa-chart-bar" aria-hidden="true"></i>
                <strong>Chưa có dữ liệu doanh thu</strong>
                <span>Biểu đồ sẽ xuất hiện khi năm được chọn có đơn hàng.</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm report-panel-card h-100">
          <div class="card-body report-panel-card__body">
            <div class="report-panel-card__head report-panel-card__head--stack">
              <div>
                <h2>Top 5 sản phẩm bán chạy</h2>
                <p>Xếp hạng theo số lượng bán ra và doanh thu.</p>
              </div>
            </div>

            <div class="report-top-products" data-top-products></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  window.adminReportPayload = <?= json_encode($reportPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
