<?php
session_start();
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

// Mengambil order_id dari URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if ($order_id !== null) {
    // Query untuk mengambil semua data dari orders berdasarkan order_id
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();  // Mengambil data sebagai array asosiatif
    $stmt->close();

    // Menyimpan path file bukti pembayaran jika ditemukan
    if ($order && isset($order['bukti_pembayaran'])) {
        $paymentProofImage = "uploads/" . $order['bukti_pembayaran'];  // Menyesuaikan dengan lokasi gambar
    } else {
        $paymentProofImage = null;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 50px auto;
            width: 80%;
            max-width: 600px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 0;
        }

        .proof-image {
            display: block;
            margin: 20px auto;
            width: 100%;
            max-width: 400px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 0;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 0;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container rounded-0">
        <h1>Bukti Pembayaran</h1>
        <?php if (isset($paymentProofImage) && file_exists($paymentProofImage)): ?>
            <img src="<?php echo $paymentProofImage; ?>" alt="Bukti Pembayaran" class="proof-image">
        <?php else: ?>
            <p>Bukti pembayaran tidak ditemukan.</p>
        <?php endif; ?>
        <a href="kelola-pesanan.php" class="back-button rounded-0">Kembali</a>
    </div>
</body>

</html>