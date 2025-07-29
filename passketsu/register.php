<?php
    include("koneksi.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Register Absensi Digital" />
    <meta name="author" content="Perpustakaan Digital" />
    <title>Register - Absensi Digital</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lobster&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
       body {
            font-family: 'Poppins', sans-serif;
            background-color:rgb(255, 253, 246);
            color: #4a403a; /* Deep Brown */
            margin: 0;
            padding: 0;
        }


        .form-control:focus {
            box-shadow: 0 0 8px rgba(146, 68, 68, 0.6);
        }

        .btn-primary {
            background-color: #A94C4C; /* Maroon Medium */
            border: none;
            transition: 0.3s;
            color: white;
        }

        .btn-primary:hover {
            background-color: #C17171; /* Dark Maroon Hover */
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(86, 36, 36, 0.4); /* #562424 shadow */
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            border-radius: 15px;
            background-color: rgb(255, 253, 246); /* White Background */
            border: 1px solid #A94C4C; /* Medium Maroon Border */
        }

        .card-header {
            background-color:  #A94C4C; /* Dark to Medium Maroon */
            color: white;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .card-footer {
            background: linear-gradient(135deg, #A94C4C, #A94C4C); /* Medium to Soft Maroon */
            color: white;
            border-radius: 0 0 15px 15px;
        }

        .alert-danger {
            background-color: #C17171; /* Soft Light Maroon */
            color: #562424; /* Dark Text Maroon */
            border: 1px solid #A94C4C; /* Soft Maroon Border */
        }

        .logo {
            width: 80px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
            <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5 fade-in">
                                <div class="card-header text-center">
                                    <img src="assets/img/logpas.png" alt="Library Logo" width="80" class="mb-2">
                                    <h3 class="font-weight-light my-3">Register Absensi Digital</h3>
                                </div>
                                <div class="card-body">
                                <?php

// Ambil data kelas dari database, urutkan berdasarkan tingkat dan nama kelas
$kelas_data = [];
$result = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY tingkat_kelas, nama_kelas");
while ($row = mysqli_fetch_assoc($result)) {
    $kelas_data[] = $row;
}

// Buat list tingkat unik dari data kelas
$tingkat_unik = [];
foreach ($kelas_data as $kelas) {
    if (!in_array($kelas['tingkat_kelas'], $tingkat_unik)) {
        $tingkat_unik[] = $kelas['tingkat_kelas'];
    }
}
sort($tingkat_unik); // Urutkan tingkat secara alfabet (opsional)

// Proses registrasi saat tombol register ditekan
if (isset($_POST['register'])) {
    $nama = $_POST['nama_lengkap']; // ambil input nama_lengkap tapi simpan ke $nama
    $email = $_POST['email'];
    $nohp = $_POST['nohp'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirm_password = $_POST['konfirm_password'];
    $id_kelas = $_POST['id_kelas'];
    $level = "anggota";

    // Validasi password konfirmasi
    if ($password != $konfirm_password) {
        echo '<div class="alert alert-danger text-center">Password dan konfirmasi tidak sama!</div>';
    }
    // Validasi email @gmail.com
    elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        echo '<div class="alert alert-danger text-center">Gunakan email dengan format @gmail.com!</div>';
    }
    // Validasi no HP 10-15 digit angka
    elseif (!preg_match("/^[0-9]{10,15}$/", $nohp)) {
        echo '<div class="alert alert-danger text-center">Nomor telepon harus 10-15 digit!</div>';
    }
    else {
        $cek_email = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE email='$email'");
        $cek_username = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE username='$username'");

        if (mysqli_num_rows($cek_email) > 0) {
            echo '<div class="alert alert-danger text-center">Email sudah digunakan!</div>';
        } elseif (mysqli_num_rows($cek_username) > 0) {
            echo '<div class="alert alert-danger text-center">Username sudah digunakan!</div>';
        } else {
            $password_hash = md5($password);
            $insert = mysqli_query($koneksi, "INSERT INTO pengguna (nama, email, alamat, nohp, username, password, level, id_kelas) VALUES ('$nama', '$email', '$alamat', '$nohp', '$username', '$password_hash', '$level', '$id_kelas')");
            if ($insert) {
                echo '<script>alert("Registrasi berhasil!"); location.href="login.php"</script>';
                exit;
            } else {
                echo '<div class="alert alert-danger text-center">Register gagal, silakan coba lagi.</div>';
            }
        }
    }
}
?>

<form method="post" id="registerForm" class="mx-auto" style="max-width: 400px;">
    <h3 class="mb-4 text-center">Form Registrasi</h3>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" required />
        <label>Nama Lengkap</label>
    </div>

    <div class="form-floating mb-3">
        <input id="email" type="email" class="form-control" name="email" placeholder="Masukkan Email" required />
        <label>Email</label>
        <div id="emailWarning" class="text-danger d-none small mt-1">Gunakan email dengan format @gmail.com!</div>
    </div>

    <div class="form-floating mb-3">
        <input id="nohp" type="text" class="form-control" name="nohp" placeholder="Masukkan No HP" maxlength="15" required />
        <label>No HP</label>
        <div id="nohpWarning" class="text-danger d-none small mt-1">Nomor telepon harus 10-15 digit!</div>
    </div>

    <div class="form-floating mb-3">
        <textarea class="form-control" name="alamat" placeholder="Masukkan Alamat" required></textarea>
        <label>Alamat</label>
    </div>

    <!-- Dropdown Tingkat dari DB -->
    <div class="form-floating mb-3">
        <select id="tingkat" class="form-control" required>
            <option value="">-- Pilih Tingkat --</option>
            <?php foreach ($tingkat_unik as $tingkat): ?>
                <option value="<?= htmlspecialchars($tingkat) ?>"><?= htmlspecialchars($tingkat) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Tingkat</label>
    </div>

    <!-- Dropdown Nama Kelas -->
    <div class="form-floating mb-3">
        <select id="id_kelas" name="id_kelas" class="form-control" required>
            <option value="">-- Pilih Kelas --</option>
        </select>
        <label>Nama Kelas</label>
    </div>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required />
        <label>Username</label>
    </div>

    <div class="form-floating mb-3">
        <input id="password" type="password" class="form-control" name="password" placeholder="Password" required />
        <label>Password</label>
        <div id="passwordWarning" class="text-danger d-none small mt-1">Password minimal 8 karakter</div>
    </div>

    <div class="form-floating mb-3">
        <input id="konfirm_password" type="password" class="form-control" name="konfirm_password" placeholder="Konfirmasi Password" required />
        <label>Konfirmasi Password</label>
    </div>

    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasData = <?= json_encode($kelas_data); ?>;
    const tingkatSelect = document.getElementById('tingkat');
    const kelasSelect = document.getElementById('id_kelas');

    tingkatSelect.addEventListener('change', function() {
        const selectedTingkat = this.value;
        kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';

        kelasData.forEach(kelas => {
            if (kelas.tingkat_kelas === selectedTingkat) {
                const option = document.createElement('option');
                option.value = kelas.id_kelas;
                option.textContent = kelas.nama_kelas;
                kelasSelect.appendChild(option);
            }
        });
    });

    // Validasi email format @gmail.com
    document.getElementById('email').addEventListener('input', function() {
        const warning = document.getElementById('emailWarning');
        const regex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (regex.test(this.value)) {
            warning.classList.add('d-none');
        } else {
            warning.classList.remove('d-none');
        }
    });

    // Validasi nomor hp angka 10-15 digit
    document.getElementById('nohp').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        const warning = document.getElementById('nohpWarning');
        if (this.value.length >= 10 && this.value.length <= 15) {
            warning.classList.add('d-none');
        } else {
            warning.classList.remove('d-none');
        }
    });

    // Validasi password minimal 8 karakter
    document.getElementById('password').addEventListener('input', function() {
        const warning = document.getElementById('passwordWarning');
        if (this.value.length >= 8) {
            warning.classList.add('d-none');
        } else {
            warning.classList.remove('d-none');
        }
    });

    // Cegah submit kalau validasi email gagal
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const emailWarning = document.getElementById('emailWarning');
        if (!emailWarning.classList.contains('d-none')) {
            alert('Gunakan email dengan format @gmail.com!');
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
