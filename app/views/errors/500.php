<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>500 | Lỗi hệ thống</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="card shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-5">
          <h1 class="mb-3">Có lỗi khi chạy ứng dụng</h1>
          <p class="text-secondary">
            Nếu đây là lần đầu bạn chạy project, thường nguyên nhân sẽ là cấu hình
            `config/database.php` hoặc database chưa đúng tên.
          </p>
          <pre class="bg-dark text-white p-3 rounded small mb-0"><?= e($errorMessage ?? 'Unknown error') ?></pre>
        </div>
      </div>
    </div>
  </body>
</html>
