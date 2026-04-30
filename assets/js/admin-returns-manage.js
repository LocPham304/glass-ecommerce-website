function initAdminReturnsManagePage() {
  const searchInput = document.querySelector("[data-return-search]");
  const keywordFilter = document.querySelector("[data-filter-return-keyword]");
  const typeFilter = document.querySelector("[data-filter-return-type]");
  const statusFilter = document.querySelector("[data-filter-return-status]");
  const dateFilter = document.querySelector("[data-filter-return-date]");
  const rows = Array.from(document.querySelectorAll(".return-row"));
  const summary = document.querySelector("[data-return-summary]");
  const emptyState = document.querySelector("[data-return-empty]");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

  let toastTimeout;

  const badgeClassMap = {
    pending: "status-pill--pending",
    processing: "status-pill--processing",
    approved: "status-pill--active",
    resolved: "status-pill--shipping",
    rejected: "status-pill--cancelled",
  };

  const statusLabelMap = {
    pending: "Mới tạo",
    processing: "Đang xác minh",
    approved: "Đã duyệt",
    resolved: "Hoàn tất",
    rejected: "Từ chối",
  };

  const normalize = (value) =>
    String(value || "")
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  const matchesDateFilter = (rowDateValue, filterValue) => {
    if (filterValue === "all") return true;

    const rowDate = new Date(`${rowDateValue}T00:00:00`);
    if (Number.isNaN(rowDate.getTime())) return false;

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const diffInDays = Math.floor((today - rowDate) / 86400000);

    if (filterValue === "today") return diffInDays === 0;
    if (filterValue === "week") return diffInDays >= 0 && diffInDays <= 7;
    if (filterValue === "month") return diffInDays >= 0 && diffInDays <= 30;

    return true;
  };

  const renderRowStatus = (row, statusValue) => {
    const badge = row.querySelector("[data-return-status-badge]");
    if (!badge) return;

    row.dataset.status = statusValue;
    badge.className = `status-pill ${badgeClassMap[statusValue] || "status-pill--pending"}`;
    badge.textContent = statusLabelMap[statusValue] || statusValue;
  };

  const applyFilters = () => {
    const search = normalize(searchInput?.value);
    const keyword = normalize(keywordFilter?.value);
    const type = typeFilter?.value || "all";
    const status = statusFilter?.value || "all";
    const date = dateFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowKeyword = normalize(row.dataset.keyword);
      const rowType = row.dataset.type || "";
      const rowStatus = row.dataset.status || "";
      const rowDate = row.dataset.date || "";

      const matchesSearch = !search || rowKeyword.includes(search);
      const matchesKeyword = !keyword || rowKeyword.includes(keyword);
      const matchesType = type === "all" || rowType === type;
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesDate = matchesDateFilter(rowDate, date);
      const isVisible = matchesSearch && matchesKeyword && matchesType && matchesStatus && matchesDate;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (summary) {
      summary.textContent =
        visibleCount === 0
          ? "Không tìm thấy yêu cầu đổi trả phù hợp"
          : `Hiển thị 1 - ${visibleCount} trong số ${rows.length} yêu cầu đổi trả`;
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

    renderRowStatus(row, row.dataset.status || "pending");

    saveButton?.addEventListener("click", () => {
      if (!select) return;
      renderRowStatus(row, select.value);
      applyFilters();

      const requestCode = row.querySelector(".return-code strong")?.textContent || "yêu cầu";
      showToast(`Đang lưu trạng thái cho ${requestCode}.`);
    });
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminReturnsManagePage);
