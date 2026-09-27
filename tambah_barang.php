<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// PROSES SIMPAN
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kode   = trim($_POST['kode']);
    $nama   = trim($_POST['nama']);
    $satuan = trim($_POST['satuan']);
    $stok   = intval($_POST['stok']);

    if ($kode == "" || $nama == "" || $satuan == "") {
        $pesan = "Semua data wajib diisi.";
    } else {

        // Cek kode barang
        $cek = mysqli_prepare($conn, "SELECT id FROM barang WHERE kode = ?");
        mysqli_stmt_bind_param($cek, "s", $kode);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Kode barang sudah digunakan.";

        } else {

            // Simpan barang
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO barang (kode, nama, satuan, stok)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssi",
                $kode,
                $nama,
                $satuan,
                $stok
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: barang.php?success=1");
                exit;
            } else {
                $pesan = "Data gagal disimpan: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tambah Barang - Stok Barang</title>

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

/* HEADER */
.header {
    background: linear-gradient(135deg, #003b8f, #0066cc);
    color: white;
    padding: 18px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.header img {
    width: 150px;
    height: auto;
    object-fit: contain;
    background: white;
    border-radius: 10px;
    padding: 7px;
}

.header-title {
    font-size: 22px;
    font-weight: bold;
}

.header-subtitle {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

/* MENU */
.navbar {
    background: white;
    padding: 12px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.navbar a {
    text-decoration: none;
    color: #174ea6;
    padding: 9px 13px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}

.navbar a:hover {
    background: #eaf2ff;
}

/* CONTENT */
.container {
    max-width: 700px;
    margin: 35px auto;
    padding: 0 18px;
}

.card {
    background: white;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.card-header {
    margin-bottom: 25px;
}

.card-header h1 {
    margin: 0;
    font-size: 27px;
    color: #123b75;
}

.card-header p {
    margin-top: 8px;
    color: #6b7280;
}

/* ALERT */
.alert {
    background: #fff1f2;
    color: #b91c1c;
    padding: 13px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 5px solid #dc2626;
}

/* FORM */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 13px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 15px;
    outline: none;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #1769e0;
    box-shadow: 0 0 0 3px rgba(23,105,224,0.12);
}

/* BUTTON */
.buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.btn {
    border: none;
    padding: 13px 20px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
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

/* FOOTER */
.footer {
    text-align: center;
    color: #6b7280;
    font-size: 13px;
    margin: 30px 0;
}

/* HP */
@media (max-width: 600px) {

    .header {
        padding: 15px;
    }

    .header img {
        width: 105px;
    }

    .header-title {
        font-size: 17px;
    }

    .card {
        padding: 22px 18px;
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

<!-- HEADER -->
<div class="header">

    <img src="logo.png" alt="Logo PT Prima Sarana Gemilang">

    <div>
        <div class="header-title">
            PT Prima Sarana Gemilang
        </div>

        <div class="header-subtitle">
            Sistem Informasi Stok Barang
        </div>
    </div>

</div>

<!-- MENU -->
<div class="navbar">

    <a href="dashboard.php">🏠 Dashboard</a>

    <a href="barang.php">📦 Data Barang</a>

    <a href="stok_masuk.php">📥 Stok Masuk</a>

    <a href="stok_keluar.php">📤 Stok Keluar</a>

    <a href="laporan.php">📊 Laporan</a>

    <a href="logout.php">🚪 Keluar</a>

</div>

<!-- CONTENT -->
<div class="container">

    <div class="card">

        <div class="card-header">

            <h1>➕ Tambah Barang</h1>

            <p>
                Masukkan data barang baru ke dalam sistem.
            </p>

        </div>

        <?php if ($pesan != ""): ?>

            <div class="alert">
                ⚠️ <?= htmlspecialchars($pesan) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>Kode Barang</label>

                <input
                    type="text"
                    name="kode"
                    placeholder="Contoh: BRG004"
                    required
                    value="<?= htmlspecialchars($_POST['kode'] ?? '') ?>"
                >

            </div>

            <div class="form-group">

                <label>Nama Barang</label>

                <input
                    type="text"
                    name="nama"
                    placeholder="Contoh: Pulpen"
                    required
                    value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                >

            </div>

            <div class="form-group">

                <label>Satuan</label>

                <select name="satuan" required>

                    <option value="">-- Pilih Satuan --</option>

                    <option value="pcs"
                        <?= (($_POST['satuan'] ?? '') == 'pcs') ? 'selected' : '' ?>>
                        Pcs
                    </option>

                    <option value="Box"
                        <?= (($_POST['satuan'] ?? '') == 'Box') ? 'selected' : '' ?>>
                        Box
                    </option>

                    <option value="Rim"
                        <?= (($_POST['satuan'] ?? '') == 'Rim') ? 'selected' : '' ?>>
                        Rim
                    </option>

                    <option value="Unit"
                        <?= (($_POST['satuan'] ?? '') == 'Unit') ? 'selected' : '' ?>>
                        Unit
                    </option>

                    <option value="Set"
                        <?= (($_POST['satuan'] ?? '') == 'Set') ? 'selected' : '' ?>>
                        Set
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>Stok Awal</label>

                <input
                    type="number"
                    name="stok"
                    min="0"
                    value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>"
                    required
                >

            </div>

            <div class="buttons">

                <button type="submit" class="btn btn-save">
                    💾 Simpan Barang
                </button>

                <a href="barang.php" class="btn btn-back">
                    ↩ Kembali
                </a>

            </div>

        </form>

    </div>

</div>

<div class="footer">
    © <?= date('Y') ?> PT Prima Sarana Gemilang
</div>

</body>
</html>