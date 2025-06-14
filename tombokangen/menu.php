<?php
// koneksi.php
$koneksi = mysqli_connect('localhost', 'root', '', 'restaurant');
if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

// proses.php
session_start();

$kategori_makanan = "Makanan";
$kategori_minuman = "Minuman";

// Query untuk mendapatkan menu berdasarkan kategori
$query_makanan = "SELECT * FROM menu WHERE kategori = '$kategori_makanan' ORDER BY id DESC";
$query_minuman = "SELECT * FROM menu WHERE kategori = '$kategori_minuman' ORDER BY id DESC";

$menu_makanan = mysqli_query($koneksi, $query_makanan);
$menu_minuman = mysqli_query($koneksi, $query_minuman);

// Form untuk tambah/edit menu
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

  <!-- css -->
  <link rel="stylesheet" href="style.css">

  <script src="https://kit.fontawesome.com/de64d1b51f.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: 'Georgia', serif;
    }
  </style>
</head>

<body>
  <!-- Tambahkan ini di bagian atas konten utama menu.php -->
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
      <?php
      echo $_SESSION['success_message'];
      unset($_SESSION['success_message']);
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
      <?php
      echo $_SESSION['error_message'];
      unset($_SESSION['error_message']);
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Tambahkan script ini sebelum closing body tag di menu.php -->
  <script>
    // Fungsi untuk menghilangkan alert
    function hideAlert(alertId) {
      const alert = document.getElementById(alertId);
      if (alert) {
        setTimeout(() => {
          alert.classList.remove('show');
          setTimeout(() => {
            alert.remove();
          }, 300);
        }, 3000); // Alert akan hilang setelah 3 detik
      }
    }

    // Auto hide alerts ketika halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      const successAlert = document.getElementById('successAlert');
      const errorAlert = document.getElementById('errorAlert');

      if (successAlert) {
        hideAlert('successAlert');
      }

      if (errorAlert) {
        hideAlert('errorAlert');
      }
    });
  </script>

  <!-- Tambahkan style ini di bagian head menu.php -->
  <style>
    .alert {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1050;
      min-width: 300px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
      color: #155724;
    }

    .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }

    .fade {
      transition: opacity 0.3s linear;
    }
  </style>
  <div class="container mt-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-5">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#"> Tombo Kangen</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item px-3">
              <a class="nav-link" href="index.php">Beranda</a>
            </li>
            <li class="nav-item px-3">
              <a class="nav-link" href="menu.php">Menu</a>
            </li>
            <li class="nav-item px-3">
              <a class="nav-link" href="tentang-kami.php">Tentang Kami</a>
            </li>
            <li class="nav-item dropdown px-3">
              <a class="nav-link dropdown-toggle" href="pesanan.php" id="pesananDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Pesanan
              </a>
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
    <div class="row align-items-center header-wrapper">
      <div class="container my-5">
        <div class="row">
          <!-- Left Column: Desserts -->
          <div class="col-lg-6 col-md-12">
            <h3 class="mb-4" style="color: white;">Makanan</h3>
            <div class="d-flex flex-column gap-3">
              <!-- Example Card -->
              <?php while ($menu_item = mysqli_fetch_assoc($menu_makanan)): ?>
                <div class="card menu-card p-3" onclick="window.location.href='menu-detail.php?id=<?php echo $menu_item['id']; ?>'">
                  <img src="admin/uploads/<?php echo $menu_item['gambar']; ?>" alt="<?php echo $menu_item['nama']; ?>">
                  <div class="menu-card-body">
                    <h5 class="menu-title"><?php echo $menu_item['nama']; ?></h5>
                    <p class="menu-desc"><?php echo $menu_item['deskripsi']; ?></p>
                    <p class="menu-price">Rp. <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                  </div>
                </div>
              <?php endwhile; ?>
              <!-- Add more cards here -->
            </div>
          </div>

          <!-- Right Column: Drinks -->
          <div class="col-lg-6 col-md-12">
            <h3 class="mb-4" style="color: white;">Minuman</h3>
            <div class="d-flex flex-column gap-3">
              <!-- Example Card -->
              <?php while ($menu_item = mysqli_fetch_assoc($menu_minuman)): ?>
                <div class="card menu-card p-3" onclick="window.location.href='menu-detail.php?id=<?php echo $menu_item['id']; ?>'">
                  <img src="admin/uploads/<?php echo $menu_item['gambar']; ?>" alt="<?php echo $menu_item['nama']; ?>">
                  <div class="menu-card-body">
                    <h5 class="menu-title"><?php echo $menu_item['nama']; ?></h5>
                    <p class="menu-desc"><?php echo $menu_item['deskripsi']; ?></p>
                    <p class="menu-price">Rp. <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                  </div>
                </div>
              <?php endwhile; ?>
              <!-- Add more cards here -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="floating-icon">
      <a href="#" class="icon-link">
        <!-- up icon -->
        <i class="fas fa-arrow-up"></i>
      </a>
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
            <a href="information.php" class="text-decoration-none" style="color: #000;">All Rights Reserved</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>