function initAdminOrdersPage() {
  const searchInput = document.querySelector("[data-order-search]");
  const keywordFilter = document.querySelector("[data-filter-keyword]");
  const statusFilter = document.querySelector("[data-filter-order-status]");
  const paymentFilter = document.querySelector("[data-filter-payment]");
  const dateFilter = document.querySelector("[data-filter-date]");
  const rows = Array.from(document.querySelectorAll(".order-admin-row"));
  const summary = document.querySelector("[data-order-summary]");

  if (rows.length === 0) {
    return;
  }

  const normalize = (value) => value.trim().toLowerCase();
  const totalRows = rows.length;

  const isWithinDateFilter = (rowTimestamp, filterValue) => {
    if (!rowTimestamp || filterValue === "all") {
      return true;
    }

    const now = new Date();
    const rowDate = new Date(rowTimestamp * 1000);

    if (Number.isNaN(rowDate.getTime())) {
      return filterValue === "all";
    }

    if (filterValue === "today") {
      return rowDate.toDateString() === now.toDateString();
    }

    const diffInDays = (now.getTime() - rowDate.getTime()) / (1000 * 60 * 60 * 24);

    if (filterValue === "week") {
      return diffInDays <= 7;
    }

    if (filterValue === "month") {
      return diffInDays <= 30;
    }

    return true;
  };

  const updateSummary = (visibleCount) => {
    if (!summary) {
      return;
    }

    if (visibleCount === 0) {
      summary.textContent = `Hiển thị 0 trong số ${totalRows} đơn hàng`;
      return;
    }

    summary.textContent = `Hiển thị 1 - ${visibleCount} trong số ${totalRows} đơn hàng`;
  };

  const applyFilters = () => {
    const search = normalize(searchInput?.value || "");
    const keyword = normalize(keywordFilter?.value || "");
    const status = statusFilter?.value || "all";
    const payment = paymentFilter?.value || "all";
    const date = dateFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowKeyword = normalize(row.dataset.keyword || "");
      const rowStatus = row.dataset.status || "";
      const rowPayment = row.dataset.payment || "";
      const rowTimestamp = Number(row.dataset.createdAt || 0);

      const matchesSearch = !search || rowKeyword.includes(search);
      const matchesKeyword = !keyword || rowKeyword.includes(keyword);
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesPayment = payment === "all" || rowPayment === payment;
      const matchesDate = isWithinDateFilter(rowTimestamp, date);
      const isVisible = matchesSearch && matchesKeyword && matchesStatus && matchesPayment && matchesDate;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    updateSummary(visibleCount);
  };

  [searchInput, keywordFilter, statusFilter, paymentFilter, dateFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  rows.forEach((row) => {
    const select = row.querySelector("[data-order-status-select]");
    const saveButton = row.querySelector("[data-order-status-save]");

    if (!select || !saveButton) {
      return;
    }

    const initialValue = select.value;
    saveButton.disabled = true;

    select.addEventListener("change", () => {
      saveButton.disabled = select.value === initialValue;
    });
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminOrdersPage);

function initAdminOrdersPageLegacy() {
  const searchInput = document.querySelector("[data-order-search]");
  const keywordFilter = document.querySelector("[data-filter-keyword]");
  const statusFilter = document.querySelector("[data-filter-order-status]");
  const paymentFilter = document.querySelector("[data-filter-payment]");
  const dateFilter = document.querySelector("[data-filter-date]");
  const rows = document.querySelectorAll(".order-admin-row");
  const summary = document.querySelector("[data-order-summary]");
  const statusStore = window.OrderStatusStore;
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

  let toastTimeout;

  const normalize = (value) => value.trim().toLowerCase();

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  const renderRowStatus = (row) => {
    if (!statusStore) return;

    const { STATUS_META, getStatus } = statusStore;
    const adminBadgeClassMap = {
      confirmed: "status-pill--pending",
      processing: "status-pill--processing",
      shipping: "status-pill--shipping",
      delivered: "status-pill--active",
      cancelled: "status-pill--cancelled",
    };
    const code = row.getAttribute("data-order-code");
    const statusKey = getStatus(code);
    const meta = STATUS_META[statusKey];
    const badge = row.querySelector("[data-order-status-badge]");
    const select = row.querySelector("[data-order-status-select]");

    if (!meta || !badge || !select) return;

    row.dataset.status = meta.adminLabel;
    badge.className = `status-pill ${adminBadgeClassMap[statusKey] || "status-pill--pending"}`;
    badge.textContent = meta.adminLabel;
    select.value = statusKey;
  };

  const applyFilters = () => {
    const search = normalize(searchInput?.value || "");
    const keyword = normalize(keywordFilter?.value || "");
    const status = statusFilter?.value || "all";
    const payment = paymentFilter?.value || "all";
    const date = dateFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowKeyword = normalize(row.dataset.keyword || "");
      const rowStatus = row.dataset.status || "";
      const rowPayment = row.dataset.payment || "";
      const rowDate = row.dataset.date || "";

      const matchesSearch = !search || rowKeyword.includes(search);
      const matchesKeyword = !keyword || rowKeyword.includes(keyword);
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesPayment = payment === "all" || rowPayment === payment;
      const matchesDate = date === "all" || rowDate === date;

      const isVisible = matchesSearch && matchesKeyword && matchesStatus && matchesPayment && matchesDate;
      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (summary) {
      summary.textContent = `Hiển thị 1 - ${visibleCount} trong số 86 đơn hàng`;
    }
  };

  [searchInput, keywordFilter, statusFilter, paymentFilter, dateFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  rows.forEach((row) => {
    renderRowStatus(row);

    const saveButton = row.querySelector("[data-order-status-save]");
    const select = row.querySelector("[data-order-status-select]");
    const code = row.getAttribute("data-order-code");

    saveButton?.addEventListener("click", () => {
      if (!statusStore || !select || !code) return;
      statusStore.setStatus(code, select.value);
      renderRowStatus(row);
      applyFilters();
      showToast(`Đã cập nhật trạng thái đơn ${code} thành công.`);
    });
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", () => {});


