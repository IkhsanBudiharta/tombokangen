<?php
session_start();

// Menghapus sesi yang sudah ada
session_unset();

// Menghancurkan sesi
session_destroy();

// Arahkan kembali ke halaman login atau halaman utama
header("Location: index.php");
exit();
