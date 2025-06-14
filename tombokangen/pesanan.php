<?php
// Mulai sesi
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
  header("Location: masuk.php");
  exit();
}

// Ambil user_id dari sesi
$user_id = $_SESSION['id_user'];

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'restaurant';

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data pesanan berdasarkan user_id dan lakukan join dengan tabel menu
// Setelah pembaruan keranjang atau penghapusan item, query ulang data pesanan
$sql = "SELECT k.*, m.nama, m.harga, m.gambar, m.deskripsi 
        FROM keranjang k 
        JOIN menu m ON k.menu_id = m.id 
        WHERE k.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mengupdate jumlah pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_keranjang'])) {
  $item_id = $_POST['item_id'];
  $jumlah = $_POST['jumlah'];

  // Pastikan jumlah tidak negatif
  if ($_POST['update_keranjang'] == 'minus') {
    $jumlah = max(1, $jumlah - 1); // Kurangi jumlah dengan minimum 1
  } elseif ($_POST['update_keranjang'] == 'plus') {
    $jumlah++; // Tambah jumlah
  }

  // Update jumlah di keranjang
  $update_sql = "UPDATE keranjang SET jumlah = ? WHERE user_id = ? AND menu_id = ?";
  $update_stmt = $conn->prepare($update_sql);
  $update_stmt->bind_param("iii", $jumlah, $user_id, $item_id);
  $update_stmt->execute();

  // Setelah update, reload halaman
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}


// Menangani penghapusan item dari keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_item'])) {
  $menu_id = $_POST['item_id']; // Mengambil item_id yang akan dihapus

  // Pastikan item_id sesuai dengan yang ada di tabel keranjang
  $delete_sql = "DELETE FROM keranjang WHERE user_id = ? AND menu_id = ?";
  $delete_stmt = $conn->prepare($delete_sql);
  $delete_stmt->bind_param("ii", $user_id, $menu_id); // Binding user_id dan menu_id untuk menghapus item
  $delete_stmt->execute();

  // Setelah penghapusan, reload halaman
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}


// Cek apakah form sudah disubmit
if (isset($_POST['action']) && $_POST['action'] === 'order') {
  try {
    if (!isset($_SESSION['id_user'])) {
      throw new Exception('User belum login.');
    }

    $user_id = $_SESSION['id_user']; // Ambil user yang sedang login

    // Validasi input
    if (!isset($_POST['total'], $_POST['payment'])) {
      throw new Exception('Data pesanan tidak lengkap.');
    }

    $total = ($_POST['total']);
    $metode_pembayaran = $_POST['payment'];
    $bukti_pembayaran = null;

    // Upload bukti pembayaran jika metode Transfer
    if ($metode_pembayaran === 'Transfer' && isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = "admin/uploads/";
      $fileName = time() . "_" . basename($_FILES['bukti']['name']);
      $uploadFile = $uploadDir . $fileName;

      if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadFile)) {
        throw new Exception('Gagal mengunggah bukti pembayaran.');
      }
      $bukti_pembayaran = $fileName;
    }

    // Simpan data pesanan ke tabel orders
    $conn->begin_transaction(); // Mulai transaksi

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, metode_pembayaran, bukti_pembayaran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $total, $metode_pembayaran, $bukti_pembayaran);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Ambil data dari keranjang untuk user yang sedang login
    $query = "SELECT menu_id, jumlah, (jumlah * harga) AS subtotal FROM keranjang k
                JOIN menu m ON k.menu_id = m.id
                WHERE k.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Simpan data ke tabel order_details
    while ($row = $result->fetch_assoc()) {
      $menu_id = $row['menu_id'];
      $jumlah = $row['jumlah'];
      $subtotal = $row['subtotal'];

      $detailStmt = $conn->prepare("INSERT INTO order_details (order_id, menu_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
      $detailStmt->bind_param("iiid", $order_id, $menu_id, $jumlah, $subtotal);
      $detailStmt->execute();
      $detailStmt->close();
    }

    $stmt->close();

    // Hapus keranjang setelah pesanan selesai
    $deleteCart = $conn->prepare("DELETE FROM keranjang WHERE user_id = ?");
    $deleteCart->bind_param("i", $user_id);
    $deleteCart->execute();
    $deleteCart->close();

    $conn->commit(); // Selesaikan transaksi

    // echo "Pesanan berhasil disimpan!";
  } catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Terjadi kesalahan: " . $e->getMessage();
  }
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

  <!-- css -->
  <link rel="stylesheet" href="style.css" />

  <script
    src="https://kit.fontawesome.com/de64d1b51f.js"
    crossorigin="anonymous"></script>
  <style>
    body {
      font-family: "Georgia", serif;
    }

    .card-pesanan {
      border-radius: 0;
    }

    .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.overlay-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 20px;
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
    <div class="row align-items-center header-wrapper-pesanan">
      <!-- Cart Items -->

      <div class="col-md-8 py-3">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
        ?>
            <!-- Item 1 -->
            <div class="card card-pesanan mb-3">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-3">
                    <img
                      src="admin/uploads/<?php echo $row['gambar']; ?>"
                      class="img-fluid rounded"
                      alt="Makanan A" />
                  </div>
                  <div class="col-md-6">
                    <h5 class="card-title"><?php echo $row['nama']; ?></h5>
                    <p class="card-text text-muted">Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                    <p class="card-text">
                      ><?php echo $row['deskripsi']; ?>
                    </p>
                  </div>
                  <div class="col-md-3">
                    <div class="input-group mb-3">
                      <form method="POST" class="input-group mb-3" id="update_keranjang">
                        <button class="btn btn-outline-secondary rounded-0" type="submit" name="update_keranjang" value="minus">
                          -
                        </button>
                        <input type="number" class="form-control text-center" name="jumlah" value="<?php echo $row['jumlah']; ?>" min="1" />
                        <button class="btn btn-outline-secondary rounded-0" type="submit" name="update_keranjang" value="plus">
                          +
                        </button>
                        <input type="hidden" name="item_id" value="<?php echo $row['menu_id']; ?>" />
                      </form>
                    </div>
                    <div>
                      <form method="POST">
                        <button class="btn btn-outline-danger btn-sm w-100 btn-hapus rounded-0" name="hapus_item" value="hapus">
                          Hapus
                        </button>
                        <input type="hidden" name="item_id" value="<?php echo $row['menu_id']; ?>" />
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "<p>Tidak ada pesanan ditemukan.</p>";
        }
        ?>
      </div>

      <!-- Order Summary -->
      <div class="col-md-4 py-3 order-summary">
        <div class="card card-pesanan">
          <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="order">
              <h5 class="card-title mb-4" style="font-weight: bold">
                Ringkasan Pesanan
              </h5>
              <?php
              $user_id = $_SESSION['id_user'];

              // Query untuk mengambil data keranjang dan menu terkait
              $query = "
                SELECT k.id, k.user_id, k.menu_id, k.jumlah, m.nama, m.harga 
                FROM keranjang k
                JOIN menu m ON k.menu_id = m.id
                WHERE k.user_id = $user_id
              ";

              $result = mysqli_query($conn, $query);

              // Variabel untuk subtotal dan total
              $subtotal = 0;
              $total = 0;

              // Array untuk menyimpan data menu dalam keranjang
              $menuItems = [];

              // Ambil semua data dan hitung subtotal
              while ($row = mysqli_fetch_assoc($result)) {
                $harga = $row['harga'];
                $jumlah = $row['jumlah'];
                $subtotal += $harga * $jumlah;

                // Simpan data untuk ditampilkan di UI
                $menuItems[] = $row;
              }

              $total = $subtotal;
              ?>
              <div class="mb-3">
                <?php foreach ($menuItems as $mn) : ?>
                  <div class="d-flex justify-content-between mb-2">
                    <span><?php echo $mn['nama']; ?></span>
                    <span>Rp. <?php echo number_format($mn['harga'] * $mn['jumlah'], 0, ',', '.'); ?></span>
                    <input type="hidden" name="subtotal" id="subtotal" value="<?php echo number_format($mn['harga'] * $mn['jumlah']); ?>">
                    <input type="hidden" name="menu_id" id="menu_id" value="<?php echo number_format($mn['id']); ?>">
                  </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between fw-bold">
                  <span>TOTAL</span>
                  <span>Rp. <?php echo number_format($total, 0, ',', '.'); ?></span>
                  <input type="hidden" name="total" id="total" value="<?php echo number_format($total); ?>">
                </div>
              </div>
              <div class="mb-3">
                <label for="payment" class="form-label">Metode Pembayaran</label>
                <select class="form-select bg-light rounded-0" id="payment" name="payment">
                  <option class="rounded-0" value="Transfer">
                    Transfer (Upload Bukti)
                  </option>
                  <option class="rounded-0" value="Bayar Di Tempat">Bayar Di Tempat</option>
                </select>
                <a
                  href="#"
                  class="d-flex mt-2 text-primary justify-content-end show-account"
                  style="font-size: 0.9rem">tampilkan No. Rekening</a>
              </div>

              <div id="accountOverlay" class="overlay">
                <div class="overlay-content rounded-0">
                    <span class="close-btn">&times;</span>
                    <h4>Informasi Rekening</h4>
                    <p>096901037848536 - BRI Yoga Prasetya</p>
                </div>
            </div>

            <script>
            document.querySelector('.show-account').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('accountOverlay').style.display = 'block';
            });

            document.querySelector('.close-btn').addEventListener('click', function() {
                document.getElementById('accountOverlay').style.display = 'none';
            });

            document.getElementById('accountOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
            </script>

              <!-- jika transfer maka tampilkan inputan upload bukti, jika bayar di tempat maka tidak tampilkan inputan upload bukti -->
              <div class="mb-3">
                <label for="bukti" class="form-label">Upload Bukti</label>
                <input
                  type="file"
                  class="form-control bg-light rounded-0" name="bukti"
                  id="bukti"
                  accept="image/*" />
              </div>

              <button class="btn btn-dark w-100 rounded-0">PESAN</button>
            </form>
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