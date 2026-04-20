<?php
$toastMessages = [];

foreach ([
    'success' => [
        'class' => 'text-bg-success',
        'label' => 'Thành công',
        'role' => 'status',
        'live' => 'polite',
    ],
    'error' => [
        'class' => 'text-bg-danger',
        'label' => 'Có lỗi',
        'role' => 'alert',
        'live' => 'assertive',
    ],
] as $key => $config) {
    $message = flash($key);

    if ($message !== null && $message !== '') {
        $toastMessages[] = array_merge([
            'message' => $message,
        ], $config);
    }
}
?>

<?php if ($toastMessages !== []): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" aria-live="polite" aria-atomic="true" data-app-toast-container>
    <?php foreach ($toastMessages as $toast): ?>
      <div
        class="toast align-items-center border-0 <?= e($toast['class']) ?>"
        role="<?= e($toast['role']) ?>"
        aria-live="<?= e($toast['live']) ?>"
        aria-atomic="true"
        data-app-toast
        data-bs-delay="3500"
      >
        <div class="d-flex">
          <div class="toast-body">
            <?= e($toast['message']) ?>
          </div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            data-bs-dismiss="toast"
            aria-label="Đóng"
          ></button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
