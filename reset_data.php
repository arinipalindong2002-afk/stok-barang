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

// ==========================================
// PROSES RESET
// ==========================================

if (isset($_POST['reset'])) {

    // Pastikan user mengetik RESET
    $konfirmasi = trim($_POST['konfirmasi']);

    if ($konfirmasi !== "RESET") {

        $error = "Ketik RESET dengan benar untuk melanjutkan.";

    } else {

        mysqli_begin_transaction($conn);

        try {

            // ----------------------------------
            // 1. HAPUS SEMUA TRANSAKSI
            // ----------------------------------

            $hapus = mysqli_query(
                $conn,
                "DELETE FROM transaksi"
            );

            if (!$hapus) {
                throw new Exception(
                    "Gagal menghapus data transaksi."
                );
            }


            // ----------------------------------
            // 2. RESET STOK SEMUA BARANG
            // ----------------------------------

            $reset_stok = mysqli_query(
                $conn,
                "UPDATE barang SET stok = 0"
            );

            if (!$reset_stok) {
                throw new Exception(
                    "Gagal mereset stok barang."
                );
            }


            // ----------------------------------
            // 3. RESET AUTO INCREMENT TRANSAKSI
            // ----------------------------------

            mysqli_query(
                $conn,
                "ALTER TABLE transaksi AUTO_INCREMENT = 1"
            );


            // ----------------------------------
            // SIMPAN PERUBAHAN
            // ----------------------------------

            mysqli_commit($conn);

            $pesan =
                "Semua data stok masuk dan stok keluar "
                . "berhasil direset. Stok semua barang "
                . "sekarang menjadi 0.";

        } catch (Exception $e) {

            mysqli_rollback($conn);

            $error = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Reset Data - Prima Sarana Gemilang
</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #eef2f7;
}


/* =====================================
   SIDEBAR
===================================== */

.sidebar {

    position: fixed;

    left: 0;
    top: 0;

    width: 245px;
    height: 100vh;

    background:
        linear-gradient(
            180deg,
            #0717c9,
            #00128f
        );

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

    background:
        rgba(255,255,255,.15);
}


.menu.active {

    background:
        rgba(255,255,255,.18);
}


/* =====================================
   MAIN
===================================== */

.main {

    margin-left: 245px;

    padding: 30px;
}


/* =====================================
   HEADER
===================================== */

.header {

    background:
        linear-gradient(
            135deg,
            #061bd0,
            #0837ef
        );

    color: white;

    padding: 28px 30px;

    border-radius: 18px;

    margin-bottom: 25px;
}


.header h2 {

    margin: 0 0 8px;

    font-size: 28px;
}


.header p {

    margin: 0;

    opacity: .9;
}


/* =====================================
   CARD
===================================== */

.card {

    background: white;

    max-width: 750px;

    margin: 0 auto;

    padding: 30px;

    border-radius: 20px;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,.08);
}


.warning {

    background: #fff4e5;

    border: 1px solid #ffd59a;

    color: #8a4b00;

    padding: 18px;

    border-radius: 12px;

    margin-bottom: 25px;

    line-height: 1.6;
}


.danger-box {

    background: #fff0f0;

    border: 1px solid #f3b5b5;

    border-radius: 14px;

    padding: 20px;

    margin-bottom: 25px;
}


.danger-box h3 {

    margin-top: 0;

    color: #b42318;
}


.danger-box ul {

    margin-bottom: 0;

    padding-left: 20px;

    line-height: 1.8;
}


label {

    display: block;

    font-weight: bold;

    margin-bottom: 8px;
}


input {

    width: 100%;

    padding: 14px;

    border: 1px solid #d5d9e2;

    border-radius: 10px;

    font-size: 15px;

    margin-bottom: 15px;
}


input:focus {

    outline: none;

    border-color: #d93025;

    box-shadow:
        0 0 0 3px
        rgba(217,48,37,.1);
}


.btn {

    width: 100%;

    padding: 15px;

    border: none;

    border-radius: 10px;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;
}


.btn-danger {

    background: #d93025;

    color: white;
}


.btn-danger:hover {

    background: #b71c1c;
}


.btn-back {

    display: block;

    text-align: center;

    text-decoration: none;

    margin-top: 12px;

    padding: 13px;

    border-radius: 10px;

    background: #eef2f7;

    color: #333;

    font-weight: bold;
}


.alert {

    padding: 15px;

    border-radius: 10px;

    margin-bottom: 20px;
}


.success {

    background: #e7f8ed;

    color: #117333;

    border: 1px solid #bce8ca;
}


.error {

    background: #fdeaea;

    color: #b42318;

    border: 1px solid #f3b9b4;
}


/* =====================================
   MOBILE
===================================== */

@media(max-width:800px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

        padding: 15px;
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


    .card {

        padding: 20px;
    }

}

</style>

</head>


<body>


<!-- =====================================
     SIDEBAR
===================================== -->

<div class="sidebar">

    <div class="logo">

        <h1>PRIMA</h1>

        <p>
            Sistem Stok Barang
        </p>

    </div>


    <div class="company">

        PT Prima Sarana Gemilang

    </div>


    <a
        href="dashboard.php"
        class="menu"
    >
        🏠 Dashboard
    </a>


    <a
        href="barang.php"
        class="menu"
    >
        📦 Data Barang
    </a>


    <a
        href="transaksi.php"
        class="menu"
    >
        🔄 Transaksi
    </a>


    <a
        href="laporan.php"
        class="menu"
    >
        📊 Laporan
    </a>

</div>



<!-- =====================================
     MAIN
===================================== -->

<div class="main">


    <div class="header">

        <h2>
            ⚠️ Reset Data Stok
        </h2>

        <p>
            Mengembalikan data transaksi dan stok
            ke kondisi awal.
        </p>

    </div>



    <div class="card">


        <?php if ($pesan != ""): ?>

            <div class="alert success">

                ✅
                <?= htmlspecialchars($pesan); ?>

            </div>

        <?php endif; ?>


        <?php if ($error != ""): ?>

            <div class="alert error">

                ❌
                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <div class="warning">

            ⚠️ <strong>Perhatian!</strong>

            <br><br>

            Reset ini akan menghapus seluruh riwayat
            transaksi stok masuk dan stok keluar.

            <br>

            Pastikan data tersebut memang sudah tidak
            diperlukan.

        </div>



        <div class="danger-box">

            <h3>
                🗑️ Data yang akan dihapus
            </h3>

            <ul>

                <li>
                    Semua transaksi stok masuk
                </li>

                <li>
                    Semua transaksi stok keluar
                </li>

                <li>
                    Stok semua barang dikembalikan menjadi 0
                </li>

                <li>
                    Nomor transaksi dimulai kembali dari 1
                </li>

            </ul>

        </div>



        <form
            method="POST"
            onsubmit="
                return confirm(
                    'PERINGATAN! Semua transaksi akan dihapus dan semua stok menjadi 0. Lanjutkan?'
                );
            "
        >

            <label>
                Ketik <strong>RESET</strong> untuk melanjutkan
            </label>


            <input
                type="text"
                name="konfirmasi"
                placeholder="Ketik RESET"
                autocomplete="off"
                required
            >


            <button
                type="submit"
                name="reset"
                class="btn btn-danger"
            >

                🗑️ RESET SEMUA DATA STOK

            </button>

        </form>


        <a
            href="dashboard.php"
            class="btn-back"
        >

            ← Kembali ke Dashboard

        </a>

    </div>

</div>


</body>

</html>