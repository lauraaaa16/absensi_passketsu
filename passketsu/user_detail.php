<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id_user='$id'");
    $data = mysqli_fetch_array($query);
}

// Cek level user yang sedang login
$level_login = $_SESSION['user']['level'];
?>

<h1 class="mt-4">
    <i class="fas fa-users"></i> Detail Pengguna
</h1>
<div class="card">
    <div class="card-body">
        <a href="javascript:history.back()" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <table class="table table-bordered">
            <tr>
                <th>Nama</th>
                <td><?php echo htmlspecialchars($data['nama']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($data['email']); ?></td>
            </tr>
            <tr>
                <th>Tanggal Daftar</th>
                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
            </tr>

            <?php if ($level_login == 'admin') { ?>
                <tr>
                    <th>No HP</th>
                    <td><?php echo htmlspecialchars($data['nohp']); ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?php echo htmlspecialchars($data['alamat']); ?></td>
                </tr>
            <?php } ?>

            <tr>
                <th>Riwayat Absensi</th>
                <td>
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Keterangan</th>
                        </tr>
                        <?php
                        $i = 1;
                        // Query riwayat absensi (ambil tanggal & waktu dari created_at)
                        $query_riwayat = mysqli_query($koneksi, "SELECT tanggal, keterangan, DATE_FORMAT(created_at, '%H:%i:%s') AS waktu 
                                                                 FROM absensi 
                                                                 WHERE id_user = '$id'
                                                                 ORDER BY tanggal DESC");
                        if (mysqli_num_rows($query_riwayat) > 0) {
                            while ($riwayat = mysqli_fetch_array($query_riwayat)) {
                                echo "<tr>
                                        <td>{$i}</td>
                                        <td>" . htmlspecialchars($riwayat['tanggal']) . "</td>
                                        <td>" . htmlspecialchars($riwayat['waktu']) . "</td>
                                        <td>" . htmlspecialchars($riwayat['keterangan']) . "</td>
                                      </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Belum ada riwayat absensi</td></tr>";
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
