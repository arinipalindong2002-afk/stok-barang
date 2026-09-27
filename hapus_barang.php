<?php
require_once "koneksi.php";

/* Ambil ID barang */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: barang.php");
    exit;
}

/*
   Sesuaikan nama koneksi.
   Jika koneksi.php menggunakan $conn,
   kita gunakan $conn sebagai koneksi utama.
*/
if (isset($conn)) {
    $db = $conn;
} elseif (isset($koneksi)) {
    $db = $koneksi;
} elseif (isset($mysqli)) {
    $db = $mysqli;
} else {
    die("Koneksi database tidak ditemukan. Periksa file koneksi.php");
}

/* Ambil data barang */
$sql = "SELECT id, kode, nama, satuan, stok
        FROM barang
        WHERE id = $id
        LIMIT 1";

$result = mysqli_query($db, $sql);

if (!$result) {
    die("Gagal mengambil data barang: " . mysqli_error($db));
}

$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    header("Location: barang.php");
    exit;
}

/* Proses hapus */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql_hapus = "DELETE FROM barang WHERE id = $id";

    if (mysqli_query($db, $sql_hapus)) {

        header("Location: barang.php?success=hapus");
        exit;

    } else {

        $error = "Barang gagal dihapus: " . mysqli_error($db);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Hapus Barang - PRIMA</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    color: #1e293b;
}

.container {
    max-width: 600px;
    margin: 60px auto;
    padding: 20px;
}

.card {
    background: white;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 10px 35px rgba(0,0,0,.10);
    text-align: center;
}

.icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;

    background: #fee2e2;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 38px;
}

h2 {
    margin: 0 0 10px;
    color: #172554;
}

.warning {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 25px;
}

.data-barang {
    background: #f8fafc;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 25px;
    text-align: left;
}

.row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 10px 0;
    border-bottom: 1px solid #e2e8f0;
}

.row:last-child {
    border-bottom: none;
}

.label {
    color: #64748b;
}

.value {
    font-weight: bold;
    color: #172554;
    text-align: right;
}

.buttons {
    display: flex;
    gap: 12px;
}

.btn {
    flex: 1;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
}

.btn-batal {
    background: #e2e8f0;
    color: #334155;
}

.btn-batal:hover {
    background: #cbd5e1;
}

.btn-hapus {
    background: #dc2626;
    color: white;
}

.btn-hapus:hover {
    background: #b91c1c;
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
}

@media (max-width: 600px) {

    .container {
        margin: 25px auto;
        padding: 15px;
    }

    .card {
        padding: 25px 18px;
    }

    .buttons {
        flex-direction: column;
    }

    .row {
        font-size: 14px;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="card">

        <div class="icon">
            🗑️
        </div>

        <h2>Hapus Barang?</h2>

        <p class="warning">
            Apakah Anda yakin ingin menghapus barang ini?
            Data yang sudah dihapus tidak dapat dikembalikan.
        </p>

        <?php if (isset($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <div class="data-barang">

            <div class="row">

                <span class="label">
                    Kode Barang
                </span>

                <span class="value">
                    <?= htmlspecialchars($barang['kode']) ?>
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Nama Barang
                </span>

                <span class="value">
                    <?= htmlspecialchars($barang['nama']) ?>
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Satuan
                </span>

                <span class="value">
                    <?= htmlspecialchars($barang['satuan']) ?>
                </span>

            </div>


            <div class="row">

                <span class="label">
                    Stok
                </span>

                <span class="value">
                    <?= htmlspecialchars($barang['stok']) ?>
                </span>

            </div>

        </div>


        <form method="POST">

            <div class="buttons">

                <a href="barang.php"
                   class="btn btn-batal">
                    ← Batal
                </a>

                <button type="submit"
                        class="btn btn-hapus"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">

                    🗑️ Ya, Hapus

                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>