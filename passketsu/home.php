<?php
include "auto_alfa.php";  // hanya include fungsi tanpa koneksi

auto_tandai_alfa(); 
if (!isset($_SESSION['user'])) {
    echo "<div class='alert alert-danger'>Anda belum login. Silakan login terlebih dahulu.</div>";
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_SESSION['user']['id_user']);
$level = $_SESSION['user']['level'] ?? 'anggota'; // Default anggota jika tidak ada level
$currentMonth = date('Y-m');

$kategori = ['Hadir', 'Izin', 'Sakit', 'Alfa'];
$statistik = [];

foreach ($kategori as $ket) {
    if ($level === 'admin') {
        // Admin lihat semua pengguna
        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE keterangan='$ket' AND DATE_FORMAT(tanggal, '%Y-%m') = '$currentMonth'");
    } else {
        // Anggota lihat data sendiri saja
        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE id_user='$id_user' AND keterangan='$ket' AND DATE_FORMAT(tanggal, '%Y-%m') = '$currentMonth'");
    }
    $data = mysqli_fetch_assoc($query);
    $statistik[$ket] = $data['total'] ?? 0;
}


// Ambil data pengguna
if ($level === 'admin') {
    // Jika admin, bisa pilih user untuk lihat profil atau tampilkan profil admin
    $queryUser = mysqli_query($koneksi, "SELECT nama, email, created_at FROM pengguna WHERE id_user = '$id_user'");
} else {
    // Anggota lihat profil sendiri
    $queryUser = mysqli_query($koneksi, "SELECT nama, email, created_at FROM pengguna WHERE id_user = '$id_user'");
}
$user = mysqli_fetch_assoc($queryUser);
?>

<style>
    .card {
        border-radius: 12px;
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
    }
    .card-body {
        text-align: center;
        padding: 20px;
    }
    .card i {
        font-size: 40px;
        margin-bottom: 10px;
    }
    .card h5 {
        font-size: 18px;
        font-weight: 600;
    }
</style>

<h1 class="mt-4"><i class="fas fa-home"></i> Dashboard Absensi</h1>
<ol class="breadcrumb mb-4"><li class="breadcrumb-item active">Dashboard</li></ol>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-5">
    <div class="col">
        <a href="?page=riwayat_absensi&filter=Hadir" class="text-decoration-none">
            <div class="card bg-success text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-check"></i>
                    <h5><?= $statistik['Hadir'] ?? 0; ?> Hadir</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="?page=riwayat_absensi&filter=Izin" class="text-decoration-none">
            <div class="card bg-info text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-clock"></i>
                    <h5><?= $statistik['Izin'] ?? 0; ?> Izin</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="?page=riwayat_absensi&filter=Sakit" class="text-decoration-none">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-md"></i>
                    <h5><?= $statistik['Sakit'] ?? 0; ?> Sakit</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="?page=riwayat_absensi&filter=Alfa" class="text-decoration-none">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-times"></i>
                    <h5><?= $statistik['Alfa'] ?? 0; ?> Alfa</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
    <?php if ($level === 'admin'): ?>
        <a href="?page=statistik_absensi" class="text-decoration-none">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar"></i>
                    <h5>Lihat Statistik Anggota</h5>
                </div>
            </div>
        </a>
    <?php else: ?>
        <a href="?page=statistik_absensi&user=<?= $id_user ?>" class="text-decoration-none">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar"></i>
                    <h5>Lihat Statistik Saya</h5>
                </div>
            </div>
        </a>
    <?php endif; ?>
</div>

</div>


<?php if ($user): ?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Profil Pengguna</h5>
            <table class="table">
                <tr><th>Nama</th><td>: <?= htmlspecialchars($user['nama']); ?></td></tr>
                <tr><th>Email</th><td>: <?= htmlspecialchars($user['email']); ?></td></tr>
                <tr><th>Dibuat Pada</th><td>: <?= htmlspecialchars($user['created_at']); ?></td></tr>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning mt-4">Data pengguna tidak ditemukan.</div>
<?php endif; ?>
