a:
<?php
session_start();
require_once "koneksi.php";

/* =========================
   CEK LOGIN
   ========================= */
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   PENCARIAN
   ========================= */
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

if ($cari != '') {

    $cari_safe = mysqli_real_escape_string($conn, $cari);

    $query = "
        SELECT *
        FROM barang
        WHERE kode LIKE '%$cari_safe%'
           OR nama LIKE '%$cari_safe%'
        ORDER BY id DESC
    ";

} else {

    $query = "
        SELECT *
        FROM barang
        ORDER BY id DESC
    ";
}

$result = mysqli_query($conn, $query);

/* =========================
   TOTAL BARANG
   ========================= */
$q_total = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM barang"
);

$d_total = mysqli_fetch_assoc($q_total);
$total_barang = (int)$d_total['total'];

/* =========================
   TOTAL STOK
   ========================= */
$q_stok = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(stok),0) AS total FROM barang"
);

$d_stok = mysqli_fetch_assoc($q_stok);
$total_stok = (int)$d_stok['total'];

?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Data Barang - Laporan Stok</title>

<style>

/* =========================
   RESET
   ========================= */

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

    background: linear-gradient(
        180deg,
        #0757c9,
        #063b91
    );

    padding: 25px 18px;

    color: white;

    overflow-y: auto;
}


.logo-area {

    text-align: center;

    margin-bottom: 30px;
}


.logo-area img {

    width: 170px;
    max-width: 90%;

    background: white;

    padding: 10px;

    border-radius: 12px;

    height: auto;
}


.company-name {

    margin-top: 12px;

    font-size: 14px;

    font-weight: bold;

    line-height: 1.5;
}


/* =========================
   MENU
   ========================= */

.menu {

    margin-top: 25px;
}


.menu a {

    display: flex;

    align-items: center;

    gap: 12px;

    color: white;

    text-decoration: none;

    padding: 13px 15px;

    margin-bottom: 8px;

    border-radius: 10px;

    font-size: 15px;

    transition: 0.2s;
}


.menu a:hover {

    background: rgba(255,255,255,0.18);

}


.menu a.active {

    background: rgba(255,255,255,0.22);

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


/* =========================
   TOPBAR
   ========================= */

.topbar {

    background: white;

    border-radius: 16px;

    padding: 18px 22px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.05);
}


.top-title h2 {

    color: #173b7a;

    font-size: 22px;
}


.top-title p {

    margin-top: 5px;

    color: #7a8497;

    font-size: 13px;
}


.user-box {

    background: #eef5ff;

    color: #0757c9;

    padding: 10px 15px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: bold;
}


/* =========================
   PAGE HEADER
   ========================= */

.page-header {

    background: linear-gradient(
        135deg,
        #0757c9,
        #1475e8
    );

    color: white;

    padding: 28px;

    border-radius: 18px;

    margin-bottom: 22px;

    box-shadow:
        0 10px 25px rgba(7,87,201,0.18);
}


.page-header h1 {

    font-size: 28px;

    margin-bottom: 8px;
}


.page-header p {

    opacity: 0.9;

    font-size: 14px;
}

/* =========================
   INFO CARDS
   ========================= */

.info-cards {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 22px;
}


.info-card {

    background: white;

    border-radius: 16px;

    padding: 20px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.05);

    display: flex;

    align-items: center;

    gap: 15px;
}


.info-icon {

    width: 52px;

    height: 52px;

    border-radius: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;

    background: #e8f1ff;
}


.info-card h3 {

    font-size: 13px;

    color: #7b8494;

    margin-bottom: 5px;
}


.info-number {

    font-size: 25px;

    font-weight: bold;

    color: #172033;
}


/* =========================
   CONTENT PANEL
   ========================= */

.panel {

    background: white;

    border-radius: 16px;

    padding: 22px;

    box-shadow:
        0 5px 20px rgba(0,0,0,0.05);
}


/* =========================
   PANEL HEADER
   ========================= */

.panel-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    margin-bottom: 20px;
}


.panel-title h2 {

    font-size: 19px;

    color: #173b7a;
}


.panel-title p {

    margin-top: 5px;

    color: #8a94a6;

    font-size: 12px;
}


/* =========================
   BUTTON
   ========================= */

.btn{

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    padding: 10px 16px;

    border-radius: 9px;

    border: none;

    text-decoration: none;

    cursor: pointer;

    font-size: 13px;

    font-weight: bold;

    transition: 0.2s;
}


..btn-tambah {
    display: inline-block;
    background: #0757d5;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: bold;
    margin-bottom: 20px;
}

.btn-tambah:hover {
    background: #0646ae;
}


/* =========================
   SEARCH
   ========================= */

.search-area {

    display: flex;

    gap: 10px;

    margin-bottom: 20px;
}


.search-box {

    flex: 1;

    position: relative;
}


.search-box input {

    width: 100%;

    padding: 12px 15px 12px 42px;

    border: 1px solid #dce2ea;

    border-radius: 10px;

    outline: none;

    font-size: 14px;

    background: #fafbfd;
}


.search-box input:focus {

    border-color: #0757c9;

    background: white;

    box-shadow:
        0 0 0 3px rgba(7,87,201,0.08);
}


.search-icon {

    position: absolute;

    left: 15px;

    top: 50%;

    transform: translateY(-50%);

    color: #8993a4;
}


/* =========================
   TABLE
   ========================= */

.table-wrapper {

    width: 100%;

    overflow-x: auto;
}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 650px;
}


thead th {

    background: #f4f7fb;

    color: #667085;

    padding: 14px;

    text-align: left;

    font-size: 12px;

    text-transform: uppercase;

    border-bottom: 1px solid #e7ebf0;
}


tbody td {

    padding: 15px 14px;

    border-bottom: 1px solid #edf0f5;

    font-size: 13px;
}


tbody tr:hover {

    background: #f8faff;
}


/* =========================
   KODE BARANG
   ========================= */

.kode {

    display: inline-block;

    background: #edf4ff;

    color: #0757c9;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: bold;

    font-size: 12px;
}


/* =========================
   NAMA
   ========================= */

.nama {

    font-weight: bold;

    color: #172033;
}


/* =========================
   STOK
   ========================= */

.stok {

    font-size: 15px;

    font-weight: bold;

    color: #173b7a;
}


/* =========================
   BADGE SATUAN
   ========================= */

.satuan {

    background: #f1f3f6;

    color: #596273;

    padding: 5px 9px;

    border-radius: 7px;

    font-size: 11px;

    font-weight: bold;
}


/* =========================
   ACTION
   ========================= */

.action {

    display: flex;

    gap: 7px;

    flex-wrap: wrap;
}


.action a {

    text-decoration: none;

    padding: 7px 11px;

border-radius: 7px;

    font-size: 11px;

    font-weight: bold;
}


.edit {

    background: #e8f2ff;

    color: #0757c9;
}


.delete {

    background: #ffecec;

    color: #d83232;
}


.edit:hover {

    background: #d8e9ff;
}


.delete:hover {

    background: #ffdcdc;
}


/* =========================
   EMPTY
   ========================= */

.empty {

    text-align: center;

    padding: 40px 20px;

    color: #8993a4;
}


.empty-icon {

    font-size: 40px;

    margin-bottom: 10px;
}


/* =========================
   FOOTER
   ========================= */

.footer {

    text-align: center;

    color: #8993a4;

    font-size: 12px;

    padding: 25px 0 10px;
}


/* =========================
   TABLET
   ========================= */

@media (max-width: 1000px) {

    .sidebar {

        width: 210px;
    }

    .main {

        margin-left: 210px;
    }

}


/* =========================
   MOBILE
   ========================= */

@media (max-width: 700px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

        padding: 18px;
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


    .info-cards {

        grid-template-columns: 1fr;
    }


    .panel-header {

        flex-direction: column;

        align-items: stretch;
    }


    .btn-primary {

        width: 100%;
    }


    .search-area {

        flex-direction: column;
    }


    .page-header h1 {

        font-size: 23px;
    }

}


/* =========================
   HP KECIL
   ========================= */

@media (max-width: 480px) {

    .menu {

        grid-template-columns: 1fr;
    }


    .logo-area img {

        width: 145px;
    }


    .page-header {

        padding: 22px;
    }


    .panel {

        padding: 15px;
    }

}

</style>

</head>


<body>


<!-- ==================================================
     SIDEBAR
     ================================================== -->

<div class="sidebar">

    <div class="logo-area">

        <img
            src="logo.jpg"
            alt="Logo PT Prima Sarana Gemilang"
        >

        <div class="company-name">
            PT Prima Sarana Gemilang
        </div>

    </div>


    <div class="menu">

        <a href="dashboard.php">

            <span class="menu-icon">🏠</span>

            Dashboard

        </a>


        <a href="barang.php" class="active">

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


<!-- ==================================================
     MAIN
     ================================================== -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="top-title">

            <h2>Data Barang</h2>

            <p>
                Kelola seluruh data persediaan barang
            </p>

        </div>


        <div class="user-box">

            👤
            <?= htmlspecialchars($_SESSION['username']); ?>

        </div>

    </div>



    <!-- HEADER -->

    <div class="page-header">

        <h1>📦 Data Barang</h1>

        <p>
            Tambah, cari, edit, dan kelola stok barang
            dengan mudah.
        </p>

    </div>



    <!-- INFO -->

    <div class="info-cards">


        <div class="info-card">

            <div class="info-icon">
                📦
            </div>

            <div>

<h3>
                    TOTAL JENIS BARANG
                </h3>

                <div class="info-number">

                    <?= number_format($total_barang); ?>

                </div>

            </div>

        </div>



        <div class="info-card">

            <div class="info-icon">
                📊
            </div>

            <div>

                <h3>
                    TOTAL STOK
                </h3>

                <div class="info-number">

                    <?= number_format($total_stok); ?>

                </div>

            </div>

        </div>


    </div>



    <!-- DATA PANEL -->

    <div class="panel">


        <div class="panel-header">


            <div class="panel-title">

                <h2>
                    Daftar Barang
                </h2>

                <p>
                    Data barang yang tersimpan dalam sistem
                </p>

            </div>


            <a href="tambah_barang.php" class="btn-tambah">
                ➕ Tambah Barang

            </a>


        </div>



        <!-- SEARCH -->

        <form
            method="GET"
            action="barang.php"
            class="search-area"
        >

            <div class="search-box">

                <span class="search-icon">
                    🔍
                </span>

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari kode atau nama barang..."
                    value="<?= htmlspecialchars($cari); ?>"
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >

                🔍 Cari

            </button>


            <?php if ($cari != ''): ?>

                <a
                    href="barang.php"
                    class="btn"
                    style="
                        background:#eef1f5;
                        color:#596273;
                    "
                >

                    Reset

                </a>

            <?php endif; ?>


        </form>



        <!-- TABLE -->

        <div class="table-wrapper">

            <table>


                <thead>

                    <tr>

                        <th width="60">
                            No
                        </th>

                        <th>
                            Kode Barang
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th>
                            Satuan
                        </th>

                        <th>
                            Stok
                        </th>

                        <th width="150">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                $no = 1;

                if (mysqli_num_rows($result) > 0):

                    while ($row = mysqli_fetch_assoc($result)):

                ?>


                    <tr>


                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>

                            <span class="kode">

                                <?= htmlspecialchars($row['kode']); ?>

                            </span>

                        </td>


                        <td>

                            <span class="nama">

                                <?= htmlspecialchars($row['nama']); ?>

                            </span>

                        </td>


                        <td>

                            <span class="satuan">

                                <?= htmlspecialchars($row['satuan']); ?>

                            </span>

                        </td>


                        <td>

                            <span class="stok">

<?= number_format((int)$row['stok']); ?>

                            </span>

                        </td>


                        <td>

                            <div class="action">


                                <a href="edit_barang.php?id=<?= $row['id'] ?>">
                                    ✏️ Edit

                                </a>


                                <a
                                    href="hapus_barang.php?id=<?= $row['id']; ?>"
                                    class="delete"
                                    onclick="return confirm(
                                        'Yakin ingin menghapus barang ini?'
                                    );"
                                >

                                    🗑️ Hapus

                                </a>


                            </div>

                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="6"
                            class="empty"
                        >

                            <div class="empty-icon">
                                📦
                            </div>

                            <strong>
                                Data barang tidak ditemukan
                            </strong>

                            <br>

                            <small>
                                Silakan tambahkan barang baru.
                            </small>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>

        </div>


    </div>


    <!-- FOOTER -->

    <div class="footer">

        © 2026 Sistem Laporan Stok Barang
        • PT Prima Sarana Gemilang

    </div>


</div>


</body>

</html>