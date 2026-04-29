function initAdminPromotionsPage() {
  const searchInput = document.querySelector("[data-promo-search]");
  const codeFilter = document.querySelector("[data-filter-code]");
  const typeFilter = document.querySelector("[data-filter-type]");
  const statusFilter = document.querySelector("[data-filter-status]");
  const rows = Array.from(document.querySelectorAll(".promotion-row"));
  const summary = document.querySelector("[data-promo-summary]");
  const createButton = document.querySelector("[data-create-voucher]");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

  let toastTimeout;

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  const normalize = (value) =>
    String(value || "")
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");

  const applyFilters = () => {
    const search = normalize(searchInput?.value || "");
    const code = normalize(codeFilter?.value || "");
    const type = typeFilter?.value || "all";
    const status = statusFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowCode = normalize(row.dataset.code || "");
      const rowType = row.dataset.type || "";
      const rowStatus = row.dataset.status || "";

      const matchesSearch = !search || rowCode.includes(search);
      const matchesCode = !code || rowCode.includes(code);
      const matchesType = type === "all" || rowType === type;
      const matchesStatus = status === "all" || rowStatus === status;
      const isVisible = matchesSearch && matchesCode && matchesType && matchesStatus;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (summary) {
      summary.textContent =
        visibleCount === 0
          ? "Không tìm thấy voucher phù hợp"
          : `Hiển thị 1 - ${visibleCount} trong số ${rows.length} voucher`;
    }
  };

  [searchInput, codeFilter, typeFilter, statusFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  createButton?.addEventListener("click", () => {
    showToast("Bước tiếp theo mình có thể làm luôn form tạo voucher mới.");
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminPromotionsPage);
