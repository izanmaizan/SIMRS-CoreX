<?php
session_start();

// Hapus session lama lalu buat baru
session_destroy();
session_start();

// Daftar user dengan role
$users = [
    'admin' => [
        'password' => '123456',
        'role' => 'admin',
        'name' => 'Administrator'
    ],
    'superadmin' => [
        'password' => 'mnatsir12',
        'role' => 'superadmin',
        'name' => 'Super Administrator'
    ]
];

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
        $_SESSION['name'] = $users[$username]['name'];
        $_SESSION['last_activity'] = time();
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - SIMRS CoreX | core Exchange</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="apple-touch-icon" href="./logo.webp">
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
    />
    <link rel="stylesheet" href="style.css">
  </head>
  <body class="login-page">
    <div class="login-container">
        <div class="left-panel">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <img src="./logo.webp" alt="RSUD Logo" style="width: 50px; height: 50px; object-fit: contain;">
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem;">SIMRS CoreX</h2>
                    <p style="margin: 2px 0 0 0; font-size: 0.85rem; opacity: 0.9;">core Exchange</p>
                </div>
            </div>
            <h2>RSUD MOHAMMAD NATSIR</h2>
            <p>
                🏥 Jl. Simpang Rumbio Kota Solok<br />
                ☎️ 075220003 / 📠 2051466<br />
                📧 rsudmnatsir@gmail.com<br />
                🌐 rsudmnatsir.sumbarprov.go.id
            </p>
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.3);">
                <small style="opacity: 0.8;">Powered by SIMRS 2025</small>
            </div>
        </div>
        
        <div class="right-panel">
        <div class="login-box">
          <h3>LOGIN</h3>

          <?php if ($error): ?>
          <div class="alert alert-danger py-2 text-center">
            <?php echo $error; ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="input-group">
              <input
                type="text"
                name="username"
                placeholder="Masukkan ID Pengguna Anda"
                autofocus
                required
              />
              <i class="fa fa-user"></i>
            </div>
            <div class="input-group">
              <input
                type="password"
                name="password"
                placeholder="Masukkan Kata Sandi Anda"
                required
              />
              <i class="fa fa-lock"></i>
            </div>
            <button type="submit">Masuk</button>
          </form>
        </div>
      </div>
    </div>

    <script
      src="https://kit.fontawesome.com/a076d05399.js"
      crossorigin="anonymous"
    ></script>
  </body>
</html>