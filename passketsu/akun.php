<?php

$id_user = $_SESSION['user']['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika user belum memiliki foto profil, gunakan default
$profilePhoto = !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : 'assets/img/2.png';

// Menentukan level pengguna
$userLevel = "Pengguna"; 
if (isset($user['level'])) {
    if ($user['level'] == 'admin') {
        $userLevel = "Administrator";
    } elseif ($user['level'] == 'anggota') {
        $userLevel = "Anggota";
    } elseif ($user['level'] == 'petugas') {
        $userLevel = "Petugas";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Lobster&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container { 
            max-width: 600px; 
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        img { 
            border-radius: 50%;
            width: 120px; 
            height: 120px; 
            object-fit: cover; 
        }
        .btn-wrapper { 
            margin-top: 20px; 
        }
        span.profile-text { 
            font-weight: bold; 
        }
        /* Responsiveness */
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            img {
                width: 100px;
                height: 100px;
            }
            .btn {
                font-size: 14px;
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>

<!-- Container Profil -->
<div class="container">
    <h2>Profil Pengguna</h2>
    <form method="POST" enctype="multipart/form-data">
      
        <div style="position: relative; display: inline-block;">
            <img src="<?php echo $profilePhoto; ?>" alt="Foto Profil">
            <a href="?page=upload_profile" title="Perbarui Profil" style="position: absolute; bottom: 5px; right: 5px; background: #fff; padding: 5px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                <i class="fas fa-edit" style="font-size: 18px; color: #333;"></i>
            </a>
        </div>

        <hr>
        <p><span class="profile-text">Nama:</span> <?php echo htmlspecialchars($user['nama']); ?></p>
        <p><span class="profile-text">Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><span class="profile-text">Username:</span> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><span class="profile-text">No. HP:</span> <?php echo htmlspecialchars($user['nohp']); ?></p>
        <p><span class="profile-text">Alamat:</span> <?php echo htmlspecialchars($user['alamat']); ?></p>

        <div class="btn-wrapper">
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
