function initAdminReportsPage() {
  const yearSelect = document.querySelector("[data-report-year]");
  const rangeButtons = document.querySelectorAll("[data-report-range]");
  const topProductsEl = document.querySelector("[data-top-products]");
  const dailyEl = document.querySelector("[data-summary-daily]");
  const monthlyEl = document.querySelector("[data-summary-monthly]");
  const yearlyEl = document.querySelector("[data-summary-yearly]");

  const fallbackPayload = {
    2026: {
      summary: {
        daily: "42.500.000d",
        monthly: "1.280.000.000d",
        yearly: "12.480.000.000d",
      },
      chart: {
        daily: {
          labels: ["01/10", "05/10", "10/10", "15/10", "20/10", "25/10", "30/10"],
          values: [18, 26, 22, 31, 28, 36, 32],
        },
        monthly: {
          labels: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"],
          values: [680, 720, 810, 905, 960, 1010, 1080, 1150, 1120, 1280, 1340, 1515],
        },
        yearly: {
          labels: ["2022", "2023", "2024", "2025", "2026"],
          values: [5.2, 7.4, 8.8, 10.56, 12.48],
        },
      },
      topProducts: [
        { name: "Ray-Ban Clubmaster Classic", sku: "RB-3016-W0365", sold: 154, revenue: "654.000.000d" },
        { name: "Combo Gọng + Tròng Smart Vision", sku: "CV-COMBO-01", sold: 128, revenue: "592.000.000d" },
        { name: "Prada Heritage Oversized", sku: "PR-543-ST", sold: 96, revenue: "648.000.000d" },
        { name: "Titanium Slim Frame", sku: "TI-SLIM-88", sold: 91, revenue: "423.000.000d" },
        { name: "Classic Aviator", sku: "AV-CLASSIC-02", sold: 88, revenue: "398.000.000d" },
      ],
    },
    2025: {
      summary: {
        daily: "35.800.000d",
        monthly: "1.110.000.000d",
        yearly: "10.560.000.000d",
      },
      chart: {
        daily: {
          labels: ["01/10", "05/10", "10/10", "15/10", "20/10", "25/10", "30/10"],
          values: [14, 21, 18, 24, 20, 27, 25],
        },
        monthly: {
          labels: ["T1", "T2", "T3", "T4", "T5", "T6", "T7", "T8", "T9", "T10", "T11", "T12"],
          values: [550, 610, 690, 760, 805, 860, 910, 945, 980, 1110, 1110, 1330],
        },
        yearly: {
          labels: ["2021", "2022", "2023", "2024", "2025"],
          values: [4.1, 5.2, 7.4, 8.8, 10.56],
        },
      },
      topProducts: [
        { name: "Gucci Square Frame Glasses", sku: "GC-1092-BB8", sold: 121, revenue: "538.000.000d" },
        { name: "Ray-Ban Wayfarer Tortoise", sku: "RB-2140-902", sold: 110, revenue: "434.000.000d" },
        { name: "Classic Aviator", sku: "AV-CLASSIC-02", sold: 94, revenue: "398.000.000d" },
        { name: "Titanium Slim Frame", sku: "TI-SLIM-88", sold: 88, revenue: "366.000.000d" },
        { name: "Oakley Holbrook Prizm", sku: "OK-HB-PR", sold: 80, revenue: "322.000.000d" },
      ],
    },
  };

  const reportPayload = window.adminReportPayload || fallbackPayload;
  let activeRange = "daily";
  let revenueChart;

  const getCurrentData = () => reportPayload[yearSelect?.value || "2026"] || reportPayload["2026"];

  const renderSummary = (data) => {
    if (dailyEl) dailyEl.textContent = data.summary.daily;
    if (monthlyEl) monthlyEl.textContent = data.summary.monthly;
    if (yearlyEl) yearlyEl.textContent = data.summary.yearly;
  };

  const renderTopProducts = (data) => {
    if (!topProductsEl) return;
    topProductsEl.innerHTML = data.topProducts
      .slice(0, 5)
      .map(
        (item, index) => `
          <article class="report-top-product">
            <div class="report-top-product__rank">${index + 1}</div>
            <div>
              <strong>${item.name}</strong>
              <p>SKU: ${item.sku}</p>
            </div>
            <div class="report-top-product__meta">
              <span class="report-top-product__sold">Đã bán: ${item.sold}</span>
              <span class="report-top-product__revenue">${item.revenue}</span>
            </div>
          </article>
        `,
      )
      .join("");
  };

  const createChart = (data) => {
    const canvas = document.getElementById("adminRevenueChart");
    if (!canvas || !window.Chart) return;

    const chartData = data.chart[activeRange];
    if (!chartData) return;

    if (revenueChart) {
      revenueChart.destroy();
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
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: "#233843",
            titleFont: {
              family: "Public Sans",
              weight: "700",
            },
            bodyFont: {
              family: "Public Sans",
            },
            padding: 12,
            displayColors: false,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#97a4b0",
              font: {
                family: "Public Sans",
                size: 12,
                weight: "600",
              },
            },
            border: {
              display: false,
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              color: "#eef2f6",
              drawBorder: false,
            },
            ticks: {
              color: "#97a4b0",
              font: {
                family: "Public Sans",
                size: 12,
                weight: "600",
              },
            },
            border: {
              display: false,
            },
          },
        },
      },
    });
  };

  const render = () => {
    const data = getCurrentData();
    if (!data) return;
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

document.addEventListener("DOMContentLoaded", initAdminReportsPage);


