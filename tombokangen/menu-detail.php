<?php
// koneksi.php
$koneksi = mysqli_connect('localhost', 'root', '', 'restaurant');
if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();

// Initialize $menu as null
$menu = null;

try {
  // Handle both GET and POST requests for pesanan
  if ((isset($_GET['action']) && $_GET['action'] == 'pesan') ||
    (isset($_POST['action']) && $_POST['action'] == 'pesan')
  ) {

    // Cek login
    if (!isset($_SESSION['id_user'])) {
      throw new Exception("Anda harus login untuk memesan.");
    }

    // Ambil menu_id dari GET atau POST
    $menu_id = isset($_GET['menu_id']) ? $_GET['menu_id'] : (isset($_POST['menu_id']) ? $_POST['menu_id'] : null);
    if (!$menu_id) {
      throw new Exception("ID Menu tidak valid.");
    }

    $user_id = $_SESSION['id_user'];
    $jumlah = 1;

    // Sanitasi input
    $menu_id = mysqli_real_escape_string($koneksi, $menu_id);
    $user_id = mysqli_real_escape_string($koneksi, $user_id);

    // Cek apakah menu exists
    $cek_menu = mysqli_query($koneksi, "SELECT id FROM menu WHERE id = '$menu_id'");
    if (mysqli_num_rows($cek_menu) == 0) {
      throw new Exception("Menu tidak ditemukan.");
    }

    // Cek duplikasi di keranjang
    $cek_keranjang = mysqli_query($koneksi, "SELECT id FROM keranjang WHERE user_id = '$user_id' AND menu_id = '$menu_id'");
    if (mysqli_num_rows($cek_keranjang) > 0) {
      throw new Exception("Menu sudah ada di keranjang.");
    }

    // Insert ke keranjang
    $query = "INSERT INTO keranjang (user_id, menu_id, jumlah) VALUES ('$user_id', '$menu_id', '$jumlah')";
    if (!mysqli_query($koneksi, $query)) {
      throw new Exception("Error inserting into keranjang: " . mysqli_error($koneksi));
    }

    // Set success message
    $_SESSION['success_message'] = "Menu berhasil ditambahkan ke keranjang!";
    header('Location: menu.php');
    exit();
  }

  // Get menu detail
  if (!isset($_GET['id'])) {
    throw new Exception("ID menu tidak diberikan.");
  }

  $id = mysqli_real_escape_string($koneksi, $_GET['id']);
  $menu_query = "SELECT * FROM menu WHERE id = '$id'";
  $menu_result = mysqli_query($koneksi, $menu_query);

  if (!$menu_result) {
    throw new Exception("Query gagal: " . mysqli_error($koneksi));
  }

  $menu = mysqli_fetch_assoc($menu_result);
  if (!$menu) {
    throw new Exception("Menu tidak ditemukan.");
  }
} catch (Exception $e) {
  $_SESSION['error_message'] = $e->getMessage();
  // If menu fetch fails, redirect to menu page
  if (!$menu) {
    header('Location: menu.php');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tombo Kangen</title>
  <link rel="icon" href="https://ik.imagekit.io/rn4jfgmjp/20241205_000510.png?updatedAt=1733331993815" type="image/png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <script src="https://kit.fontawesome.com/de64d1b51f.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: "Georgia", serif;
    }

    .card-shadow {
      position: relative;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 2;
    }

    .menu-unavailable {
      font-size: 1.5rem;
      color: white;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-5">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#">Tombo Kangen</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item px-3"><a class="nav-link" href="index.php">Beranda</a></li>
            <li class="nav-item px-3"><a class="nav-link" href="menu.php">Menu</a></li>
            <li class="nav-item px-3"><a class="nav-link" href="tentang-kami.php">Tentang Kami</a></li>
            <li class="nav-item dropdown px-3">
              <a class="nav-link dropdown-toggle" href="pesanan.php" id="pesananDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pesanan</a>
              <ul class="dropdown-menu" aria-labelledby="pesananDropdown">
                <li><a class="dropdown-item" href="pesanan.php">Keranjang</a></li>
                <li><a class="dropdown-item" href="semua-pesanan.php">Semua Pesanan</a></li>
              </ul>
            </li>
            <li class="nav-item px-3">
              <?php if (isset($_SESSION['id_user'])): ?>
                <!-- Jika user sudah login -->
                <a class="nav-link" href="logout.php">Logout</a>
              <?php else: ?>
                <!-- Jika user belum login -->
                <a class="nav-link" href="masuk.php">Masuk</a>
              <?php endif; ?>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="row align-items-center header-wrapper-menu-detail">
      <div class="contact3 py-5">
        <div class="row no-gutters">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <div class="card-shadow" style="position: relative; display: inline-block">
                  <img src="admin/uploads/<?= $menu['gambar'] ?>" class="img-fluid" />
                  <!-- Overlay hanya muncul jika status menu tidak tersedia -->
                  <?php if ($menu['status'] === 'tidak_tersedia'): ?>
                    <div class="overlay">
                      <p class="menu-unavailable">Menu Tidak Tersedia</p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="contact-box ml-3">
                  <h1 class="font-weight-light mt-2" style="color: white"><?= $menu['nama'] ?></h1>
                  <form class="mt-4">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="form-group mt-2">
                          <p style="color: white"><?= $menu['deskripsi'] ?></p>
                        </div>
                      </div>
                      <div class="col-lg-12">
                        <div class="form-group mt-2">
                          <p class="menu-price" style="color: white; font-size: 1.5rem">Rp. <?= number_format($menu['harga'], 0, ',', '.') ?></p>
                        </div>
                      </div>
                      <!-- Overlay hanya muncul jika status menu tidak tersedia -->
                      <?php if ($menu['status'] === 'tersedia'): ?>
                        <div class="col-lg-12">
                          <a href="?id=<?= htmlspecialchars($menu['id']) ?>&action=pesan&menu_id=<?= htmlspecialchars($menu['id']) ?>"
                            class="btn btn-danger-gradiant mt-3 border-0 button-pesan">
                            <span>PESAN SEKARANG</span>
                          </a>
                        </div>
                      <?php endif; ?>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid mt-5 footer">
      <div class="divider mb-4"></div>
      <div class="row text-center text-sm-start">
        <div class="col-sm-6 mb-2 mb-sm-0">
          <p><i class="fa fa-copyright"></i> 2024 Tombo Kangen</p>
        </div>
        <div class="col-sm-6">
          <div class="d-flex justify-content-center justify-content-sm-end">
            <a href="information.php" class="text-decoration-none" style="color: #000">All Rights Reserved</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>