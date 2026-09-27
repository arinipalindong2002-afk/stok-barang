<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* CEK ID BARANG */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: barang.php");
    exit;
}

$id = intval($_GET['id']);

/* AMBIL DATA BARANG */
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, kode, nama, satuan, stok
     FROM barang
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: barang.php");
    exit;
}

$pesan = "";

/* PROSES UPDATE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kode   = trim($_POST['kode']);
    $nama   = trim($_POST['nama']);
    $satuan = trim($_POST['satuan']);
    $stok   = intval($_POST['stok']);

    if ($kode == "" || $nama == "" || $satuan == "") {

        $pesan = "Semua data wajib diisi.";

    } else {

        /* CEK KODE DUPLIKAT */
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM barang
             WHERE kode = ? AND id != ?"
        );

        mysqli_stmt_bind_param($cek, "si", $kode, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kode barang sudah digunakan oleh barang lain.";

        } else {

            /* UPDATE DATA */
            $update = mysqli_prepare(
                $conn,
                "UPDATE barang
                 SET kode = ?, nama = ?, satuan = ?, stok = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "sssii",
                $kode,
                $nama,
                $satuan,
                $stok,
                $id
            );

            if (mysqli_stmt_execute($update)) {

                header("Location: barang.php?success=edit");
                exit;

            } else {

                $pesan = "Data gagal diperbarui: " . mysqli_error($conn);
            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Edit Barang - PT Prima Sarana Gemilang</title>

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
    background: #f4f7fb;
    color: #1f2937;
}

/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 235px;
    height: 100vh;

    background: linear-gradient(
        180deg,
        #063bb8,
        #001b72
    );

    color: white;

    padding: 25px 15px;

    box-shadow: 4px 0 15px rgba(0,0,0,0.15);
}

/* LOGO */

.logo-box {
    text-align: center;
    margin-bottom: 12px;
}

.logo-box img {
    width: 155px;
    max-width: 100%;
    background: white;
    padding: 8px;
    border-radius: 12px;
}

/* COMPANY */

.company {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 25px;
}

/* MENU */

.menu {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.menu a {
    color: white;
    text-decoration: none;

    padding: 13px 15px;

    border-radius: 10px;

    font-size: 14px;

    transition: 0.2s;
}

.menu a:hover {
    background: rgba(255,255,255,0.15);
}

.menu a.active {
    background: rgba(255,255,255,0.18);
    box-shadow: inset 4px 0 white;
}

/* =========================
   MAIN
========================= */

.main {
    margin-left: 235px;
    min-height: 100vh;
    padding: 35px;
}

/* HEADER */

.page-header {
    margin-bottom: 25px;
}

.page-header h1 {
    margin: 0;
    color: #123b75;
    font-size: 30px;
}

.page-header p {
    margin-top: 7px;
    color: #6b7280;
}

/* =========================
   CARD
========================= */

.card {
    max-width: 700px;

    background: white;

    border-radius: 18px;

    padding: 30px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.08);
}

/* CARD TITLE */

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;

    margin-bottom: 25px;
}

.card-icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eaf2ff;

    border-radius: 12px;

    font-size: 24px;
}

.card-title h2 {
    margin: 0;
    font-size: 21px;
    color: #174ea6;
}

.card-title span {
    display: block;
    font-size: 13px;
    color: #777;
    margin-top: 4px;
}

/* =========================
   ALERT
========================= */

.alert {
    background: #fff1f2;
    color: #b91c1c;

    border-left: 5px solid #dc2626;

    padding: 13px 15px;

    border-radius: 9px;

    margin-bottom: 20px;
}

/* =========================
   FORM
========================= */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    font-weight: bold;

    margin-bottom: 8px;

    color: #374151;
}

.form-group input,
.form-group select {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #d1d5db;

    border-radius: 10px;

    font-size: 15px;

    background: white;

    outline: none;

    transition: 0.2s;
}

.form-group input:focus,
.form-group select:focus {

    border-color: #1769e0;

    box-shadow:
        0 0 0 3px rgba(23,105,224,0.12);
}

/* =========================
   BUTTON
========================= */

.buttons {

    display: flex;

    gap: 10px;

    margin-top: 28px;
}

.btn {

    border: none;

    padding: 13px 20px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: bold;

    text-decoration: none;

    cursor: pointer;

    display: inline-block;
}

.btn-save {

    background: #075bd3;

    color: white;
}

.btn-save:hover {

    background: #0649a8;
}

.btn-back {

    background: #e5e7eb;

    color: #374151;
}

.btn-back:hover {

    background: #d1d5db;
}

/* =========================
   FOOTER
========================= */

.footer {

    margin-top: 30px;

    color: #777;

    font-size: 13px;
}

/* =========================
   MOBILE / TABLET
========================= */

@media (max-width: 800px) {

    .sidebar {

        width: 190px;
    }

    .main {

        margin-left: 190px;

        padding: 25px;
    }

    .logo-box img {

        width: 135px;
    }
}

@media (max-width: 600px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

        padding: 15px;
    }

    .logo-box img {

        width: 130px;
    }

    .menu {

        flex-direction: row;

        overflow-x: auto;
    }

    .menu a {

        white-space: nowrap;
    }

    .main {

        margin-left: 0;

        padding: 20px 15px;
    }

    .card {

        padding: 20px;
    }

    .buttons {

        flex-direction: column;
    }

    .btn {

        width: 100%;

        text-align: center;
    }
}

</style>

</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="logo-box">

        <img src="logo.png"
             alt="Logo PT Prima Sarana Gemilang">

    </div>

    <div class="company">
        PT Prima Sarana Gemilang
    </div>

    <div class="menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="barang.php"
           class="active">
            📦 Data Barang
        </a>

        <a href="stok_masuk.php">
            📥 Transaksi
        </a>

        <a href="laporan.php">
            📊 Laporan
        </a>

        <a href="logout.php">
            🚪 Keluar
        </a>

    </div>

</div>


<!-- =========================
     MAIN
========================= -->

<div class="main">

    <div class="page-header">

        <h1>Edit Data Barang</h1>

        <p>
            Perbarui informasi barang yang tersimpan
            di dalam sistem.
        </p>

    </div>


    <div class="card">

        <div class="card-title">

            <div class="card-icon">
                ✏️
            </div>

            <div>

                <h2>
                    Edit Barang
                </h2>

                <span>
                    Perbarui kode, nama, satuan dan stok
                </span>

            </div>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="alert">

                ⚠️ <?= htmlspecialchars($pesan) ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- KODE -->

            <div class="form-group">

                <label>
                    Kode Barang
                </label>

                <input
                    type="text"
                    name="kode"
                    required
                    value="<?= htmlspecialchars(
                        $_POST['kode'] ?? $data['kode']
                    ) ?>"
                    placeholder="Contoh: BRG001"
                >

            </div>


            <!-- NAMA -->

            <div class="form-group">

                <label>
                    Nama Barang
                </label>

                <input
                    type="text"
                    name="nama"
                    required
                    value="<?= htmlspecialchars(
                        $_POST['nama'] ?? $data['nama']
                    ) ?>"
                    placeholder="Contoh: Pulpen"
                >

            </div>


            <!-- SATUAN -->

            <div class="form-group">

                <label>
                    Satuan
                </label>

                <select name="satuan" required>

                    <?php
                    $satuan = $_POST['satuan']
                        ?? $data['satuan'];
                    ?>

                    <option value="">
                        -- Pilih Satuan --
                    </option>

                    <option value="pcs"
                        <?= $satuan == 'pcs'
                            ? 'selected' : '' ?>>
                        Pcs
                    </option>

                    <option value="Box"
                        <?= $satuan == 'Box'
                            ? 'selected' : '' ?>>
                        Box
                    </option>

                    <option value="Rim"
                        <?= $satuan == 'Rim'
                            ? 'selected' : '' ?>>
                        Rim
                    </option>

                    <option value="Unit"
                        <?= $satuan == 'Unit'
                            ? 'selected' : '' ?>>
                        Unit
                    </option>

                    <option value="Set"
                        <?= $satuan == 'Set'
                            ? 'selected' : '' ?>>
                        Set
                    </option>

                </select>

            </div>


            <!-- STOK -->

            <div class="form-group">

                <label>
                    Stok
                </label>

                <input
                    type="number"
                    name="stok"
                    min="0"
                    required
                    value="<?= htmlspecialchars(
                        $_POST['stok'] ?? $data['stok']
                    ) ?>"
                >

            </div>


            <!-- BUTTON -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-save">

                    💾 Simpan Perubahan

                </button>


                <a
                    href="barang.php"
                    class="btn btn-back">

                    ↩ Kembali

                </a>

            </div>

        </form>

    </div>


    <div class="footer">

        © <?= date('Y') ?>
        Sistem Laporan Stok Barang -
        PT Prima Sarana Gemilang

    </div>

</div>

</body>

</html>