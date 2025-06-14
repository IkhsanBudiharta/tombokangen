<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['id_user'])) {
  header("Location: masuk.php");
  exit();
}

$user_id = $_SESSION['id_user'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'restaurant');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch user's orders
$query = "
    SELECT o.id AS order_id, o.tanggal, o.waktu, o.status, 
           m.nama, m.gambar, d.jumlah, d.subtotal
    FROM orders o
    JOIN order_details d ON o.id = d.order_id
    JOIN menu m ON d.menu_id = m.id
    WHERE o.user_id = ?
    ORDER BY o.tanggal DESC, o.waktu DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
  $orderId = $row['order_id'];
  if (!isset($orders[$orderId])) {
    $orders[$orderId] = [
      'tanggal' => $row['tanggal'],
      'waktu' => $row['waktu'],
      'status' => $row['status'],
      'items' => []
    ];
  }
  $orders[$orderId]['items'][] = [
    'nama' => $row['nama'],
    'gambar' => $row['gambar'],
    'jumlah' => $row['jumlah'],
    'subtotal' => $row['subtotal']
  ];
}
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

  <script
    src="https://kit.fontawesome.com/de64d1b51f.js"
    crossorigin="anonymous"></script>
  <style>
    body {
      font-family: "Georgia", serif;
    }

    article {
      margin: 20px 20px 20px 20px;
    }

    .header-title {
      font-size: 9vw;
      /* Menggunakan unit fleksibel untuk ukuran font */
      font-weight: 400;
      color: white;
      line-height: 1.2;
    }

    .navbar {
      max-width: 97%;
      margin: 0 auto;
      /* Pastikan posisinya tetap di tengah */
    }

    .header-wrapper {
      margin-top: 4rem;
      /* Mendorong bagian tulisan ke bawah */
      background-color: darkgrey;
      background-size: cover;
      background-position: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      min-height: 74vh;
      max-width: 96%;
      /* Atur lebar maksimal menjadi 90% layar */
      margin-left: auto;
      margin-right: auto;
    }

    .nav-link {
      color: #000;
      position: relative;
      transition: color 0.3s;
    }

    .nav-link:hover {
      color: #d4a89c;
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
        width: 100%;
        margin-left: 0.1rem;
      }
    }

    .floating-icon {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #d4a89c;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease;
    }

    .floating-icon .icon-link {
      color: #fff;
      font-size: 20px;
    }

    .floating-icon:hover {
      background-color: #c08a76;
    }

    .nav-item.dropdown:hover .dropdown-menu {
      display: block;
      margin-top: 0;
      border-radius: 0;
    }

    .card {
      position: relative;
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
      -ms-flex-direction: column;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 0.1rem;
    }

    .card-header:first-child {
      border-radius: calc(0.37rem - 1px) calc(0.37rem - 1px) 0 0;
    }

    .card-header {
      padding: 0.75rem 1.25rem;
      margin-bottom: 0;
      background-color: #fff;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .itemside {
      position: relative;
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      width: 100%;
    }

    .itemside .aside {
      position: relative;
      -ms-flex-negative: 0;
      flex-shrink: 0;
    }

    .img-sm {
      width: 80px;
      height: 80px;
      padding: 7px;
    }

    ul.row,
    ul.row-sm {
      list-style: none;
      padding: 0;
    }

    .itemside .info {
      padding-left: 15px;
      padding-right: 7px;
    }

    .itemside .title {
      display: block;
      margin-bottom: 5px;
      color: #212529;
    }

    p {
      margin-top: 0;
      margin-bottom: 1rem;
    }

    .btn-ajax {
      color: #ffffff;
      background-color: #d4a89c;
      border-color: #d4a89c;
      border-radius: 1px;
    }

    .btn-ajax:hover {
      color: #ffffff;
      background-color: #c08a76;
      border-color: #c08a76;
      border-radius: 1px;
    }

    .footer {
      max-width: 97%;
      margin: 0 auto;
      /* Pastikan posisinya tetap di tengah */
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
    <div class="row align-items-center header-wrapper">
      <div class="container">
      <div class="container">
    <?php if (empty($orders)): ?>
      <div class="text-center py-5">
        <div class="card-body">
          <p>Belum ada pesanan, silahkan <a href="menu.php">pesan</a> terlebih dahulu</p>
        </div>
      </div>
    <?php else: ?>
        <?php foreach ($orders as $orderId => $order): ?>
          <article class="card py-3">
            <header class="card-header">Pesanan Saya</header>
            <div class="card-body">
              <h6>ID Pesanan: <?php echo $orderId; ?></h6>
              <article class="card">
                <div class="card-body row">
                  <div class="col">
                    <strong>Tanggal:</strong><br>
                    <?php echo date('d M Y', strtotime($order['tanggal'])); ?>
                  </div>
                  <div class="col">
                    <strong>Waktu:</strong><br>
                    <?php echo $order['waktu']; ?>
                  </div>
                  <div class="col">
                    <strong>Status:</strong><br>
                    <?php echo $order['status']; ?>
                  </div>
                </div>
              </article>
              <hr>
              <ul class="row">
                <?php foreach ($order['items'] as $item): ?>
                  <li class="col-md-4">
                    <figure class="itemside mb-3">
                      <div class="aside">
                        <img src="admin/uploads/<?php echo $item['gambar']; ?>" class="img-sm border">
                      </div>
                      <figcaption class="info align-self-center">
                        <p class="title">
                          <?php echo $item['nama']; ?><br>
                          JUMLAH: <?php echo $item['jumlah']; ?>
                        </p>
                        <span class="text-muted">Rp. <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                      </figcaption>
                    </figure>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </article>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Footer -->
    
  </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>