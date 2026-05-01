<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 | Không tìm thấy trang</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="card shadow-sm mx-auto" style="max-width: 680px;">
        <div class="card-body p-5">
          <h1 class="mb-3">404</h1>
          <p class="lead">Trang bạn tìm hiện không tồn tại hoặc đường dẫn chưa được khai báo route.</p>
          <a class="btn btn-dark" href="<?= e(url('/')) ?>">Quay về trang chủ</a>
        </div>
      </div>
    </div>
  </body>
</html>
