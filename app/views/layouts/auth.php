<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Public+Sans:wght@400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <?php foreach ($pageStyles as $style): ?>
      <link rel="stylesheet" href="<?= e(asset($style)) ?>" />
    <?php endforeach; ?>
  </head>
  <body class="<?= e($bodyClass) ?>">
    <?php require BASE_PATH . '/app/views/partials/alerts.php'; ?>
    <?php require $viewFile; ?>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="<?= e(asset('assets/js/site-toasts.js?v=20260418-2')) ?>"></script>
    <?php foreach ($pageScripts as $script): ?>
      <?php
        $scriptUrl = asset($script);
        $scriptUrl .= str_contains($scriptUrl, '?') ? '&v=20260418-4' : '?v=20260418-4';
      ?>
      <script src="<?= e($scriptUrl) ?>"></script>
    <?php endforeach; ?>
  </body>
</html>
