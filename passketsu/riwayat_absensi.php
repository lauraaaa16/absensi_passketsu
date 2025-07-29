<?php

$level = $_SESSION['user']['level'] ?? 'anggota';
$id_user = $_SESSION['user']['id_user'] ?? '';

// Tangkap filter dari URL
$filter = $_GET['filter'] ?? '';

// Daftar kategori valid untuk filter
$kategoriValid = ['Hadir', 'Izin', 'Sakit', 'Alfa'];

// Escape untuk keamanan
$filter_escaped = mysqli_real_escape_string($koneksi, $filter);
$id_user_escaped = mysqli_real_escape_string($koneksi, $id_user);

// Bangun query
if ($level === 'admin') {
    // Admin bisa lihat semua data, dengan filter jika ada
    if ($filter && in_array($filter, $kategoriValid)) {
        $sql = "SELECT absensi.*, pengguna.nama 
                FROM absensi 
                LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user
                WHERE absensi.keterangan = '$filter_escaped'
                ORDER BY absensi.tanggal DESC";
    } else {
        $sql = "SELECT absensi.*, pengguna.nama 
                FROM absensi 
                LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user
                ORDER BY absensi.tanggal DESC";
    }
} else {
    // Anggota hanya lihat data sendiri, dengan filter jika ada
    if ($filter && in_array($filter, $kategoriValid)) {
        $sql = "SELECT absensi.*, pengguna.nama 
                FROM absensi 
                LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user
                WHERE absensi.id_user = '$id_user_escaped' AND absensi.keterangan = '$filter_escaped'
                ORDER BY absensi.tanggal DESC";
    } else {
        $sql = "SELECT absensi.*, pengguna.nama 
                FROM absensi 
                LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user
                WHERE absensi.id_user = '$id_user_escaped'
                ORDER BY absensi.tanggal DESC";
    }
}

$query = mysqli_query($koneksi, $sql);

?>

<h1 class="mt-4">
    <i class="fas fa-history"></i> Riwayat Absensi Paskibra
</h1>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?php if ($level == 'admin' || $level == 'anggota') { ?>
                <!-- Input Pencarian (Hanya Admin & Anggota) -->
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchBox" class="form-control" placeholder="Cari riwayat absensi...">
                </div>
                <?php } ?>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Anggota</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Bukti</th> <!-- Tambahan -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr class="absensi-row">
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                <td><?= htmlspecialchars($data['tanggal']); ?></td>
                                <td><?= htmlspecialchars($data['keterangan']); ?></td>
                                <td>
                                    <?php 
                                    if (($data['keterangan'] == 'Izin' || $data['keterangan'] == 'Sakit') && !empty($data['bukti'])) {
                                        // Tampilkan file bukti
                                        echo "<a href='uploads/bukti/" . htmlspecialchars($data['bukti']) . "' target='_blank' class='btn btn-info btn-sm'>
                                                <i class='fas fa-file'></i> Lihat Bukti
                                              </a>";
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <a href="javascript:history.back()" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($level == 'admin' || $level == 'anggota') { ?>
<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        var searchText = this.value.toLowerCase();
        var rows = document.querySelectorAll(".absensi-row");

        rows.forEach(function(row) {
            var rowData = row.textContent.toLowerCase();
            row.style.display = rowData.includes(searchText) ? "" : "none";
        });
    });
</script>
<?php } ?>
