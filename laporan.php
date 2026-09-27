<?php
session_start();
require_once "koneksi.php";

/* =========================
   FILTER TANGGAL
========================= */
$dari = isset($_GET['dari']) && $_GET['dari'] != ''
    ? $_GET['dari']
    : date('Y-m-01');

$sampai = isset($_GET['sampai']) && $_GET['sampai'] != ''
    ? $_GET['sampai']
    : date('Y-m-d');


/* =========================
   TOTAL TRANSAKSI MASUK
========================= */
$sqlMasuk = "
    SELECT COALESCE(SUM(jumlah), 0) AS total
    FROM transaksi
    WHERE jenis = 'masuk'
    AND tanggal BETWEEN ? AND ?
";

$stmtMasuk = mysqli_prepare($conn, $sqlMasuk);
mysqli_stmt_bind_param($stmtMasuk, "ss", $dari, $sampai);
mysqli_stmt_execute($stmtMasuk);

$resultMasuk = mysqli_stmt_get_result($stmtMasuk);
$dataMasuk = mysqli_fetch_assoc($resultMasuk);

$totalMasuk = $dataMasuk['total'];


/* =========================
   TOTAL TRANSAKSI KELUAR
========================= */
$sqlKeluar = "
    SELECT COALESCE(SUM(jumlah), 0) AS total
    FROM transaksi
    WHERE jenis = 'keluar'
    AND tanggal BETWEEN ? AND ?
";

$stmtKeluar = mysqli_prepare($conn, $sqlKeluar);
mysqli_stmt_bind_param($stmtKeluar, "ss", $dari, $sampai);
mysqli_stmt_execute($stmtKeluar);

$resultKeluar = mysqli_stmt_get_result($stmtKeluar);
$dataKeluar = mysqli_fetch_assoc($resultKeluar);

$totalKeluar = $dataKeluar['total'];


/* =========================
   JUMLAH TRANSAKSI
========================= */
$sqlJumlah = "
    SELECT COUNT(*) AS total
    FROM transaksi
    WHERE tanggal BETWEEN ? AND ?
";

$stmtJumlah = mysqli_prepare($conn, $sqlJumlah);
mysqli_stmt_bind_param($stmtJumlah, "ss", $dari, $sampai);
mysqli_stmt_execute($stmtJumlah);

$resultJumlah = mysqli_stmt_get_result($stmtJumlah);
$dataJumlah = mysqli_fetch_assoc($resultJumlah);

$totalTransaksi = $dataJumlah['total'];


/* =========================
   DATA LAPORAN
   BARANG.ID = TRANSAKSI.BARANG_ID
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
    WHERE t.tanggal BETWEEN ? AND ?
    ORDER BY t.tanggal DESC, t.id DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $dari, $sampai);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Transaksi Stok</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
        }

        /* HEADER */

        .header {
            background: linear-gradient(135deg, #0645d6, #1677ff);
            color: white;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,.12);
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            opacity: .9;
        }

        /* NAVIGASI */

        .nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .nav a {
            text-decoration: none;
            background: white;
            color: #174ea6;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0,0,0,.06);
        }

        .nav a:hover {
            background: #174ea6;
            color: white;
        }

        /* FILTER */

        .filter {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(0,0,0,.07);
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: bold;
            color: #64748b;
        }

        input[type="date"] {
            padding: 11px 13px;
            border: 1px solid #d7dee8;
            border-radius: 9px;
            font-size: 14px;
        }

        .btn {
            border: none;
            padding: 11px 18px;
            border-radius: 9px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #1769e0;
            color: white;
        }

        .btn-primary:hover {
            background: #0d4fb8;
        }

        .btn-excel {
            background: #198754;
            color: white;
        }

        .btn-excel:hover {
            background: #146c43;
        }

        /* CARD */

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 5px 18px rgba(0,0,0,.07);
            position: relative;
            overflow: hidden;
        }

        .card::after {
            content: "";
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            right: -25px;
            top: -25px;
            background: rgba(23,105,224,.08);
        }

        .card-title {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .card-number {
            font-size: 30px;
            font-weight: bold;
        }

        .masuk {
            color: #198754;
        }

        .keluar {
            color: #dc3545;
        }

        .biru {
            color: #1769e0;
        }

        /* TABEL */

        .table-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 18px rgba(0,0,0,.07);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #edf0f4;
        }

        .table-header h2 {
            margin: 0;
            font-size: 20px;
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
            background: #f1f5f9;
            color: #475569;
            padding: 14px;
            text-align: left;
            font-size: 13px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #edf0f4;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fbff;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-masuk {
            background: #d1fae5;
            color: #047857;
        }

        .badge-keluar {
            background: #fee2e2;
            color: #b91c1c;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {

            .container {
                width: 94%;
                margin: 15px auto;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 23px;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group,
            input[type="date"],
            .btn {
                width: 100%;
            }
        }

    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h1>📊 Laporan Transaksi Stok</h1>
        <p>Rekap transaksi stok barang berdasarkan periode tanggal</p>
    </div>


    <!-- NAVIGASI -->
    <div class="nav">
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="barang.php">📦 Data Barang</a>
        <a href="stok_masuk.php">📥 Stok Masuk</a>
        <a href="stok_keluar.php">📤 Stok Keluar</a>
        <a href="laporan.php">📊 Laporan</a>
    </div>


    <!-- FILTER -->
    <div class="filter">

        <div class="filter-title">
            🔎 Filter Laporan
        </div>

        <form method="GET" class="filter-form">

            <div class="form-group">
                <label>Dari Tanggal</label>
                <input
                    type="date"
                    name="dari"
                    value="<?= htmlspecialchars($dari); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input
                    type="date"
                    name="sampai"
                    value="<?= htmlspecialchars($sampai); ?>"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                🔍 Tampilkan
            </button>

            <a
                href="download_excel.php?dari=<?= urlencode($dari); ?>&sampai=<?= urlencode($sampai); ?>"
                class="btn btn-success"
            >
                📥 Download Excel
            </a>

        </form>

    </div>


    <!-- SUMMARY -->
    <div class="cards">

        <div class="card">
            <div class="card-title">
                📥 Total Stok Masuk
            </div>

            <div class="card-number masuk">
                <?= number_format($totalMasuk); ?>
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                📤 Total Stok Keluar
            </div>

            <div class="card-number keluar">
                <?= number_format($totalKeluar); ?>
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                📋 Total Transaksi
            </div>

            <div class="card-number biru">
                <?= number_format($totalTransaksi); ?>
            </div>
        </div>

    </div>


    <!-- TABEL -->
    <div class="table-box">

        <div class="table-header">
            <h2>📋 Detail Transaksi</h2>
        </div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($row['kode'] ?? '-'); ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['satuan'] ?? '-'); ?>
                            </td>

                            <td>

                                <?php if ($row['jenis'] == 'masuk'): ?>

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
                                    <?= number_format($row['jumlah']); ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['keterangan'] ?? '-'); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8" class="empty">
                            📭 Tidak ada transaksi pada periode tersebut.
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