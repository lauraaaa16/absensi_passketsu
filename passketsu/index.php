<?php
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect ke halaman login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Perpustakaan Digital</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lobster&display=swap" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.php">Absensi Digital</a>
            <button class="btn btn-link order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
                <i class="fas fa-bars"></i>
            </button>

            <div class="d-flex align-items-center ms-auto" id="currentDate"></div>

            
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="nav-link" href="?page=akun">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Account
                            </a>
                        </li>
                    </ul>

                </li>
            </ul>
        </nav>

        <script>
        function updateDate() {
                const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

                let now = new Date();
                let hariIni = hari[now.getDay()];
                let tanggal = now.getDate();
                let bulanIni = bulan[now.getMonth()];
                let tahun = now.getFullYear();

                // Cek ukuran layar, jika kecil tampilkan format singkat
                if (window.innerWidth < 576) {
                    document.getElementById("currentDate").innerHTML = `${tanggal}/${now.getMonth() + 1}/${tahun}`;
                } else {
                    document.getElementById("currentDate").innerHTML = `${hariIni}, ${tanggal} ${bulanIni} ${tahun}`;
                }
            }

            // Perbarui saat halaman dimuat
            updateDate();

            // Perbarui saat ukuran layar berubah
            window.addEventListener("resize", updateDate);
        </script>


        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <?php
                            $id_user = $_SESSION['user']['id_user'];
                            $profilePhoto = 'uploads/default.png';
                            $userLevel = "Pengguna";

                            $result = mysqli_query($koneksi, "SELECT profile_photo FROM pengguna WHERE id_user='$id_user'");
                            $row = mysqli_fetch_assoc($result);

                            if (!empty($row['profile_photo'])) {
                                $profilePhoto = $row['profile_photo'];
                                $_SESSION['photo_profile'] = $profilePhoto; 
                            }

                            if (isset($_SESSION['user']['level'])) {
                                $level = $_SESSION['user']['level'];
                                if ($level == 'admin') {
                                    $userLevel = "Administrator";
                                } elseif ($level == 'anggota') {
                                    $userLevel = "Anggota Passketsu";
                                }
                            }
                            ?>

                            <div class="text-center m-2">
                                <aside>
                                    <?php
                                    if (!empty($_SESSION['photo_profile'])) {
                                        $photoProfile = $_SESSION['photo_profile'];
                                    } else {
                                        $adminPhotos = glob("assets/img/2.png/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                                        
                                        if (!empty($adminPhotos)) {
                                            $photoProfile = $adminPhotos[array_rand($adminPhotos)];
                                        } else {
                                            $photoProfile = "assets/img/2.png"; 
                                        }
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($photoProfile) ?>" alt="Foto Profil" class="profile-img">
                                    <style>
                                        .profile-img {
                                            border-radius: 50%;
                                            width: 100px;
                                            height: 100px;
                                            object-fit: cover;
                                        }
                                    </style>
                                </aside>
                                <p class="text-muted"><?php echo $userLevel; ?></p>
                                <p class="mt-3">Hi, <?php echo htmlspecialchars($_SESSION['user']['username']); ?> 👋</p>
                            </div>

                            
<a class="nav-link d-flex align-items-center" href="?">
    <div class="sb-nav-link-icon me-2"><i class="fas fa-tachometer-alt"></i></div>
    <span>Dashboard</span>
</a>
<div class="sb-sidenav-menu-heading">Navigasi</div>
<?php
if (isset($_SESSION['user']['level'])) {
    $level = $_SESSION['user']['level'];

    if ($level == 'admin') {
        // Navigasi khusus admin (tanpa dashboard karena sudah ada di atas)
        ?>
        <a class="nav-link d-flex align-items-center" href="?page=user">
            <div class="sb-nav-link-icon me-2"><i class="fas fa-calendar-check"></i></div>
            <span>Data Pengguna</span>
        </a>
        <a class="nav-link d-flex align-items-center" href="?page=riwayat_absensi">
            <div class="sb-nav-link-icon me-2"><i class="fas fa-calendar-check"></i></div>
            <span>Kelola Absensi</span>
        </a>
        <a class="nav-link d-flex align-items-center" href="?page=laporan">
            <div class="sb-nav-link-icon me-2"><i class="fas fa-file-alt"></i></div>
            <span>Laporan Absensi</span>
        </a>
        <?php
    } elseif ($level == 'anggota') {
        // Navigasi khusus anggota (tanpa dashboard karena sudah ada di atas)
        ?>
        <a class="nav-link d-flex align-items-center" href="?page=absen">
            <div class="sb-nav-link-icon me-2"><i class="fas fa-edit"></i></div>
            <span>Isi Absensi</span>
        </a>
        <a class="nav-link d-flex align-items-center" href="?page=riwayat_absensi">
            <div class="sb-nav-link-icon me-2"><i class="fas fa-history"></i></div>
            <span>Riwayat Absensi</span>
        </a>
        <?php
    }
}
?>
<li>
    <a class="nav-link d-flex align-items-center" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');">
        <div class="sb-nav-link-icon me-2"><i class="fas fa-power-off"></i></div>
        <span>Logout</span>
    </a>
</li>

                            </li>
                        </div>
                    </div>
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <?php
                        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                        if (file_exists($page . '.php')) {
                            include $page . '.php';
                        } else {
                            include '404.php';
                        }
                        ?>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Laurand Libraries</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
<style>
    #currentDate {
    white-space: nowrap;
    font-size: 14px; /* Sesuaikan agar tidak terlalu besar */
    text-align: right;
    flex-grow: 1; /* Agar bisa mengisi ruang kosong */
    color: white; /* Sesuaikan warna dengan navbar */
    margin-left: auto; /* Dorong elemen ke kanan */
}

/* Jika layar kecil (misalnya, lebar < 576px), kurangi font atau sembunyikan */
@media (max-width: 576px) {
    #currentDate {
        font-size: 12px;
    }
}

</style>