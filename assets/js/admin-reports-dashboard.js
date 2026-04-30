function initAdminReportsDashboard() {
  const yearSelect = document.querySelector("[data-report-year]");
  const rangeButtons = document.querySelectorAll("[data-report-range]");
  const topProductsEl = document.querySelector("[data-top-products]");
  const dailyEl = document.querySelector("[data-summary-daily]");
  const monthlyEl = document.querySelector("[data-summary-monthly]");
  const yearlyEl = document.querySelector("[data-summary-yearly]");
  const emptyChart = document.querySelector("[data-report-empty-chart]");
  const canvas = document.getElementById("adminRevenueChart");

  const reportPayload = window.adminReportPayload || {};
  let activeRange = "daily";
  let revenueChart;

  const getCurrentData = () => {
    const selectedYear = yearSelect?.value || Object.keys(reportPayload)[0];
    return reportPayload[selectedYear] || reportPayload[Object.keys(reportPayload)[0]];
  };

  const hasChartData = (chartData) =>
    Array.isArray(chartData?.labels) &&
    Array.isArray(chartData?.values) &&
    chartData.labels.length > 0 &&
    chartData.values.some((value) => Number(value) > 0);

  const escapeHtml = (value) =>
    String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

  const renderSummary = (data) => {
    if (dailyEl) dailyEl.textContent = data?.summary?.daily || "0đ";
    if (monthlyEl) monthlyEl.textContent = data?.summary?.monthly || "0đ";
    if (yearlyEl) yearlyEl.textContent = data?.summary?.yearly || "0đ";
  };

  const renderTopProducts = (data) => {
    if (!topProductsEl) return;

    const products = Array.isArray(data?.topProducts) ? data.topProducts.slice(0, 5) : [];

    if (products.length === 0) {
      topProductsEl.innerHTML = `
        <div class="report-empty-list">
          <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
          <strong>Chưa có sản phẩm bán chạy</strong>
          <span>Dữ liệu sẽ được cập nhật khi có đơn hàng trong năm này.</span>
        </div>
      `;
      return;
    }

    topProductsEl.innerHTML = products
      .map(
        (item, index) => `
          <article class="report-top-product">
            <div class="report-top-product__rank">${index + 1}</div>
            <div>
              <strong>${escapeHtml(item.name || "Sản phẩm")}</strong>
              <p>SKU: ${escapeHtml(item.sku || "N/A")}</p>
            </div>
            <div class="report-top-product__meta">
              <span class="report-top-product__sold">Đã bán: ${escapeHtml(item.sold || 0)}</span>
              <span class="report-top-product__revenue">${escapeHtml(item.revenue || "0đ")}</span>
            </div>
          </article>
        `,
      )
      .join("");
  };

  const createChart = (data) => {
    if (!canvas || !window.Chart) return;

    const chartData = data?.chart?.[activeRange] || { labels: [], values: [] };
    const shouldShowChart = hasChartData(chartData);

    if (emptyChart) {
      emptyChart.hidden = shouldShowChart;
    }
    canvas.hidden = !shouldShowChart;

    if (revenueChart) {
      revenueChart.destroy();
      revenueChart = null;
    }

    if (!shouldShowChart) {
      return;
    }

    revenueChart = new window.Chart(canvas, {
      type: "line",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            data: chartData.values,
            label: "Doanh thu",
            borderColor: "#f97316",
            backgroundColor: "rgba(249, 115, 22, 0.12)",
            pointBackgroundColor: "#ffffff",
            pointBorderColor: "#f97316",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 5,
            tension: 0.38,
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#233843",
            titleFont: { family: "Public Sans", weight: "700" },
            bodyFont: { family: "Public Sans" },
            padding: 12,
            displayColors: false,
            callbacks: {
              label: (context) => `${context.parsed.y.toLocaleString("vi-VN")} triệu đồng`,
            },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: "#97a4b0",
              font: { family: "Public Sans", size: 12, weight: "600" },
            },
            border: { display: false },
          },
          y: {
            beginAtZero: true,
            grid: { color: "#eef2f6", drawBorder: false },
            ticks: {
              color: "#97a4b0",
              font: { family: "Public Sans", size: 12, weight: "600" },
              callback: (value) => `${value}tr`,
            },
            border: { display: false },
          },
        },
      },
    });
  };

  const render = () => {
    const data = getCurrentData();
    renderSummary(data);
    renderTopProducts(data);
    createChart(data);
  };

  yearSelect?.addEventListener("change", render);

  rangeButtons.forEach((button) => {
    button.addEventListener("click", () => {
      rangeButtons.forEach((item) => item.classList.remove("is-active"));
      button.classList.add("is-active");
      activeRange = button.dataset.reportRange || "daily";
      render();
    });
  });

  render();
}

document.addEventListener("DOMContentLoaded", initAdminReportsDashboard);
