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

// Cek apakah pengguna sudah login dan apakah role-nya adalah 'admin'
if ($_SESSION['role'] != 'Admin') {
    // Jika tidak login atau bukan admin, arahkan ke halaman utama
    header('Location: ../menu.php');
    exit();
}

// Cek apakah form disubmit dan data lengkap
if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Query untuk memperbarui status pesanan
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind parameter dan eksekusi query
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Ambil semua pesanan
$queryAllOrders = "
    SELECT o.id AS order_id, o.tanggal, o.waktu, o.metode_pembayaran, o.status, o.bukti_pembayaran,
        d.menu_id, d.jumlah, d.subtotal, m.nama, m.gambar
    FROM orders o
    JOIN order_details d ON o.id = d.order_id
    JOIN menu m ON d.menu_id = m.id
    ORDER BY o.id DESC
";

$result = $conn->query($queryAllOrders);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tombo Kangen | Admin</title>
    <link rel="icon" href="https://ik.imagekit.io/rn4jfgmjp/20241205_000510.png?updatedAt=1733331993815" type="image/png">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- css -->


    <script src="https://kit.fontawesome.com/de64d1b51f.js" crossorigin="anonymous"></script>


    <style>
        body {
            font-family: 'Georgia', serif;
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
            padding: 2% 7%
        }

        .divider {
            border-top: 2px solid
        }

        .menu-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .menu-image {
                width: 150px;
                /* Membesar 20% */
                height: 150px;
            }
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
            border-radius: 0.10rem
        }

        .card-header:first-child {
            border-radius: calc(0.37rem - 1px) calc(0.37rem - 1px) 0 0
        }

        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1)
        }

        .itemside {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            width: 100%
        }

        .itemside .aside {
            position: relative;
            -ms-flex-negative: 0;
            flex-shrink: 0
        }

        .img-sm {
            width: 80px;
            height: 80px;
            padding: 7px
        }

        ul.row,
        ul.row-sm {
            list-style: none;
            padding: 0
        }

        .itemside .info {
            padding-left: 15px;
            padding-right: 7px
        }

        .itemside .title {
            display: block;
            margin-bottom: 5px;
            color: #212529
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem
        }

        .btn-ajax {
            color: #ffffff;
            background-color: #d4a89c;
            border-color: #d4a89c;
            border-radius: 1px
        }

        .btn-ajax:hover {
            color: #ffffff;
            background-color: #c08a76;
            border-color: #c08a76;
            border-radius: 1px
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white mb-5">
            <div class="container">
                <a class="navbar-brand fw-bold" href="#">HI, Admin</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item px-3">
                            <a class="nav-link" href="../menu.php"> <i class="fa fa-sign-out"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="row align-items-center header-wrapper">
            <div class="container">
                <?php
                $currentOrderId = null; // Untuk membedakan pesanan
                while ($row = $result->fetch_assoc()) {
                    // Jika order_id berubah, tutup pesanan sebelumnya dan mulai pesanan baru
                    if ($currentOrderId !== $row['order_id']) {
                        if ($currentOrderId !== null) {
                ?>
                            </ul>
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="bukti-pembayaran.php?order_id=<?php echo $row['order_id']; ?>" id="cekBukti" class="btn btn-ajax" data-abc="true"> Cek Bukti Pembayaran</a>
                                <a href="#" id="perbaruiStatus" class="btn btn-ajax" data-abc="true"> Perbarui Status</a>
                            </div>
            </div>
            </article>
        <?php
                        }
                        // Mulai pesanan baru
                        $currentOrderId = $row['order_id'];
        ?>
        <article class="card py-3 shadow-sm mb-3">
            <div class="card-body">
                <h6>ID Pesanan: <?php echo $row['order_id']; ?></h6> <!-- Menampilkan ID Pesanan dinamis -->
                <article class="card">
                    <div class="card-body row">
                        <div class="col"><strong>Tanggal:</strong> <br><?php echo date('j F Y', strtotime($row['tanggal'])); ?></div>
                        <div class="col"><strong>Waktu:</strong> <br><?php echo $row['waktu']; ?></div>
                        <div class="col"><strong>Metode:</strong> <br><?php echo $row['metode_pembayaran']; ?></div>
                        <div class="col"><strong>Status:</strong> <br><?php echo $row['status']; ?></div>
                    </div>
                </article>
                <!-- Overlay untuk Form Ubah Status -->
                <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
                    <div style="background: white; margin: 10% auto; padding: 20px; width: 40%; border-radius: 0;">
                        <h4>Ubah Status Pesanan</h4>
                        <form id="" action="" method="POST">
                            <div class="form-group">
                                <label for="status">Pilih Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="Diproses">Diproses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <!-- Input hidden untuk menyimpan order_id -->
                            <input type="name" name="order_id" id="order_id" value="<?php echo $row['order_id']; ?>">
                            <input type="hidden" name="action" value="update_status">
                            <button type="submit" class="btn btn-primary mt-2 rounded-0">Simpan</button>
                            <button type="button" id="closeOverlay" class="btn btn-secondary mt-2 rounded-0">Batal</button>
                        </form>
                    </div>
                </div>
                <hr>
                <ul class="row">
                <?php
                    }
                ?>
                <li class="col-md-4">
                    <figure class="itemside mb-3">
                        <div class="aside">
                            <img src="uploads/<?php echo $row['gambar']; ?>" class="img-sm border">
                        </div>
                        <figcaption class="info align-self-center">
                            <p class="title"><?php echo $row['nama']; ?> <br> JUMLAH : <?php echo $row['jumlah']; ?></p>
                            <span class="text-muted">Rp. <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></span>
                        </figcaption>
                    </figure>
                </li>
            <?php
                }
                // Tutup pesanan terakhir jika ada
                if ($currentOrderId !== null) {
            ?>
                </ul>
                <hr>
                <div class="d-flex justify-content-end gap-2">
                <a href="bukti-pembayaran.php?order_id=<?php echo $currentOrderId; ?>" class="btn btn-ajax" data-abc="true">Cek Bukti Pembayaran</a>
                    <a href="#" id="perbaruiStatus" class="btn btn-ajax" data-abc="true"> Perbarui Status</a>
                </div>
            </div>
        </article>
    <?php
                }
    ?>
        </div>
    </div>

    <script>
        // Tombol "Perbarui Status"
        document.getElementById('perbaruiStatus').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('overlay').style.display = 'block';
        });

        // Tombol "Batal" di Overlay
        document.getElementById('closeOverlay').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('overlay').style.display = 'none';
        });

        // Form "Ubah Status"
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedStatus = document.getElementById('status').value;
            alert('Status berhasil diperbarui menjadi: ' + selectedStatus);
            document.getElementById('overlay').style.display = 'none';
        });
    </script>





    <!-- Footer -->
    <div class="container-fluid mt-5 footer">
        <div class="divider mb-4"></div>

        <div class="row text-center text-sm-start">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <p><i class="fa fa-copyright"></i> 2024 Tombo Kangen</p>
            </div>
            <div class="col-sm-6">
                <div class="d-flex justify-content-center justify-content-sm-end">
                    <a href="information.html" class="text-decoration-none" style="color: #000;">All Rights Reserved</a>
                </div>
            </div>
        </div>
    </div>



    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>