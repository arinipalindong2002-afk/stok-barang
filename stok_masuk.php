<?php
session_start();
require_once "koneksi.php";

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";
$error = "";

// ===============================
// PROSES TAMBAH STOK MASUK
// ===============================
if (isset($_POST['simpan'])) {

    $barang_id  = (int) $_POST['barang_id'];
    $jumlah     = (int) $_POST['jumlah'];
    $tanggal    = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);

    if ($barang_id <= 0) {
        $error = "Silakan pilih barang terlebih dahulu.";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah stok harus lebih dari 0.";
    } elseif (empty($tanggal)) {
        $error = "Tanggal harus diisi.";
    } else {

        // Ambil data barang
        $query_barang = mysqli_query(
            $conn,
            "SELECT * FROM barang WHERE id = $barang_id"
        );

        if (!$query_barang || mysqli_num_rows($query_barang) == 0) {

            $error = "Barang tidak ditemukan.";

        } else {

            $barang = mysqli_fetch_assoc($query_barang);

            // Tambah stok barang
            $stok_lama = (int) $barang['stok'];
            $stok_baru = $stok_lama + $jumlah;

            mysqli_begin_transaction($conn);

            try {

                // Update stok
                $update = mysqli_query(
                    $conn,
                    "UPDATE barang 
                     SET stok = $stok_baru 
                     WHERE id = $barang_id"
                );

                if (!$update) {
                    throw new Exception("Gagal memperbarui stok.");
                }

                // Simpan transaksi
                $tanggal_sql = mysqli_real_escape_string($conn, $tanggal);
                $ket_sql     = mysqli_real_escape_string($conn, $keterangan);

                $insert = mysqli_query(
                    $conn,
                    "INSERT INTO transaksi
                    (barang_id, jenis, jumlah, tanggal, keterangan)
                    VALUES
                    ($barang_id, 'masuk', $jumlah, '$tanggal_sql', '$ket_sql')"
                );

                if (!$insert) {
                    throw new Exception("Gagal menyimpan transaksi.");
                }

                mysqli_commit($conn);

                $pesan = "Stok masuk berhasil ditambahkan.";

            } catch (Exception $e) {

                mysqli_rollback($conn);
                $error = $e->getMessage();
            }
        }
    }
}

// ===============================
// DATA BARANG
// ===============================
$data_barang = mysqli_query(
    $conn,
    "SELECT * FROM barang ORDER BY nama ASC"
);

// ===============================
// RIWAYAT STOK MASUK
// ===============================
$riwayat = mysqli_query(
    $conn,
    "SELECT 
        transaksi.*,
        barang.kode,
        barang.nama,
        barang.satuan
     FROM transaksi
     INNER JOIN barang ON transaksi.barang_id = barang.id
     WHERE transaksi.jenis = 'masuk'
     ORDER BY transaksi.id DESC
     LIMIT 20"
);

// ===============================
// TOTAL STOK MASUK
// ===============================
$total_masuk = 0;

$q_total = mysqli_query(
    $conn,
    "SELECT SUM(jumlah) AS total
     FROM transaksi
     WHERE jenis = 'masuk'"
);

if ($q_total) {
    $hasil_total = mysqli_fetch_assoc($q_total);
    $total_masuk = (int) ($hasil_total['total'] ?? 0);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Stok Masuk - Prima Sarana Gemilang</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #eef2f7;
    color: #222;
}

/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 245px;
    height: 100vh;
    background: linear-gradient(180deg, #0717c9, #00128f);
    color: white;
    padding: 25px 15px;
}

.logo {
    background: white;
    color: #0920b8;
    border-radius: 12px;
    padding: 18px 10px;
    text-align: center;
    margin-bottom: 25px;
}

.logo h1 {
    margin: 0;
    font-size: 27px;
    font-weight: bold;
}

.logo p {
    margin: 4px 0 0;
    color: #333;
    font-size: 12px;
}

.company {
    text-align: center;
    font-size: 14px;
    margin-bottom: 25px;
    opacity: .95;
}

.menu {
    display: block;
    text-decoration: none;
    color: white;
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 8px;
    font-size: 15px;
}

.menu:hover {
    background: rgba(255,255,255,.15);
}

.menu.active {
    background: rgba(255,255,255,.18);
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
}

.menu.logout {
    margin-top: 25px;
}

/* =========================
   MAIN
========================= */

.main {
    margin-left: 245px;
    padding: 25px;
}

.header {
    background: linear-gradient(135deg, #061bd0, #0837ef);
    color: white;
    padding: 28px 30px;
    border-radius: 18px;
    margin-bottom: 22px;
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}

.header h2 {
    margin: 0 0 8px;
    font-size: 28px;
}

.header p {
    margin: 0;
    opacity: .9;
}

/* =========================
   STAT CARD
========================= */

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 22px;
}

.stat {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,.06);
}

.stat-title {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 28px;
    font-weight: bold;
    color: #0925c7;
}

/* =========================
   FORM
========================= */

.card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 22px;
    box-shadow: 0 5px 15px rgba(0,0,0,.06);
}

.card-title {
    font-size: 21px;
    font-weight: bold;
    margin-bottom: 5px;
}

.card-subtitle {
    color: #777;
    font-size: 14px;
    margin-bottom: 22px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.form-group {
    margin-bottom: 5px;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 14px;
}

input,
select,
textarea {
    width: 100%;
    padding: 13px 14px;
    border: 1px solid #d5d9e2;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    background: #fafbfc;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #1536dc;
    box-shadow: 0 0 0 3px rgba(21,54,220,.1);
}

textarea {
    resize: vertical;
    min-height: 90px;
}

.btn {
    border: none;
    padding: 13px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.btn-success {
    background: #13a64a;
    color: white;
}

.btn-success:hover {
    background: #0d8b3c;
}

/* =========================
   ALERT
========================= */

.alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 14px;
}

.alert-success {
    background: #e7f8ed;
    color: #117333;
    border: 1px solid #bce8ca;
}

.alert-error {
    background: #fdeaea;
    color: #b42318;
    border: 1px solid #f3b9b4;
}

/* =========================
   TABLE
========================= */

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
}

th {
    background: #f2f5fb;
    color: #444;
    padding: 13px;
    text-align: left;
    font-size: 13px;
}

td {
    padding: 13px;
    border-bottom: 1px solid #edf0f5;
    font-size: 14px;
}

.badge {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 20px;
    background: #e5f8ec;
    color: #11833e;
    font-size: 12px;
    font-weight: bold;
}

/* =========================
   MOBILE
========================= */

@media(max-width: 800px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding: 15px;
    }

    .logo {
        margin-bottom: 10px;
    }

    .company {
        margin-bottom: 10px;
    }

    .menu {
        display: inline-block;
        padding: 10px 12px;
        margin: 3px;
        font-size: 13px;
    }

    .menu.logout {
        margin-top: 3px;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .header {
        padding: 22px;
    }

    .header h2 {
        font-size: 22px;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }
}

</style>
</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="logo">
        <h1>PRIMA</h1>
        <p>Sistem Stok Barang</p>
    </div>

    <div class="company">
        PT Prima Sarana Gemilang
    </div>

    <a href="dashboard.php" class="menu">
        🏠 Dashboard
    </a>

    <a href="barang.php" class="menu">
        📦 Data Barang
    </a>

    <a href="transaksi.php" class="menu active">
        🔄 Transaksi
    </a>

    <a href="laporan.php" class="menu">
        📊 Laporan
    </a>

    <a href="logout.php" class="menu logout">
        🚪 Keluar
    </a>

</div>


<!-- =========================
     MAIN
========================= -->

<div class="main">

    <div class="header">
        <h2>📥 Stok Masuk</h2>
        <p>Tambahkan barang yang masuk ke dalam persediaan.</p>
    </div>


    <?php if ($pesan != ""): ?>

        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($pesan); ?>
        </div>

    <?php endif; ?>


    <?php if ($error != ""): ?>

        <div class="alert alert-error">
            ⚠️ <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <!-- STATISTIK -->

    <div class="stats">

        <div class="stat">
            <div class="stat-title">
                📥 Total Stok Masuk
            </div>

            <div class="stat-value">
                <?= number_format($total_masuk); ?>
            </div>
        </div>

        <div class="stat">
            <div class="stat-title">
                📦 Jenis Barang
            </div>

            <div class="stat-value">
                <?php
                $q_barang = mysqli_query(
                    $conn,
                    "SELECT COUNT(*) AS total FROM barang"
                );

                $j_barang = mysqli_fetch_assoc($q_barang);
                echo number_format($j_barang['total']);
                ?>
            </div>
        </div>

        <div class="stat">
            <div class="stat-title">
                🕒 Transaksi Terbaru
            </div>

            <div class="stat-value">
                20
            </div>
        </div>

    </div>


    <!-- FORM STOK MASUK -->

    <div class="card">

        <div class="card-title">
            📥 Tambah Stok Masuk
        </div>

        <div class="card-subtitle">
            Isi data barang yang masuk ke dalam persediaan.
        </div>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        📦 Pilih Barang
                    </label>

                    <select name="barang_id" required>

                        <option value="">
                            -- Pilih Barang --
                        </option>

                        <?php while ($barang = mysqli_fetch_assoc($data_barang)): ?>

                            <option value="<?= $barang['id']; ?>">

                                <?= htmlspecialchars($barang['kode']); ?>
                                -
                                <?= htmlspecialchars($barang['nama']); ?>

                                (Stok:
                                <?= $barang['stok']; ?>
                                <?= htmlspecialchars($barang['satuan']); ?>
                                )

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        🔢 Jumlah Stok
                    </label>

                    <input
                        type="number"
                        name="jumlah"
                        min="1"
                        placeholder="Masukkan jumlah"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        📅 Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        value="<?= date('Y-m-d'); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        📝 Keterangan
                    </label>

                    <input
                        type="text"
                        name="keterangan"
                        placeholder="Contoh: Pembelian dari supplier"
                    >

                </div>


                <div class="form-group full">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-success"
                    >
                        ➕ Tambah Stok Masuk
                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- RIWAYAT -->

    <div class="card">

        <div class="card-title">
            📋 Riwayat Stok Masuk
        </div>

        <div class="card-subtitle">
            Daftar transaksi stok masuk terbaru.
        </div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                if ($riwayat && mysqli_num_rows($riwayat) > 0):

                    while ($row = mysqli_fetch_assoc($riwayat)):

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
                                <?= htmlspecialchars($row['kode']); ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama']); ?>
                        </td>

                        <td>
                            <strong style="color:#139447;">
                                +<?= number_format($row['jumlah']); ?>
                            </strong>

                            <?= htmlspecialchars($row['satuan']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['keterangan'] ?: '-'
                            ); ?>
                        </td>

                        <td>
                            <span class="badge">
                                STOK MASUK
                            </span>
                        </td>

                    </tr>

                <?php

                    endwhile;

                else:

                ?>

                    <tr>

                        <td colspan="7" style="text-align:center;padding:30px;">

                            📭 Belum ada transaksi stok masuk.

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