<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tombo Kangen</title>
  <link
    rel="icon"
    href="https://ik.imagekit.io/rn4jfgmjp/20241205_000510.png?updatedAt=1733331993815"
    type="image/png" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet" />

  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />

  <!-- css -->
  <link rel="stylesheet" href="style.css" />

  <script
    src="https://kit.fontawesome.com/de64d1b51f.js"
    crossorigin="anonymous"></script>
  <style>
    body {
      font-family: "Georgia", serif;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-5">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#"> Tombo Kangen</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation">
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
              <a
                class="nav-link dropdown-toggle"
                href="pesanan.php"
                id="pesananDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Pesanan
              </a>
              <ul class="dropdown-menu" aria-labelledby="pesananDropdown">
                <li>
                  <a class="dropdown-item" href="pesanan.php">Keranjang</a>
                </li>
                <li>
                  <a class="dropdown-item" href="semua-pesanan.php">Semua Pesanan</a>
                </li>
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
    <div class="row align-items-center header-wrapper-tentang">
      <div class="col-lg-7">
        <h1 class="header-title">Tentang<br />Kami</h1>
      </div>
      <div class="col-lg-5" style="color: white">
        <p class="lead">Kontak Kami : 085809876543</p>
        <br />
        <p class="lead">
          Alamat : Jl. Akses UI No.44, Tugu, Kec. Cimanggis, Kota Depok, Jawa
          Barat 16451
        </p>
        <br />
        <p class="lead">Jam Operasional : Senin - Minggu (10:00 - 22:00)</p>
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
            <a
              href="information.php"
              class="text-decoration-none"
              style="color: #000">All Rights Reserved</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>