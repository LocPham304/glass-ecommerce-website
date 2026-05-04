function initAdminUserCreatePage() {
  const avatarInput = document.querySelector("[data-user-avatar]");
  const avatarPreview = document.querySelector("[data-avatar-preview]");
  const form = document.querySelector("[data-user-create-form]");
  const formMode = form?.getAttribute("data-form-mode") || "create";
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
    manager: {
      title: "Quản lý",
      description: "Theo dõi vận hành, quản lý báo cáo và điều phối các nhóm nghiệp vụ.",
      icon: "fa-solid fa-user-tie",
    },
    sales: {
      title: "Nhân viên bán hàng",
      description: "Phụ trách tư vấn, xử lý đơn hàng và hỗ trợ khách hàng trong quá trình mua sắm.",
      icon: "fa-solid fa-user-gear",
    },
    operations: {
      title: "Nhân viên vận hành",
      description: "Phụ trách vận hành đơn hàng, đổi trả và các nghiệp vụ nội bộ hằng ngày.",
      icon: "fa-solid fa-user-gear",
    },
    customer: {
      title: "Khách hàng",
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
        <span>Nhấn để thay đổi ảnh đại diện</span>
    `;
  });

  roleSelect?.addEventListener("change", updateRolePreview);

  draftButton?.addEventListener("click", () => {
    showToast("Đã lưu hồ sơ người dùng ở trạng thái nháp.");
  });

  form?.addEventListener("submit", (event) => {
    showToast(
      formMode === "edit"
        ? "Thông tin người dùng đang được cập nhật."
        : "Tài khoản người dùng mới đang được tạo."
    );
  });

  updateRolePreview();
}

document.addEventListener("DOMContentLoaded", initAdminUserCreatePage);