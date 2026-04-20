<?php

function config(string $key, mixed $default = null): mixed
{
    $segments = explode('.', $key);
    $value = $GLOBALS['config'] ?? [];

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $baseUrl = rtrim((string) config('app.base_url', ''), '/');
    $path = trim($path, '/');

    if ($path === '') {
        return $baseUrl;
    }

    return $baseUrl . '/' . $path;
}

function url(string $path = ''): string
{
    return base_url($path);
}

function asset(string $path = ''): string
{
    $assetBaseUrl = rtrim((string) config('app.asset_url', config('app.base_url', '')), '/');
    $path = trim($path, '/');

    if ($path === '') {
        return $assetBaseUrl;
    }

    return $assetBaseUrl . '/' . $path;
}

function media_url(?string $path, string $fallback = 'assets/images/about-us/eyewear-display.png'): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return asset($fallback);
    }

    if (preg_match('#^(?:https?:)?//#i', $path) === 1 || str_starts_with($path, 'data:')) {
        return $path;
    }

    return asset(ltrim($path, '/'));
}

function order_type_label(?string $type): string
{
    return match ($type) {
        'ready_stock' => 'Đơn có sẵn',
        'pre_order' => 'Pre-order',
        'prescription' => 'Đơn cắt kính theo toa',
        default => $type !== null && $type !== '' ? $type : 'Chưa xác định',
    };
}

function color_to_hex(?string $value): string
{
    $normalized = mb_strtolower(trim((string) $value), 'UTF-8');

    return match (true) {
        $normalized === '' => '#d1d5db',
        str_contains($normalized, 'đen') || str_contains($normalized, 'black') => '#111827',
        str_contains($normalized, 'trắng') || str_contains($normalized, 'white') => '#f8fafc',
        str_contains($normalized, 'xám') || str_contains($normalized, 'grey') || str_contains($normalized, 'gray') => '#94a3b8',
        str_contains($normalized, 'bạc') || str_contains($normalized, 'silver') => '#cbd5e1',
        str_contains($normalized, 'vàng') || str_contains($normalized, 'gold') => '#d4a017',
        str_contains($normalized, 'xanh navy') || str_contains($normalized, 'navy') => '#1e3a8a',
        str_contains($normalized, 'xanh dương') || str_contains($normalized, 'blue') => '#2563eb',
        str_contains($normalized, 'xanh lá') || str_contains($normalized, 'green') => '#16a34a',
        str_contains($normalized, 'đỏ') || str_contains($normalized, 'red') => '#dc2626',
        str_contains($normalized, 'nâu') || str_contains($normalized, 'brown') => '#8b5e3c',
        str_contains($normalized, 'đồi mồi') || str_contains($normalized, 'tortoise') => '#7c4f35',
        str_contains($normalized, 'hồng') || str_contains($normalized, 'pink') => '#ec4899',
        str_contains($normalized, 'tím') || str_contains($normalized, 'purple') => '#7c3aed',
        str_contains($normalized, 'cam') || str_contains($normalized, 'orange') => '#f97316',
        str_contains($normalized, 'trong') || str_contains($normalized, 'clear') => '#e2e8f0',
        default => '#d1d5db',
    };
}

function parse_option_list(?string $value): array
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return [];
    }

    $parts = preg_split('/\s*[,;|\/]\s*/u', $raw) ?: [];
    $options = [];

    foreach ($parts as $part) {
        $option = trim((string) $part);
        if ($option === '' || in_array($option, $options, true)) {
            continue;
        }

        $options[] = $option;
    }

    return $options;
}

function category_display_name(?string $slug, ?string $name = null): string
{
    $slug = trim((string) $slug);
    $name = trim((string) $name);

    $labels = [
        'gong-kinh' => 'Gọng kính',
        'trong-kinh' => 'Tròng kính',
        'kinh-ram' => 'Kính râm',
        'phu-kien' => 'Phụ kiện',
    ];

    if ($slug !== '' && isset($labels[$slug])) {
        return $labels[$slug];
    }

    return $name !== '' ? $name : 'Danh mục';
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_currency(float|int|string|null $value): string
{
    return number_format((float) $value, 0, ',', '.') . 'đ';
}

function query_value(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;

    if (is_array($value)) {
        return $default;
    }

    return trim((string) $value);
}

function post_value(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    if (is_array($value)) {
        return $default;
    }

    return trim((string) $value);
}

function redirect(string $path = ''): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    if (isset($_SESSION['_flash'][$key])) {
        unset($_SESSION['_flash'][$key]);
    }

    return $value;
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function is_authenticated(): bool
{
    return auth_user() !== null;
}

function login_user(array $user): void
{
    $_SESSION['auth_user'] = $user;
}

function logout_user(): void
{
    unset($_SESSION['auth_user']);
}

function user_role_name(): ?string
{
    return auth_user()['role_name'] ?? null;
}

function has_role(array|string $roles): bool
{
    $roles = is_array($roles) ? $roles : [$roles];
    $currentRole = user_role_name();

    return $currentRole !== null && in_array($currentRole, $roles, true);
}

function require_auth(): void
{
    if (!is_authenticated()) {
        flash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('/login');
    }
}

function require_admin(): void
{
    if (!has_role(['manager', 'admin', 'sales', 'operations'])) {
        flash('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        redirect('/login');
    }
}

function now_sql(): string
{
    return date('Y-m-d H:i:s');
}

function generate_id(string $prefix): string
{
    return strtoupper(substr($prefix . bin2hex(random_bytes(8)), 0, 20));
}

function generate_order_code(): string
{
    return 'CV' . date('ymdHis') . random_int(10, 99);
}

function build_status_badge(string $status): array
{
    return match ($status) {
        'pending_confirmation' => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning-subtle text-warning-emphasis'],
        'pre_order_pending' => ['label' => 'Chờ hàng về', 'class' => 'bg-info-subtle text-info-emphasis'],
        'prescription_review' => ['label' => 'Đang duyệt toa', 'class' => 'bg-primary-subtle text-primary-emphasis'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-primary-subtle text-primary-emphasis'],
        'shipping' => ['label' => 'Đang giao', 'class' => 'bg-info-subtle text-info-emphasis'],
        'delivered' => ['label' => 'Hoàn thành', 'class' => 'bg-success-subtle text-success-emphasis'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-danger-subtle text-danger-emphasis'],
        'after_sales' => ['label' => 'Đổi trả / hoàn tiền', 'class' => 'bg-secondary-subtle text-secondary-emphasis'],
        default => ['label' => $status !== '' ? $status : 'Không xác định', 'class' => 'bg-light text-dark'],
    };
}
