<?php
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "restaurant"; // Ganti dengan nama database Anda

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Mengecek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Mendapatkan data dari form
  $username = $_POST['nama'];
  $email = $_POST['email'];
  $no_telp = $_POST['number'];
  $password = $_POST['password'];
  $password_confirm = $_POST['password-confirm'];
  $role = "User";

  // Validasi password
  if ($password !== $password_confirm) {
    echo "Konfirmasi kata sandi tidak cocok!";
    exit();
  }

  // Menghash password untuk keamanan
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  // Menyimpan data ke database
  $sql = "INSERT INTO users (username, email, password, no_telp, role) VALUES ('$username', '$email', '$hashed_password', '$no_telp', '$role')";

  if ($conn->query($sql) === TRUE) {
    echo "Pendaftaran berhasil!";
    // Redirect ke halaman login setelah berhasil
    header("Location: masuk.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  // Menutup koneksi
  $conn->close();
}
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
              <h5 class="modal-title">Daftar</h5>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="Username">Nama<span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" id="nama" style="border-radius: 0;">
              </div>

              <div class="mb-3">
                <label for="Email">Email<span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email" style="border-radius: 0;">
              </div>

              <div class="mb-3">
                <label for="Number">Nomor Telepon<span class="text-danger">*</span></label>
                <input type="number" name="number" class="form-control" id="number" style="border-radius: 0;">
              </div>
              <div class="mb-3">
                <label for="Password">Kata Sandi<span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" id="password" style="border-radius: 0;">
              </div>
              <div class="mb-3">
                <label for="Password-confirm">Konfirmasi Kata Sandi<span class="text-danger">*</span></label>
                <input type="password" name="password-confirm" class="form-control" id="password-confirm" style="border-radius: 0;">
              </div>
            </div>
            <div class="modal-footer pt-4">
              <button type="button button-daftar" class="btn mx-auto w-100" style="border-radius: 0; background-color: #d4a89c; color: white;">Daftar</button>
            </div>
            <p class="text-center">Sudah Terdaftar? <a href="masuk.php" style="color: black;"> Masuk</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>