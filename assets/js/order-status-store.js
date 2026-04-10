(function initOrderStatusStore() {
  const STORAGE_KEY = "clearvision-order-statuses-v1";

  const STATUS_META = {
    confirmed: {
      adminLabel: "Chờ xác nhận",
      customerLabel: "Chờ xác nhận",
      filterKey: "confirmed",
      badgeText: "CHỜ XÁC NHẬN",
      badgeClass: "history-status--confirmed",
      note: "Đơn hàng đang chờ cửa hàng xác nhận. Bạn sẽ nhận được thông báo ngay khi đơn được duyệt.",
      primaryAction: "Liên hệ",
      progressIndex: 0,
    },
    processing: {
      adminLabel: "Đang xử lý",
      customerLabel: "Đang xử lý",
      filterKey: "processing",
      badgeText: "ĐANG XỬ LÝ",
      badgeClass: "history-status--processing",
      note: "Đơn hàng đã được xác nhận và đang trong quá trình chuẩn bị sản phẩm để bàn giao cho đơn vị vận chuyển.",
      primaryAction: "Liên hệ",
      progressIndex: 1,
    },
    shipping: {
      adminLabel: "Đang giao",
      customerLabel: "Đang giao hàng",
      filterKey: "shipping",
      badgeText: "ĐANG GIAO HÀNG",
      badgeClass: "history-status--shipping",
      note: "Đơn hàng đang được giao đến bạn. Khi đã nhận đúng sản phẩm, bạn có thể xác nhận đã nhận hàng ngay tại đây.",
      primaryAction: "Đã nhận được hàng",
      progressIndex: 2,
    },
    delivered: {
      adminLabel: "Hoàn thành",
      customerLabel: "Hoàn thành",
      filterKey: "delivered",
      badgeText: "HOÀN THÀNH",
      badgeClass: "history-status--delivered",
      note: "Đơn hàng đã giao thành công. Bạn có thể đánh giá hoặc yêu cầu đổi trả nếu cần hỗ trợ thêm.",
      primaryAction: "Trả hàng",
      progressIndex: 3,
    },
    cancelled: {
      adminLabel: "Đã hủy",
      customerLabel: "Đã hủy",
      filterKey: "cancelled",
      badgeText: "ĐÃ HỦY",
      badgeClass: "history-status--cancelled",
      note: "Đơn hàng đã bị hủy. Nếu cần hỗ trợ thêm, vui lòng liên hệ CSKH để được giải đáp.",
      primaryAction: "Liên hệ",
      progressIndex: -1,
    },
  };

  const FLOW_STEPS = [
    "Đã đặt hàng",
    "Xác nhận",
    "Đang xử lý",
    "Đang giao",
    "Hoàn thành",
  ];

  const DEFAULT_ORDER_STATUSES = {
    CV98421: "shipping",
    CV98399: "delivered",
    CV98456: "confirmed",
    CV98277: "processing",
    CV98110: "cancelled",
  };

  const normalizeCode = (code) => String(code || "").replace(/^#/, "").trim().toUpperCase();

  const loadStatuses = () => {
    try {
      const parsed = JSON.parse(window.localStorage.getItem(STORAGE_KEY) || "{}");
      return { ...DEFAULT_ORDER_STATUSES, ...parsed };
    } catch (error) {
      return { ...DEFAULT_ORDER_STATUSES };
    }
  };

  const saveStatuses = (statuses) => {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(statuses));
  };

  const ensureDefaults = () => {
    const statuses = loadStatuses();
    saveStatuses(statuses);
    return statuses;
  };

  const getStatus = (orderCode) => {
    const code = normalizeCode(orderCode);
    const statuses = loadStatuses();
    return statuses[code] || "confirmed";
  };

  const setStatus = (orderCode, status) => {
    const code = normalizeCode(orderCode);
    const statuses = loadStatuses();
    statuses[code] = status;
    saveStatuses(statuses);
    return statuses[code];
  };

  ensureDefaults();

  window.OrderStatusStore = {
    FLOW_STEPS,
    STATUS_META,
    getStatus,
    setStatus,
    normalizeCode,
  };
})();




