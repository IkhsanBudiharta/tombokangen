<?php
session_start();

// Konfigurasi koneksi database
$host = 'localhost';  // Ganti dengan host database 
$username = 'root';   // Ganti dengan username database 
$password = '';       // Ganti dengan password database 
$dbname = 'restaurant'; // Ganti dengan nama database 

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek apakah koneksi berhasil
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah pengguna sudah login
if (isset($_SESSION['email'])) {
  header("Location: menu.php"); // Redirect jika sudah login
  exit();
}

// Inisialisasi variabel error dan email/password
$error = '';
$email = $password = '';

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Validasi input
  if (empty($email) || empty($password)) {
    $error = "Email dan Kata Sandi wajib diisi!";
  } else {
    // Query untuk memeriksa email di database
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika email ditemukan
    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      // Verifikasi password menggunakan password_hash() dan password_verify()
      if (password_verify($password, $user['password'])) {
        // Set session untuk menandakan pengguna sudah login
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        // Cek kredensial, ini adalah contoh sederhana
        // Debug: Log nilai role
        // error_log("Role user: " . $_SESSION['role']);
        // Perbaiki pengecekan role
        if ($_SESSION['role'] === 'Admin') {
          header('Location: admin/admin.php'); // Redirect ke halaman admin
          exit();
        } else {
          header('Location: index.php'); // Redirect ke halaman utama
          exit();
        }
        header("Location: menu.php"); // Redirect ke halaman dashboard
        exit();
      } else {
        $error = "Email atau Kata Sandi salah!";
      }
    } else {
      $error = "Email atau Kata Sandi salah!";
    }

    // Tutup statement dan koneksi
    $stmt->close();
  }
}

// Tutup koneksi database
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tombo Kangen</title>
  <link rel="icon" href="https://ik.imagekit.io/rn4jfgmjp/20241205_000510.png?updatedAt=1733331993815" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <script src="https://kit.fontawesome.com/de64d1b51f.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: 'Georgia', serif;
      color: white;
    }
  </style>
</head>

<body>
  <!-- Modal Form -->
  <div class="modal fade show" id="ModalForm" tabindex="-1" aria-hidden="true" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background-image: url('https://images.unsplash.com/photo-1635922996618-e9945dc17079?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); background-size: cover; background-position: center; border-radius: 0;">
        <!-- Overlay to reduce blur effect and improve contrast -->
        <div style="background-color: rgba(128, 128, 128, 0.8); width: 100%; height: 100%; padding: 20px;">
          <!-- Login Form -->
          <form action="" method="POST">
            <div class="modal-header justify-content-center">
              <h5 class="modal-title">Masuk</h5>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="Username">Email<span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email" style="border-radius: 0;">
              </div>

              <div class="mb-3">
                <label for="Password">Kata Sandi<span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" id="password" style="border-radius: 0;">
              </div>
              <!-- <div class="mb-3">
              <input class="form-check-input" type="checkbox" value="" id="remember" required>
              <label class="form-check-label" for="remember">Ingat Saya</label>
              <a style="color: black;" href="#" class="float-end">Lupa Kata Sandi</a>
            </div> -->
            </div>
            <div class="modal-footer pt-4">
              <button type="submit" class="btn mx-auto w-100" style="background-color: #d4a89c; color: white; border-radius: 0;">Login</button>
            </div>
            <p class="text-center">Belum Punya Akun? <a style="color: black;" href="daftar.php">Daftar</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>