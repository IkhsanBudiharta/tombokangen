<?php
session_start();
// koneksi.php
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
// proses.php
// require_once 'koneksi.php';

if (isset($_POST['action'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    if ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $stmt = $koneksi->prepare("UPDATE menu SET nama=?, kategori=?, harga=?, deskripsi=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $nama, $kategori, $harga, $deskripsi, $status, $id);

        if ($stmt->execute()) {
            $_SESSION['pesan'] = 'Menu berhasil diupdate';
        } else {
            $_SESSION['pesan'] = 'Gagal mengupdate menu: ' . $stmt->error;
        }
        $stmt->close();
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Hapus gambar lama
        $query_gambar = "SELECT gambar FROM menu WHERE id='$id'";
        $result = mysqli_query($koneksi, $query_gambar);
        $data = mysqli_fetch_assoc($result);
        if ($data['gambar'] && file_exists("uploads/" . $data['gambar'])) {
            unlink("uploads/" . $data['gambar']);
        }

        $query = "DELETE FROM menu WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = 'Menu berhasil dihapus';
        } else {
            $_SESSION['pesan'] = 'Gagal menghapus menu';
        }
    }
    // Redirect ke halaman yang sama setelah aksi berhasil
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$query = "SELECT * FROM menu ORDER BY id DESC";
$menu = mysqli_query($koneksi, $query);

// Form untuk tambah/edit menu
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
                            <a class="nav-link" href="tambah-menu.php" title="Tambah Menu"> <i class="fa fa-plus"></i></a>
                        </li>
                        <li class="nav-item px-3">
                            <a class="nav-link" href="../menu.php"> <i class="fa fa-sign-out"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container my-5 header-wrapper">
            <!-- Button Tambah Menu -->
            <!-- <div class="text-end mb-4">
                <a href="tambah-menu.php" class="btn btn-primary rounded-0">Tambah Menu</a>
            </div> -->
            <!-- Modify the menu items display section to use PHP -->
            <?php foreach ($menu as $mn): ?>
                <div class="card mb-3 shadow-sm rounded-0">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="uploads/<?= $mn['gambar'] ?>" alt="Minuman 1" class="menu-image">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?= $mn['nama'] ?></h5>
                                <p class="menu-price"><?= $mn['harga'] ?></p>
                                <p class="card-text"><?= $mn['deskripsi'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <!-- Kontainer untuk tombol -->
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Tombol Edit -->
                                <button class="btn btn-success rounded-0" data-bs-toggle="modal" data-bs-target="#editMenuModal<?= $mn['id'] ?>">Edit</button>

                                <!-- Tombol Hapus -->
                                <form action="" method="POST">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $mn['id'] ?>">
                                    <button type="submit" class="btn btn-danger rounded-0">Hapus</button>
                                </form>
                            </div>
                            <div class="modal fade" id="editMenuModal<?= $mn['id'] ?>" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-0">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="action" value="update">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?= $mn['id'] ?>">

                                                    <div class="mb-3">
                                                        <label for="nama<?= $mn['id'] ?>" class="form-label">Nama Menu</label>
                                                        <input type="text" name="nama" id="nama<?= $mn['id'] ?>" class="form-control" value="<?= $mn['nama'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="kategori<?= $mn['id'] ?>" class="form-label">Kategori</label>
                                                        <select name="kategori" id="kategori<?= $mn['id'] ?>" class="form-select" required>
                                                            <option value="makanan" <?= $mn['kategori'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                                                            <option value="minuman" <?= $mn['kategori'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="harga<?= $mn['id'] ?>" class="form-label">Harga</label>
                                                        <input type="number" name="harga" id="harga<?= $mn['id'] ?>" class="form-control" value="<?= $mn['harga'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="deskripsi<?= $mn['id'] ?>" class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" id="deskripsi<?= $mn['id'] ?>" class="form-control" rows="3" required><?= $mn['deskripsi'] ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="status<?= $mn['id'] ?>" class="form-label">Status Menu</label>
                                                        <select name="status" id="status<?= $mn['id'] ?>" class="form-select" required>
                                                            <option value="tersedia" <?= $mn['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                                            <option value="tidak_tersedia" <?= $mn['status'] == 'tidak_tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="gambar<?= $mn['id'] ?>" class="form-label">Gambar</label>
                                                        <input type="file" name="gambar" id="gambar<?= $mn['id'] ?>" class="form-control">
                                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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