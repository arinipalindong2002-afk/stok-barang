<?php
require_once "koneksi.php";

// ========================================
// AMBIL FILTER TANGGAL
// ========================================

$dari = isset($_GET['dari'])
    ? $_GET['dari']
    : date('Y-m-01');

$sampai = isset($_GET['sampai'])
    ? $_GET['sampai']
    : date('Y-m-d');


// ========================================
// VALIDASI TANGGAL
// ========================================

if (empty($dari)) {
    $dari = date('Y-m-01');
}

if (empty($sampai)) {
    $sampai = date('Y-m-d');
}


// ========================================
// NAMA FILE EXCEL
// ========================================

$nama_file =
    "Laporan_Transaksi_"
    . date('d-m-Y', strtotime($dari))
    . "_sd_"
    . date('d-m-Y', strtotime($sampai))
    . ".xls";


// ========================================
// HEADER EXCEL
// ========================================

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");


// ========================================
// QUERY DATA TRANSAKSI
// ========================================

$dari_sql =
    mysqli_real_escape_string($conn, $dari);

$sampai_sql =
    mysqli_real_escape_string($conn, $sampai);

$query = mysqli_query(
    $conn,
    "SELECT
        transaksi.id,
        transaksi.tanggal,
        barang.kode,
        barang.nama,
        transaksi.jenis,
        transaksi.jumlah,
        barang.satuan,
        transaksi.keterangan
     FROM transaksi
     INNER JOIN barang
        ON transaksi.barang_id = barang.id
     WHERE transaksi.tanggal
        BETWEEN '$dari_sql'
        AND '$sampai_sql'
     ORDER BY transaksi.tanggal ASC,
              transaksi.id ASC"
);


// ========================================
// JUDUL LAPORAN
// ========================================

echo "<html>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "</head>";
echo "<body>";

echo "<h2>LAPORAN TRANSAKSI STOK</h2>";

echo "<h3>PT Prima Sarana Gemilang</h3>";

echo "<p>";
echo "Periode: ";
echo date('d-m-Y', strtotime($dari));
echo " s/d ";
echo date('d-m-Y', strtotime($sampai));
echo "</p>";


// ========================================
// TABEL
// ========================================

echo "<table border='1'>";

echo "<tr style='font-weight:bold;background:#D9EAF7;'>";

echo "<th>No</th>";
echo "<th>Tanggal</th>";
echo "<th>Kode Barang</th>";
echo "<th>Nama Barang</th>";
echo "<th>Jenis Transaksi</th>";
echo "<th>Jumlah</th>";
echo "<th>Satuan</th>";
echo "<th>Keterangan</th>";

echo "</tr>";


// ========================================
// DATA
// ========================================

$no = 1;

$total_masuk = 0;
$total_keluar = 0;


if ($query && mysqli_num_rows($query) > 0) {

    while ($row = mysqli_fetch_assoc($query)) {

        $jenis =
            strtolower($row['jenis']);

        if ($jenis == 'masuk') {

            $total_masuk +=
                (int)$row['jumlah'];

            $jenis_text = "STOK MASUK";

        } else {

            $total_keluar +=
                (int)$row['jumlah'];

            $jenis_text = "STOK KELUAR";
        }


        echo "<tr>";

        echo "<td>";
        echo $no++;
        echo "</td>";


        echo "<td>";
        echo date(
            'd-m-Y',
            strtotime($row['tanggal'])
        );
        echo "</td>";


        echo "<td>";
        echo htmlspecialchars(
            $row['kode']
        );
        echo "</td>";


        echo "<td>";
        echo htmlspecialchars(
            $row['nama']
        );
        echo "</td>";


        echo "<td>";
        echo $jenis_text;
        echo "</td>";


        echo "<td>";
        echo $row['jumlah'];
        echo "</td>";


        echo "<td>";
        echo htmlspecialchars(
            $row['satuan']
        );
        echo "</td>";


        echo "<td>";
        echo htmlspecialchars(
            $row['keterangan']
            ?: '-'
        );
        echo "</td>";


        echo "</tr>";
    }

} else {

    echo "<tr>";

    echo "<td colspan='8'>";
    echo "Tidak ada transaksi pada periode tersebut.";
    echo "</td>";

    echo "</tr>";
}


// ========================================
// TOTAL
// ========================================

echo "<tr>";
echo "<td colspan='5'><b>TOTAL STOK MASUK</b></td>";
echo "<td><b>$total_masuk</b></td>";
echo "<td colspan='2'></td>";
echo "</tr>";


echo "<tr>";
echo "<td colspan='5'><b>TOTAL STOK KELUAR</b></td>";
echo "<td><b>$total_keluar</b></td>";
echo "<td colspan='2'></td>";
echo "</tr>";


echo "</table>";

echo "<br>";

echo "<p>";
echo "Dicetak pada: ";
echo date('d-m-Y H:i:s');
echo "</p>";

echo "</body>";
echo "</html>";

exit;
?>