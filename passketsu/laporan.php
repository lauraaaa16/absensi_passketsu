<?php
// Ambil 5 user dengan absensi terbanyak (misal berdasarkan jumlah hadir)
$user_query = "SELECT pengguna.id_user, pengguna.nama, COUNT(absensi.id_absensi) AS total_absen 
               FROM absensi 
               JOIN pengguna ON pengguna.id_user = absensi.id_user 
               GROUP BY absensi.id_user 
               ORDER BY total_absen DESC 
               LIMIT 5";
$user_result = mysqli_query($koneksi, $user_query);
$users = [];
while ($row = mysqli_fetch_assoc($user_result)) {
    $users[] = $row;
}

$tanggal_sekarang = date('Y-m-d');

// Query utama ambil data absensi dan hitung status final (misal: 'Terlambat' kalau ada aturan tambahan, disini kita anggap status sesuai keterangan)
$query = "SELECT absensi.*, pengguna.nama 
          FROM absensi 
          LEFT JOIN pengguna ON pengguna.id_user = absensi.id_user";
$result = mysqli_query($koneksi, $query);
?>

<h1 class="mt-4">
    <i class="fas fa-file-alt"></i> Laporan Absensi
</h1>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <!-- Filter Status (Keterangan Absensi) -->
                <div class="input-group mb-3">
                    <label class="input-group-text">Filter Keterangan</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">Semua</option>
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alfa">Alfa</option>
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div class="input-group mb-3">
                    <label class="input-group-text">Filter Tanggal</label>
                    <input type="date" id="tanggalFilter" class="form-control">
                </div>

                <!-- Filter User -->
                <div class="input-group mb-3">
                    <label class="input-group-text">User</label>
                    <select id="userFilter" class="form-control">
                        <option value="">Semua</option>
                        <?php foreach ($users as $user) : ?>
                            <option value="<?php echo strtolower(htmlspecialchars($user['nama'])); ?>">
                                <?php echo htmlspecialchars($user['nama']); ?> (<?php echo $user['total_absen']; ?>x)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <a href="cetak_absensi.php?status=&tanggal=&user=" id="cetakBtn" target="_blank" class="btn btn-info mb-3">
                    <i class="fa fa-print"></i> Cetak Data
                </a>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($data = mysqli_fetch_assoc($result)) :
                        ?>
                            <tr class="laporan-row" 
                                data-status="<?php echo strtolower($data['keterangan']); ?>" 
                                data-tanggal="<?php echo $data['tanggal']; ?>"
                                data-user="<?php echo strtolower(htmlspecialchars($data['nama'])); ?>">
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                <td><?php echo htmlspecialchars($data['tanggal']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($data['keterangan'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("statusFilter").addEventListener("change", filterTable);
document.getElementById("tanggalFilter").addEventListener("change", filterTable);
document.getElementById("userFilter").addEventListener("change", filterTable);

function filterTable() {
    var status = document.getElementById("statusFilter").value.toLowerCase();
    var tanggal = document.getElementById("tanggalFilter").value;
    var user = document.getElementById("userFilter").value.toLowerCase();

    var rows = document.querySelectorAll(".laporan-row");

    rows.forEach(function(row) {
        var rowStatus = row.getAttribute("data-status");
        var rowTanggal = row.getAttribute("data-tanggal");
        var rowUser = row.getAttribute("data-user");

        var statusMatch = (status === "" || rowStatus === status);
        var tanggalMatch = (tanggal === "" || rowTanggal === tanggal);
        var userMatch = (user === "" || rowUser === user);

        if (statusMatch && tanggalMatch && userMatch) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    document.getElementById("cetakBtn").href = `cetak_absensi.php?status=${status}&tanggal=${tanggal}&user=${user}`;
}
</script>
