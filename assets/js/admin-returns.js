function initAdminReturnsPage() {
  const searchInput = document.querySelector("[data-return-search]");
  const keywordFilter = document.querySelector("[data-filter-return-keyword]");
  const typeFilter = document.querySelector("[data-filter-return-type]");
  const statusFilter = document.querySelector("[data-filter-return-status]");
  const dateFilter = document.querySelector("[data-filter-return-date]");
  const rows = document.querySelectorAll(".return-row");
  const summary = document.querySelector("[data-return-summary]");
  const emptyState = document.querySelector("[data-return-empty]");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

  let toastTimeout;

  const badgeClassMap = {
    "Mới tạo": "status-pill--pending",
    "Đang xác minh": "status-pill--processing",
    "Đã duyệt": "status-pill--active",
    "Từ chối": "status-pill--cancelled",
    "Hoàn tất": "status-pill--shipping",
  };

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

  const renderRowStatus = (row, statusText) => {
    const badge = row.querySelector("[data-return-status-badge]");
    if (!badge) return;

    row.dataset.status = statusText;
    badge.className = `status-pill ${badgeClassMap[statusText] || "status-pill--pending"}`;
    badge.textContent = statusText;
  };

  const applyFilters = () => {
    const search = normalize(searchInput?.value || "");
    const keyword = normalize(keywordFilter?.value || "");
    const type = typeFilter?.value || "all";
    const status = statusFilter?.value || "all";
    const date = dateFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowKeyword = normalize(row.dataset.keyword || "");
      const rowType = row.dataset.type || "";
      const rowStatus = row.dataset.status || "";
      const rowDate = row.dataset.date || "";

      const matchesSearch = !search || rowKeyword.includes(search);
      const matchesKeyword = !keyword || rowKeyword.includes(keyword);
      const matchesType = type === "all" || rowType === type;
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesDate = date === "all" || rowDate === date;
      const isVisible = matchesSearch && matchesKeyword && matchesType && matchesStatus && matchesDate;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (summary) {
      summary.textContent = `Hien thi ${visibleCount ? 1 : 0} - ${visibleCount} trong so 36 yeu cau doi tra`;
    }

    if (emptyState) {
      emptyState.hidden = visibleCount > 0;
    }
  };

  [searchInput, keywordFilter, typeFilter, statusFilter, dateFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  rows.forEach((row) => {
    const select = row.querySelector("[data-return-status-select]");
    const saveButton = row.querySelector("[data-return-status-save]");

    renderRowStatus(row, row.dataset.status || "Mới tạo");

    saveButton?.addEventListener("click", () => {
      if (!select) return;
      renderRowStatus(row, select.value);
      applyFilters();
      const requestCode = row.querySelector(".return-code strong")?.textContent || "yêu cầu";
      showToast(`Đã cập nhật trạng thái cho ${requestCode}.`);
    });
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminReturnsPage);


