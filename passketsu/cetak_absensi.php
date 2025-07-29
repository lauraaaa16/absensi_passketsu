<?php
include("koneksi.php");

// Ambil filter dari laporan_absensi.php
$status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$tanggal = isset($_GET['tanggal']) ? mysqli_real_escape_string($koneksi, $_GET['tanggal']) : '';
$user = isset($_GET['user']) ? mysqli_real_escape_string($koneksi, $_GET['user']) : '';

// Query utama untuk menampilkan data absensi
$query = "SELECT absensi.*, pengguna.nama, pengguna.email 
          FROM absensi 
          LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user
          WHERE 1";

// Terapkan filter user jika ada
if (!empty($user)) {
    $query .= " AND pengguna.nama = '$user'";
}

// Terapkan filter status/keterangan jika ada
if (!empty($status)) {
    $query .= " AND absensi.keterangan = '$status'";
}

// Terapkan filter tanggal jika ada
if (!empty($tanggal)) {
    $query .= " AND absensi.tanggal = '$tanggal'";
}

$query .= " ORDER BY absensi.tanggal DESC, absensi.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<h2 style="text-align:center;">Laporan Absensi</h2>
<p style="text-align:center;">Dicetak pada: <?= date("d-m-Y H:i:s"); ?></p>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Keterangan</th>
    </tr>
    <?php
    $i = 1;
    if (mysqli_num_rows($result) > 0) {
        while ($data = mysqli_fetch_array($result)) {
            echo "<tr>
                    <td>{$i}</td>
                    <td>" . htmlspecialchars($data['nama']) . "</td>
                    <td>" . htmlspecialchars($data['email']) . "</td>
                    <td>" . htmlspecialchars($data['tanggal']) . "</td>
                    <td>" . date('H:i:s', strtotime($data['created_at'])) . "</td>
                    <td>" . htmlspecialchars($data['keterangan']) . "</td>
                  </tr>";
            $i++;
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data absensi</td></tr>";
    }
    ?>
</table>

<script>
    window.print();
    setTimeout(function(){
        window.close();               
    }, 100);
</script>
