function initAdminUserCreatePage() {
  const avatarInput = document.querySelector("[data-user-avatar]");
  const avatarPreview = document.querySelector("[data-avatar-preview]");
  const form = document.querySelector("[data-user-create-form]");
  const draftButton = document.querySelector("[data-save-draft]");
  const roleSelect = document.querySelector("[data-user-role]");
  const roleTitle = document.querySelector("[data-role-title]");
  const roleDescription = document.querySelector("[data-role-description]");
  const roleIcon = document.querySelector(".role-preview__icon i");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

  let toastTimeout;

  const roleMeta = {
    admin: {
      title: "Quản trị viên",
      description: "Toàn quyền quản trị hệ thống, quản lý dữ liệu và điều phối nghiệp vụ.",
      icon: "fa-solid fa-user-shield",
    },
    staff: {
      title: "Nhan vien",
      description: "Phụ trách vận hành đơn hàng, hỗ trợ khách hàng và xử lý nghiệp vụ hằng ngày.",
      icon: "fa-solid fa-user-gear",
    },
    customer: {
      title: "Khach hang",
      description: "Tài khoản mua hàng, theo dõi đơn và sử dụng voucher trên website.",
      icon: "fa-solid fa-user",
    },
  };

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  const updateRolePreview = () => {
    const meta = roleMeta[roleSelect?.value || "admin"];
    if (!meta) return;

    if (roleTitle) roleTitle.textContent = meta.title;
    if (roleDescription) roleDescription.textContent = meta.description;
    if (roleIcon) roleIcon.className = meta.icon;
  };

  avatarInput?.addEventListener("change", () => {
    const file = avatarInput.files?.[0];

    if (!file || !avatarPreview) return;

    const imageUrl = URL.createObjectURL(file);
    avatarPreview.classList.add("has-image");
    avatarPreview.style.backgroundImage = `url("${imageUrl}")`;
    avatarPreview.innerHTML = `
      <i class="fa-regular fa-image" aria-hidden="true"></i>
      <strong>${file.name}</strong>
        <span>Nhan de thay doi anh dai dien</span>
    `;
  });

  roleSelect?.addEventListener("change", updateRolePreview);

  draftButton?.addEventListener("click", () => {
    showToast("Da luu ho so nguoi dung o trang thai nhap.");
  });

  form?.addEventListener("submit", (event) => {
    event.preventDefault();
    showToast("Tai khoan nguoi dung moi da duoc tao thanh cong.");
  });

  updateRolePreview();
}

document.addEventListener("DOMContentLoaded", initAdminUserCreatePage);


