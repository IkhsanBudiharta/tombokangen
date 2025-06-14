<?php
session_start();
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";  // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah pengguna sudah login dan apakah role-nya adalah 'admin'
if ($_SESSION['role'] != 'Admin') {
    // Jika tidak login atau bukan admin, arahkan ke halaman utama
    header('Location: ../menu.php');
    exit();
}

// Mengambil data pengguna
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Hapus pengguna
// Hapus pengguna
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Hapus data di tabel `order_details` yang terkait dengan `orders`
        $delete_order_details_sql = "DELETE FROM order_details WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)";
        $stmt = $conn->prepare($delete_order_details_sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();

        // Hapus data di tabel `orders` yang terkait dengan pengguna
        $delete_orders_sql = "DELETE FROM orders WHERE user_id = ?";
        $stmt = $conn->prepare($delete_orders_sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();

        // Hapus data pengguna dari tabel `users`
        $delete_user_sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_user_sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();

        // Commit transaksi
        $conn->commit();
        echo "User deleted successfully";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $conn->rollback();
        echo "Error deleting user: " . $e->getMessage();
    }

    // Redirect kembali ke halaman
    header("Location: kelola-pengguna.php");
    exit();
}


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
                    |  =========== Responsive Index.php =========== |
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
        <div class="container mt-4">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php
                // Tampilkan pengguna
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col">
                            <div class="card shadow-sm h-100" style="border-radius: 0;">
                                <div class="card-body text-center">
                                    <a href="#" class="d-block mb-4"> <i class="fa fa-user fa-5x"></i> </a>
                                    <h5 class="card-title">Nama: <span>' . $row["username"] . '</span></h5>
                                    <p class="card-text">Nomor Telepon: <span>' . $row["no_telp"] . '</span></p>
                                    <p class="card-text">Email: <span>' . $row["email"] . '</span></p>
                                    <p class="card-text">Role: <span>' . $row["role"] . '</span></p>
                                </div>';

                        // Tampilkan tombol "Delete User" jika role bukan Admin
                        if ($row['role'] !== 'Admin') {
                            echo '
                                <div class="text-center">
                                    <a href="?delete_id=' . $row["id"] . '" class="btn btn-danger rounded-0 mb-3">Delete User</a>
                                </div>';
                        }

                        echo '
                            </div>
                        </div>';
                    }
                } else {
                    echo "No users found.";
                }
                ?>

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
                        <a href="information.php" class="text-decoration-none" style="color: #000;">All Rights Reserved</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>