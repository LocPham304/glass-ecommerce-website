function initAdminProductsPage() {
  const searchInput = document.querySelector("[data-product-search]");
  const nameFilter = document.querySelector("[data-filter-name]");
  const categoryFilter = document.querySelector("[data-filter-category]");
  const brandFilter = document.querySelector("[data-filter-brand]");
  const statusFilter = document.querySelector("[data-filter-status]");
  const stockFilter = document.querySelector("[data-filter-stock]");
  const checkAll = document.querySelector("[data-check-all]");
  const rows = document.querySelectorAll(".product-row");
  const summary = document.querySelector("[data-product-summary]");

  const normalize = (value) => value.trim().toLowerCase();

  const applyFilters = () => {
    const search = normalize(searchInput?.value || "");
    const name = normalize(nameFilter?.value || "");
    const category = categoryFilter?.value || "all";
    const brand = brandFilter?.value || "all";
    const status = statusFilter?.value || "all";
    const stock = stockFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowName = normalize(row.dataset.name || "");
      const rowCategory = row.dataset.category || "";
      const rowBrand = row.dataset.brand || "";
      const rowStatus = row.dataset.status || "";
      const rowStock = row.dataset.stock || "";

      const matchesSearch = !search || rowName.includes(search) || normalize(rowName).includes(name);
      const matchesName = !name || rowName.includes(name);
      const matchesCategory = category === "all" || rowCategory === category;
      const matchesBrand = brand === "all" || rowBrand === brand;
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesStock = stock === "all" || rowStock === stock;

      const isVisible =
        matchesSearch &&
        matchesName &&
        matchesCategory &&
        matchesBrand &&
        matchesStatus &&
        matchesStock;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (summary) {
      summary.textContent = `Hiển thị 1 - ${visibleCount} trong số 254 sản phẩm`;
    }
  };

  [searchInput, nameFilter, categoryFilter, brandFilter, statusFilter, stockFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  checkAll?.addEventListener("change", () => {
    const checked = Boolean(checkAll.checked);
    rows.forEach((row) => {
      const checkbox = row.querySelector('input[type="checkbox"]');
      if (checkbox && !row.hidden) {
        checkbox.checked = checked;
      }
    });
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminProductsPage);

