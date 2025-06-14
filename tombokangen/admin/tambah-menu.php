<?php
session_start();

$koneksi = mysqli_connect('localhost', 'root', '', 'restaurant');
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek apakah pengguna sudah login dan apakah role-nya adalah 'admin'
if ($_SESSION['role'] != 'Admin') {
    // Jika tidak login atau bukan admin, arahkan ke halaman utama
    header('Location: ../menu.php');
    exit();
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $harga = $_POST['harga'];
        $deskripsi = $_POST['deskripsi'];

        if ($_POST['action'] === 'add') {
            $gambar = $_FILES['gambar']['name'];
            $target = "uploads/" . basename($gambar);

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                $stmt = $koneksi->prepare("INSERT INTO menu (nama, kategori, harga, deskripsi, gambar, status) VALUES (?, ?, ?, ?, ?, 'tersedia')");
                $stmt->bind_param("ssiss", $nama, $kategori, $harga, $deskripsi, $gambar);

                if ($stmt->execute()) {
                    $_SESSION['pesan'] = 'Menu berhasil ditambahkan';
                } else {
                    $_SESSION['pesan'] = 'Gagal menambahkan menu';
                }
                $stmt->close();
            } else {
                $_SESSION['pesan'] = 'Gagal upload gambar';
            }
        }
    }
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
        <div class="row justify-content-center text-center my-5 header-wrapper">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm rounded-0">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Tambah Menu</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select id="kategori" name="kategori" class="form-select rounded-0" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="makanan">Makanan</option>
                                    <option value="minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Menu</label>
                                <input type="text" id="nama" name="nama" class="form-control rounded-0" placeholder="Masukkan nama menu" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" id="harga" name="harga" class="form-control rounded-0" placeholder="Masukkan harga" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control rounded-0" rows="3" placeholder="Masukkan deskripsi menu" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar</label>
                                <input type="file" id="gambar" name="gambar" class="form-control rounded-0" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0 w-100">Simpan</button>
                            <div class="mt-3">
                                <a href="kelola-menu.php" class="btn btn-secondary rounded-0 w-100">Batal</a>
                            </div>
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
                        <a href="information.php" class="text-decoration-none" style="color: #000;">All Rights Reserved</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>