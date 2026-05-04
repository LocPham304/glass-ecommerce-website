function initAdminProductsManagePage() {
  const searchInput = document.querySelector("[data-product-search]");
  const nameFilter = document.querySelector("[data-filter-name]");
  const categoryFilter = document.querySelector("[data-filter-category]");
  const statusFilter = document.querySelector("[data-filter-status]");
  const stockFilter = document.querySelector("[data-filter-stock]");
  const checkAll = document.querySelector("[data-check-all]");
  const rows = Array.from(document.querySelectorAll(".product-row"));
  const summary = document.querySelector("[data-product-summary]");

  if (rows.length === 0) {
    return;
  }

  const normalize = (value) =>
    String(value || "")
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");

  const updateSummary = (visibleCount) => {
    if (!summary) {
      return;
    }

    if (visibleCount === 0) {
      summary.textContent = "Không tìm thấy sản phẩm phù hợp";
      return;
    }

    summary.textContent = `Hiển thị 1 - ${visibleCount} trong số ${rows.length} sản phẩm`;
  };

  const visibleCheckboxes = () =>
    rows
      .filter((row) => !row.hidden)
      .map((row) => row.querySelector('input[type="checkbox"]'))
      .filter(Boolean);

  const syncCheckAll = () => {
    if (!checkAll) {
      return;
    }

    const checkboxes = visibleCheckboxes();
    const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

    checkAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
    checkAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
  };

  const applyFilters = () => {
    const search = normalize(searchInput?.value);
    const keyword = normalize(nameFilter?.value);
    const category = categoryFilter?.value || "all";
    const status = statusFilter?.value || "all";
    const stock = stockFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowName = normalize(row.dataset.name);
      const rowSku = normalize(row.dataset.sku);
      const rowCategory = row.dataset.category || "";
      const rowStatus = row.dataset.status || "";
      const rowStock = row.dataset.stock || "";

      const matchesSearch = !search || rowName.includes(search) || rowSku.includes(search);
      const matchesKeyword = !keyword || rowName.includes(keyword) || rowSku.includes(keyword);
      const matchesCategory = category === "all" || rowCategory === category;
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesStock = stock === "all" || rowStock === stock;

      const isVisible =
        matchesSearch &&
        matchesKeyword &&
        matchesCategory &&
        matchesStatus &&
        matchesStock;

      row.hidden = !isVisible;

      if (!isVisible) {
        const checkbox = row.querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = false;
        }
      }

      if (isVisible) {
        visibleCount += 1;
      }
    });

    updateSummary(visibleCount);
    syncCheckAll();
  };

  [searchInput, nameFilter, categoryFilter, statusFilter, stockFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  rows.forEach((row) => {
    const checkbox = row.querySelector('input[type="checkbox"]');
    checkbox?.addEventListener("change", syncCheckAll);

    const editButton = row.querySelector(".product-actions button");
    editButton?.addEventListener("click", () => {
      const editUrl = row.dataset.editUrl;
      if (editUrl) {
        window.location.href = editUrl;
      }
    });

    const deleteForm = row.querySelector("[data-delete-product-form]");
    deleteForm?.addEventListener("submit", (event) => {
      const productName = deleteForm.dataset.productName || row.dataset.productName || "san pham nay";
      const confirmed = window.confirm(
        `Ban co chac muon xoa "${productName}"? Hanh dong nay khong the hoan tac.`
      );

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  checkAll?.addEventListener("change", () => {
    const checked = Boolean(checkAll.checked);

    visibleCheckboxes().forEach((checkbox) => {
      checkbox.checked = checked;
    });

    syncCheckAll();
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminProductsManagePage);