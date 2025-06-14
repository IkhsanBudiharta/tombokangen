<?php
session_start();
// Cek apakah pengguna sudah login dan apakah role-nya adalah 'admin'
if ($_SESSION['role'] != 'Admin') {
  // Jika tidak login atau bukan admin, arahkan ke halaman utama
  header('Location: ../menu.php');
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tombo Kangen | Admin</title>
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

  <script
    src="https://kit.fontawesome.com/de64d1b51f.js"
    crossorigin="anonymous"></script>

  <style>
    body {
      font-family: "Georgia", serif;
    }

    .navbar {
      max-width: 97%;
      margin: 0 auto;
      /* Pastikan posisinya tetap di tengah */
    }

    .nav-link {
      color: #000;
      position: relative;
      transition: color 0.3s;
    }

    .nav-link:hover {
      color: #d4a89c;
    }

    .nav-item.dropdown:hover .dropdown-menu {
      display: block;
      margin-top: 0;
      border-radius: 0;
    }

    .header-title {
      font-size: 9vw;
      /* Menggunakan unit fleksibel untuk ukuran font */
      font-weight: 400;
      color: white;
      line-height: 1.2;
    }

    .header-wrapper {
      margin-top: 4rem;
      max-width: 96%;
      margin-left: auto;
      margin-right: auto;
    }

    .content-text {
      font-size: 1rem;
      color: white;
    }

    .to-request {
      text-transform: uppercase;
      color: white;
      font-weight: 600;
      text-decoration: none;
    }

    .to-request:hover {
      color: #000;
      text-decoration: underline;
    }

    /* =============================================*\
                    |  =========== Responsive Index.Html =========== |
                    \* ============================================ */
    @media (max-width: 768px) {
      .header-title {
        font-size: 8vw;
        /* Ukuran font lebih kecil di layar medium */
      }

      .header-wrapper {
        margin-top: 2rem;
        /* Kurangi margin pada layar kecil */
      }

      .navbar-nav {
        display: flex;
        justify-content: center;
        width: 100%;
      }

      .navbar-nav .nav-item {
        flex: 1;
      }

      .navbar-nav .nav-link {
        text-align: center;
      }
    }

    @media (max-width: 576px) {
      .header-title {
        font-size: 12vw;
        /* Ukuran font lebih kecil di layar kecil */
      }

      .content-text {
        font-size: 0.9rem;
        /* Kurangi ukuran teks deskripsi */
      }

      .to-request {
        font-size: 0.9rem;
        /* Ukuran tombol lebih kecil */
      }

      .header-wrapper {
        margin-top: 1rem;
        /* Margin lebih kecil untuk layar kecil */
        width: 94%;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
      }
    }

    .footer {
      max-width: 97%;
      margin: 0 auto;
      /* Pastikan posisinya tetap di tengah */
    }

    .card {
      padding: 2% 7%;
    }

    .divider {
      border-top: 2px solid;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-5">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#">HI, Admin</a>
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
              <a class="nav-link" href="../menu.php">
                <i class="fa fa-sign-out"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="row justify-content-center text-center my-5 header-wrapper">
      <!-- Card 1: Kelola Menu -->
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm rounded-0">
          <div class="card-body">
            <i class="fa-solid fa-utensils mb-3" style="font-size: 5rem"></i>
            <h5 class="card-title mb-3">Kelola Menu</h5>
            <p class="card-text">
              Kelola semua menu yang tersedia di warung Tombo Kangen.
            </p>
            <a href="kelola-menu.php" class="btn btn-primary rounded-0">Kelola Menu</a>
          </div>
        </div>
      </div>

      <!-- Card 2: Kelola Pesanan -->
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm rounded-0">
          <div class="card-body">
            <i class="fa-solid fa-bowl-food mb-3" style="font-size: 5rem"></i>
            <h5 class="card-title mb-3">Kelola Pesanan</h5>
            <p class="card-text">
              Pantau dan atur pesanan pelanggan dengan mudah.
            </p>
            <a href="kelola-pesanan.php" class="btn btn-success rounded-0">Kelola Pesanan</a>
          </div>
        </div>
      </div>

      <!-- Card 3: Kelola Pengguna -->
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm rounded-0">
          <div class="card-body">
            <i class="fa-solid fa-user mb-3" style="font-size: 5rem"></i>
            <h5 class="card-title mb-3">Kelola Pengguna</h5>
            <p class="card-text">
              Atur data pengguna dengan sistem yang efisien.
            </p>
            <a href="kelola-pengguna.php" class="btn btn-warning rounded-0">Kelola Pengguna</a>
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