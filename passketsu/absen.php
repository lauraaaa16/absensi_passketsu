<?php
$level = $_SESSION['user']['level'] ?? '';
$hari_ini = date('l'); // contoh: Monday, Tuesday, Wednesday
$jam_sekarang = date('H:i'); // contoh: 15:30

// Jadwal ekstra per hari
$jadwal_ekstra = [
    'Wednesday' => ['mulai' => '15:00', 'selesai' => '17:00'], // Rabu
    'Friday'    => ['mulai' => '13:00', 'selesai' => '17:00'], // Jumat
];

// Jika bukan admin, cek hari & jam
if ($level !== 'admin') {
    if (!array_key_exists($hari_ini, $jadwal_ekstra)) {
        echo "<script>
                alert('Absensi hanya dapat diakses pada hari Rabu atau Jumat.');
                window.location.href='index.php';
              </script>";
        exit;
    } else {
        $jam_mulai = $jadwal_ekstra[$hari_ini]['mulai'];
        $jam_selesai = $jadwal_ekstra[$hari_ini]['selesai'];

        if ($jam_sekarang < $jam_mulai || $jam_sekarang > $jam_selesai) {
            echo "<script>
                    alert('Absensi hari ini hanya dapat diakses jam $jam_mulai - $jam_selesai.');
                    window.location.href='index.php';
                  </script>";
            exit;
        }
    }
}
// ===================================================================

// ==================== CEK LOGIN ====================
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='index.php';</script>";
    exit;
}

$id_user = $_SESSION['user']['id_user'] ?? null;
if (!$id_user) {
    die("Error: User ID tidak ditemukan di session.");
}

// Ambil nama user dari tabel pengguna
$res_user = mysqli_query($koneksi, "SELECT nama FROM pengguna WHERE id_user='$id_user'");
if (!$res_user || mysqli_num_rows($res_user) == 0) {
    die("User tidak ditemukan di tabel pengguna.");
}
$row_user = mysqli_fetch_assoc($res_user);
$nama_user = $row_user['nama'];

$tanggal_absensi = date('Y-m-d');

if (isset($_POST['submit'])) {
    $status = $_POST['status'] ?? null;  
    $keterangan_tambahan = $_POST['keterangan_tambahan'] ?? null;

    if (!$status) {
        echo "<script>alert('Harap pilih status absensi!'); history.back();</script>";
        exit;
    }

    // Jika status Hadir, otomatis isi keterangan "Hadir"
    if ($status == 'Hadir') {
        $keterangan_tambahan = 'Hadir';
    }

    // Cek sudah absen hari ini
    $cek_absen = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_user='$id_user' AND tanggal='$tanggal_absensi'");
    if (!$cek_absen) {
        die("Error cek absensi: " . mysqli_error($koneksi));
    }
    if (mysqli_num_rows($cek_absen) > 0) {
        echo "<script>alert('Anda sudah mengisi absensi hari ini.'); window.location.href='index.php';</script>";
        exit;
    }

    $bukti_path = ''; // default kosong

    // Jika status Izin/Sakit, wajib upload bukti
    if ($status == 'Izin' || $status == 'Sakit') {
        if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] == UPLOAD_ERR_NO_FILE) {
            echo "<script>alert('Harap upload bukti untuk status $status.'); history.back();</script>";
            exit;
        }
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $file_type = $_FILES['bukti']['type'];
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('File bukti harus berupa JPG, PNG, atau PDF.'); history.back();</script>";
            exit;
        }

        $upload_dir = __DIR__ . '/uploads/bukti_absensi/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $nama_file = 'bukti_'.$id_user.'_'.date('YmdHis').'.'.$ext;
        $target_file = $upload_dir . $nama_file;

        if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
            echo "<script>alert('Gagal mengupload file bukti.'); history.back();</script>";
            exit;
        }
        $bukti_path = 'uploads/bukti_absensi/' . $nama_file;
    }

    // Insert absensi
    $stmt = mysqli_prepare($koneksi, "INSERT INTO absensi (id_user, nama, tanggal, status, keterangan, bukti) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssss", $id_user, $nama_user, $tanggal_absensi, $status, $keterangan_tambahan, $bukti_path);
    $exec = mysqli_stmt_execute($stmt);

    if (!$exec) {
        die("Gagal menyimpan absensi: " . mysqli_error($koneksi));
    }
    mysqli_stmt_close($stmt);

    echo "<script>alert('Absensi berhasil disimpan!'); window.location.href='index.php';</script>";
    exit;
}
?>

<h1 class="mt-4">Isi Absensi Hari Ini</h1>
<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <label class="col-md-2 col-form-label">Tanggal</label>
                <div class="col-md-8">
                    <input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($tanggal_absensi) ?>" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-2 col-form-label">Status</label>
                <div class="col-md-8">
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Hadir">Hadir</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3" id="buktiRow" style="display:none;">
                <label class="col-md-2 col-form-label">Upload Bukti</label>
                <div class="col-md-8">
                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" class="form-control" />
                    <small class="text-muted">Format: JPG, PNG, PDF. Maks 5MB.</small>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-md-2 col-form-label">Keterangan Tambahan</label>
                <div class="col-md-8">
                    <textarea name="keterangan_tambahan" id="keterangan" class="form-control" placeholder="(Opsional) Keterangan tambahan..."></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <button type="submit" class="btn btn-info" name="submit" value="submit">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <a href="?page=home" class="btn btn-danger">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('status').addEventListener('change', function() {
    const val = this.value;
    const buktiRow = document.getElementById('buktiRow');
    const keterangan = document.getElementById('keterangan');

    if (val === 'Izin' || val === 'Sakit') {
        buktiRow.style.display = 'flex';
        keterangan.value = ''; // kosongkan agar bisa diisi manual
        keterangan.removeAttribute('readonly');
    } else if (val === 'Hadir') {
        buktiRow.style.display = 'none';
        keterangan.value = 'Hadir'; // otomatis isi
        keterangan.setAttribute('readonly', true);
    } else {
        buktiRow.style.display = 'none';
        keterangan.value = '';
        keterangan.removeAttribute('readonly');
    }
});
</script>
