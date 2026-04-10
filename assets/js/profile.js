function initProfilePage() {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");
  const saveButton = document.querySelector("[data-save-profile]");

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  saveButton?.addEventListener("click", () => {
    const originalMarkup = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fa-regular fa-circle-check" aria-hidden="true"></i> Đã cập nhật';
    saveButton.disabled = true;

    window.setTimeout(() => {
      saveButton.innerHTML = originalMarkup;
      saveButton.disabled = false;
    }, 1800);
  });
}

document.addEventListener("DOMContentLoaded", initProfilePage);

