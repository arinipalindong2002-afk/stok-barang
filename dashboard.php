
<?php
session_start();
require_once "koneksi.php";

/* Cek login */
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   DATA DASHBOARD
   ========================= */

/* Total barang */
$q_barang = mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang");
$data_barang = mysqli_fetch_assoc($q_barang);
$total_barang = (int)$data_barang['total'];

/* Total stok */
$q_stok = mysqli_query($conn, "SELECT COALESCE(SUM(stok),0) AS total FROM barang");
$data_stok = mysqli_fetch_assoc($q_stok);
$total_stok = (int)$data_stok['total'];

/* Total stok masuk */
$q_masuk = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(jumlah),0) AS total 
     FROM transaksi 
     WHERE jenis = 'masuk'"
);
$data_masuk = mysqli_fetch_assoc($q_masuk);
$total_masuk = (int)$data_masuk['total'];

/* Total stok keluar */
$q_keluar = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(jumlah),0) AS total 
     FROM transaksi 
     WHERE jenis = 'keluar'"
);
$data_keluar = mysqli_fetch_assoc($q_keluar);
$total_keluar = (int)$data_keluar['total'];

/* Transaksi terbaru */
$q_transaksi = mysqli_query(
    $conn,
    "SELECT 
        transaksi.*,
        barang.kode,
        barang.nama
     FROM transaksi
     LEFT JOIN barang ON barang.id = transaksi.barang_id
     ORDER BY transaksi.id DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - Laporan Stok Barang</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7fb;
    color: #172033;
}

/* =========================
   SIDEBAR
   ========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #0757c9, #063b91);
    padding: 25px 18px;
    color: white;
}

.logo-area {
    text-align: center;
    margin-bottom: 30px;
}

.logo-area img {
    width: 170px;
    max-width: 90%;
    height: auto;
    background: white;
    border-radius: 12px;
    padding: 10px;
}

.company-name {
    font-size: 14px;
    margin-top: 12px;
    line-height: 1.5;
    font-weight: bold;
}

.menu {
    margin-top: 25px;
}

.menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: white;
    padding: 13px 15px;
    margin-bottom: 8px;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.2s;
}

.menu a:hover,
.menu a.active {
    background: rgba(255,255,255,0.18);
}

.menu-icon {
    width: 25px;
    text-align: center;
    font-size: 18px;
}

/* =========================
   MAIN
   ========================= */

.main {
    margin-left: 250px;
    padding: 25px;
}

/* TOPBAR */

.topbar {
    background: white;
    border-radius: 16px;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.top-title h2 {
    font-size: 22px;
    color: #173b7a;
}

.top-title p {
    margin-top: 5px;
    color: #7a8497;
    font-size: 13px;
}

.user-box {
    background: #eef5ff;
    padding: 10px 15px;
    border-radius: 10px;
    color: #0757c9;
    font-weight: bold;
}

/* =========================
   WELCOME
   ========================= */

.welcome {
    background: linear-gradient(135deg, #0757c9, #1475e8);
    color: white;
    padding: 28px;
    border-radius: 18px;
    margin-bottom: 25px;
    box-shadow: 0 10px 25px rgba(7,87,201,0.2);
}

.welcome h1 {
    font-size: 28px;
    margin-bottom: 8px;
}

.welcome p {
    opacity: 0.9;
}

/* =========================
   CARDS
   ========================= */

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.card {
    background: white;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 23px;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 14px;
    color: #7b8494;
    margin-bottom: 8px;
}

.card .number {
    font-size: 30px;
    font-weight: bold;
    color: #172033;
}

.card.blue .card-icon {
    background: #e7f0ff;
}

.card.green .card-icon {
    background: #e8f8ef;
}

.card.orange .card-icon {
    background: #fff2df;
}

.card.red .card-icon {
    background: #ffe9e9;
}

/* =========================
   TRANSAKSI
   ========================= */

.panel {
    background: white;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.panel-header h2 {
    font-size: 18px;
    color: #173b7a;
}

.btn {
    background: #0757c9;
    color: white;
    text-decoration: none;
    padding: 9px 15px;
    border-radius: 8px;
    font-size: 13px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f4f7fb;
    padding: 13px;
    text-align: left;
    font-size: 13px;
    color: #667085;
}

td {
    padding: 13px;
    border-bottom: 1px solid #edf0f5;
    font-size: 13px;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.badge-masuk {
    background: #e8f8ef;
    color: #16834a;
}

.badge-keluar {
    background: #ffe9e9;
    color: #d63636;
}

/* =========================
   RESPONSIVE
   ========================= */

@media (max-width: 1000px) {

    .sidebar {
        width: 210px;
    }

    .main {
        margin-left: 210px;
    }

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .menu {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 5px;
    }

    .menu a {
        margin-bottom: 0;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .cards {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .card {
        padding: 16px;
    }

    .card .number {
        font-size: 25px;
    }

    .welcome h1 {
        font-size: 23px;
    }
}

@media (max-width: 480px) {

    .cards {
        grid-template-columns: 1fr;
    }

    .menu {
        grid-template-columns: 1fr;
    }

    .logo-area img {
        width: 140px;
    }
}

</style>
</head>

<body>

<!-- =========================
     SIDEBAR
     ========================= -->

<div class="sidebar">

    <div class="logo-area">

        <!-- LOGO PERUSAHAAN -->
        <img src="logo.jpg" alt="Logo Perusahaan">

        <div class="company-name">
            PT Prima Sarana Gemilang
        </div>

    </div>

    <div class="menu">

        <a href="dashboard.php" class="active">
            <span class="menu-icon">🏠</span>
            Dashboard
        </a>

        <a href="barang.php">
            <span class="menu-icon">📦</span>
            Data Barang
        </a>

        <a href="transaksi.php">
            <span class="menu-icon">🔄</span>
            Transaksi
        </a>

        <a href="laporan.php">
            <span class="menu-icon">📊</span>
            Laporan
        </a>

        <a href="logout.php">
            <span class="menu-icon">🚪</span>
            Keluar
        </a>

    </div>

</div>


<!-- =========================
     MAIN
     ========================= -->

<div class="main">

    <div class="topbar">

<div class="top-title">
            <h2>Dashboard Stok Barang</h2>
            <p>Sistem informasi pengelolaan stok barang</p>
        </div>

        <div class="user-box">
            👤 <?= htmlspecialchars($_SESSION['username']); ?>
        </div>

    </div>


    <!-- WELCOME -->

    <div class="welcome">

        <h1>Selamat Datang 👋</h1>

        <p>
            Kelola data barang, transaksi stok masuk dan stok keluar
            dengan mudah melalui sistem ini.
        </p>

    </div>


    <!-- STATISTIC CARDS -->

    <div class="cards">

        <div class="card blue">

            <div class="card-icon">
                📦
            </div>

            <h3>Total Barang</h3>

            <div class="number">
                <?= number_format($total_barang); ?>
            </div>

        </div>


        <div class="card green">

            <div class="card-icon">
                📊
            </div>

            <h3>Total Stok</h3>

            <div class="number">
                <?= number_format($total_stok); ?>
            </div>

        </div>


        <div class="card orange">

            <div class="card-icon">
                📥
            </div>

            <h3>Stok Masuk</h3>

            <div class="number">
                <?= number_format($total_masuk); ?>
            </div>

        </div>


        <div class="card red">

            <div class="card-icon">
                📤
            </div>

            <h3>Stok Keluar</h3>

            <div class="number">
                <?= number_format($total_keluar); ?>
            </div>

        </div>

    </div>


    <!-- TRANSAKSI TERBARU -->

    <div class="panel">

        <div class="panel-header">

            <h2>Transaksi Terbaru</h2>

            <a href="transaksi.php" class="btn">
                Lihat Semua
            </a>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                    </tr>

                </thead>

                <tbody>

                <?php
                $no = 1;

                if (mysqli_num_rows($q_transaksi) > 0):

                    while ($row = mysqli_fetch_assoc($q_transaksi)):
                ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <?= htmlspecialchars($row['kode'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama'] ?? '-'); ?>
                        </td>

                        <td>

                            <?php if ($row['jenis'] == 'masuk'): ?>

                                <span class="badge badge-masuk">
                                    STOK MASUK
                                </span>

                            <?php else: ?>

                                <span class="badge badge-keluar">
                                    STOK KELUAR
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= number_format((int)$row['jumlah']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['tanggal']); ?>
                        </td>

                    </tr>

                <?php
                    endwhile;

                else:
                ?>

                    <tr>

                        <td colspan="6" style="text-align:center;">
                            Belum ada transaksi.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>