<?php
session_start();
require_once "koneksi.php";

/* =========================
   DATA TRANSAKSI TERBARU
========================= */

$sql = "
    SELECT
        t.id,
        t.tanggal,
        b.kode,
        b.nama,
        b.satuan,
        t.jenis,
        t.jumlah,
        t.keterangan
    FROM transaksi t
    LEFT JOIN barang b ON b.id = t.barang_id
    ORDER BY t.tanggal DESC, t.id DESC
    LIMIT 10
";

$result = mysqli_query($conn, $sql);


/* =========================
   TOTAL TRANSAKSI
========================= */

$qTotal = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM transaksi"
);

$dataTotal = mysqli_fetch_assoc($qTotal);
$totalTransaksi = $dataTotal['total'];


/* =========================
   TOTAL MASUK
========================= */

$qMasuk = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(jumlah),0) AS total
     FROM transaksi
     WHERE jenis = 'masuk'"
);

$dataMasuk = mysqli_fetch_assoc($qMasuk);
$totalMasuk = $dataMasuk['total'];


/* =========================
   TOTAL KELUAR
========================= */

$qKeluar = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(jumlah),0) AS total
     FROM transaksi
     WHERE jenis = 'keluar'"
);

$dataKeluar = mysqli_fetch_assoc($qKeluar);
$totalKeluar = $dataKeluar['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Transaksi - Sistem Stok Barang</title>

<style>

/* =========================
   RESET
========================= */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
}


/* =========================
   LAYOUT
========================= */

.wrapper {
    display: flex;
    min-height: 100vh;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
    width: 250px;
    background: linear-gradient(
        180deg,
        #062fd3,
        #0645d6,
        #0734b8
    );

    color: white;
    padding: 25px 15px;

    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
}


/* LOGO */

.logo {
    background: white;
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    margin-bottom: 10px;
}

.logo img {
    max-width: 150px;
    max-height: 60px;
}


/* NAMA PERUSAHAAN */

.company {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 30px;
}


/* MENU */

.menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.menu a {
    color: white;
    text-decoration: none;

    padding: 14px 16px;

    border-radius: 12px;

    font-weight: bold;

    display: flex;
    align-items: center;

    gap: 12px;

    transition: .2s;
}

.menu a:hover {
    background: rgba(255,255,255,.15);
}

.menu a.active {
    background: rgba(255,255,255,.18);
    box-shadow:
        0 4px 12px rgba(0,0,0,.12);
}


/* =========================
   CONTENT
========================= */

.content {
    margin-left: 250px;
    width: calc(100% - 250px);

    padding: 30px;
}


/* =========================
   TOP
========================= */

.top {
    background: white;

    padding: 20px 25px;

    border-radius: 16px;

    margin-bottom: 20px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    box-shadow:
        0 5px 20px rgba(0,0,0,.06);
}

.top h1 {
    margin: 0;
    font-size: 25px;
}

.admin {
    background: #eff6ff;
    color: #174ea6;

    padding: 9px 15px;

    border-radius: 20px;

    font-weight: bold;
}


/* =========================
   HERO
========================= */

.hero {
    background:
        linear-gradient(
            135deg,
            #0645d6,
            #1677ff
        );

    color: white;

    padding: 30px;

    border-radius: 18px;

    margin-bottom: 22px;

    box-shadow:
        0 8px 25px rgba(0,60,200,.18);
}

.hero h2 {
    margin: 0 0 8px;
    font-size: 28px;
}

.hero p {
    margin: 0;
    opacity: .9;
}


/* =========================
   QUICK MENU
========================= */

.quick {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;

    margin-bottom: 22px;
}

.quick-card {
    background: white;

    border-radius: 18px;

    padding: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.07);

    transition: .2s;
}

.quick-card:hover {
    transform: translateY(-3px);

    box-shadow:
        0 10px 25px rgba(0,0,0,.10);
}

.quick-icon {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 15px;

    background: #eff6ff;

    font-size: 28px;

    margin-bottom: 15px;
}

.quick-card h3 {
    margin: 0 0 7px;
    font-size: 20px;
}

.quick-card p {
    margin: 0 0 18px;
    color: #64748b;
    font-size: 14px;
}

.quick-btn {
    display: inline-block;

    text-decoration: none;

    padding: 11px 18px;

    border-radius: 10px;

    color: white;

    font-weight: bold;
}

.btn-masuk {
    background: #198754;
}

.btn-keluar {
    background: #dc3545;
}


/* =========================
   STATISTIK
========================= */

.stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 18px;

    margin-bottom: 22px;
}

.stat {
    background: white;

    border-radius: 16px;

    padding: 22px;

    box-shadow:
        0 5px 18px rgba(0,0,0,.06);
}

.stat-title {
    color: #64748b;

    font-size: 14px;

    margin-bottom: 8px;
}

.stat-number {
    font-size: 30px;

    font-weight: bold;
}

.blue {
    color: #1769e0;
}

.green {
    color: #198754;
}

.red {
    color: #dc3545;
}


/* =========================
   TABLE
========================= */

.table-card {
    background: white;

    border-radius: 18px;

    overflow: hidden;

    box-shadow:
        0 5px 20px rgba(0,0,0,.06);
}

.table-title {
    padding: 22px;

    border-bottom:
        1px solid #e5e7eb;
}

.table-title h2 {
    margin: 0;

    font-size: 20px;
}

.table-title p {
    margin: 6px 0 0;

    color: #64748b;

    font-size: 13px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;

    border-collapse: collapse;

    min-width: 750px;
}

th {
    background: #f8fafc;

    padding: 14px;

    text-align: left;

    color: #475569;

    font-size: 13px;
}

td {
    padding: 14px;

    border-top:
        1px solid #eef2f7;

    font-size: 14px;
}

tr:hover {
    background: #f8fbff;
}


/* BADGE */

.badge {
    display: inline-block;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: bold;
}

.badge-masuk {
    background: #dcfce7;
    color: #15803d;
}

.badge-keluar {
    background: #fee2e2;
    color: #b91c1c;
}


/* EMPTY */

.empty {
    text-align: center;

    padding: 40px;

    color: #64748b;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width: 800px) {

    .sidebar {
        width: 200px;
    }

    .content {
        margin-left: 200px;
        width: calc(100% - 200px);
        padding: 15px;
    }

    .quick,
    .stats {
        grid-template-columns: 1fr;
    }

}

@media(max-width: 600px) {

    .sidebar {
        width: 70px;
        padding: 15px 8px;
    }

    .logo {
        padding: 5px;
    }

    .logo img {
        max-width: 50px;
    }

    .company {
        display: none;
    }

    .menu a {
        justify-content: center;
        font-size: 0;
        padding: 13px;
    }

    .menu a:first-letter {
        font-size: 20px;
    }

    .content {
        margin-left: 70px;
        width: calc(100% - 70px);
        padding: 12px;
    }

    .top h1 {
        font-size: 18px;
    }

    .hero h2 {
        font-size: 22px;
    }

}

</style>

</head>


<body>


<div class="wrapper">


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="logo">

        <img
            src="logo.jpg"
            alt="PRIMA"
            onerror="this.style.display='none'"
        >

        <strong style="color:#174ea6;font-size:28px;">
            PRIMA
        </strong>

    </div>


    <div class="company">
        PT Prima Sarana Gemilang
    </div>


    <nav class="menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="barang.php">
            📦 Data Barang
        </a>

        <a href="transaksi.php" class="active">
            🔄 Transaksi
        </a>

        <a href="laporan.php">
            📊 Laporan
        </a>

        <a href="logout.php">
            🚪 Keluar
        </a>

    </nav>

</aside>



<!-- =========================
     CONTENT
========================= -->

<main class="content">


    <!-- TOP -->

    <div class="top">

        <h1>
            🔄 Transaksi Stok
        </h1>

        <div class="admin">
            👤 Admin
        </div>

    </div>



    <!-- HERO -->

    <div class="hero">

        <h2>
            Kelola Transaksi 📦
        </h2>

        <p>
            Catat stok masuk dan stok keluar dengan cepat,
            mudah, dan terorganisir.
        </p>

    </div>



    <!-- QUICK MENU -->

    <div class="quick">


        <!-- STOK MASUK -->

        <div class="quick-card">

            <div class="quick-icon">
                📥
            </div>

            <h3>
                Stok Masuk
            </h3>

            <p>
                Tambahkan barang yang masuk
                ke dalam persediaan.
            </p>

            <a
                href="stok_masuk.php"
                class="quick-btn btn-masuk"
            >
                + Tambah Stok Masuk
            </a>

        </div>



        <!-- STOK KELUAR -->

        <div class="quick-card">

            <div class="quick-icon">
                📤
            </div>

            <h3>
                Stok Keluar
            </h3>

            <p>
                Catat barang yang keluar
                dari persediaan.
            </p>

            <a
                href="stok_keluar.php"
                class="quick-btn btn-keluar"
            >
                + Tambah Stok Keluar
            </a>

        </div>


    </div>



    <!-- STATISTIK -->

    <div class="stats">


        <div class="stat">

            <div class="stat-title">
                🔄 Total Transaksi
            </div>

            <div class="stat-number blue">
                <?= number_format($totalTransaksi); ?>
            </div>

        </div>


        <div class="stat">

            <div class="stat-title">
                📥 Total Stok Masuk
            </div>

            <div class="stat-number green">
                <?= number_format($totalMasuk); ?>
            </div>

        </div>


        <div class="stat">

            <div class="stat-title">
                📤 Total Stok Keluar
            </div>

            <div class="stat-number red">
                <?= number_format($totalKeluar); ?>
            </div>

        </div>


    </div>



    <!-- TABLE -->

    <div class="table-card">


        <div class="table-title">

            <h2>
                📋 Transaksi Terbaru
            </h2>

            <p>
                Menampilkan 10 transaksi terakhir
            </p>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Tanggal</th>

                        <th>Kode</th>

                        <th>Nama Barang</th>

                        <th>Jenis</th>

                        <th>Jumlah</th>

                        <th>Keterangan</th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($result && mysqli_num_rows($result) > 0): ?>


                    <?php
                    $no = 1;

                    while ($row = mysqli_fetch_assoc($result)):
                    ?>


                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>
                            <?= date(
                                'd-m-Y',
                                strtotime($row['tanggal'])
                            ); ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $row['kode'] ?? '-'
                                ); ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row['nama'] ?? '-'
                            ); ?>

                        </td>


                        <td>

                            <?php
                            if ($row['jenis'] == 'masuk'):
                            ?>

                                <span class="badge badge-masuk">
                                    📥 MASUK
                                </span>

                            <?php else: ?>

                                <span class="badge badge-keluar">
                                    📤 KELUAR
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <strong>
                                <?= number_format(
                                    $row['jumlah']
                                ); ?>
                            </strong>

                            <?= htmlspecialchars(
                                $row['satuan'] ?? ''
                            ); ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row['keterangan'] ?? '-'
                            ); ?>

                        </td>

                    </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="7"
                            class="empty"
                        >
                            📭 Belum ada transaksi.
                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


</main>

</div>

</body>

</html>