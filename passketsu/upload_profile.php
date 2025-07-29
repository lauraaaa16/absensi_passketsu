<?php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$response = null;

// Ambil data pengguna dari tabel `pengguna`
$query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updateFields = [];

    function validate_input($input) {
        global $koneksi;
        return mysqli_real_escape_string($koneksi, trim($input));
    }

    // Validasi nama
    if (!empty($_POST['nama'])) {
        $nama = validate_input($_POST['nama']);
        $updateFields[] = "nama='$nama'";
    }

    // Validasi username unik
    if (!empty($_POST['username'])) {
        $username = validate_input($_POST['username']);
        $checkUsername = mysqli_query($koneksi, "SELECT id_user FROM pengguna WHERE username='$username' AND id_user != '$id_user'");
        if (mysqli_num_rows($checkUsername) > 0) {
            $response = ["success" => false, "message" => "Username sudah digunakan!"];
        } else {
            $updateFields[] = "username='$username'";
        }
    }

    // Validasi password minimal 8 karakter
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            $response = ["success" => false, "message" => "Password minimal 8 karakter!"];
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $updateFields[] = "password='$password'";
        }
    }

    // Validasi email harus @gmail.com
    if (!empty($_POST['email'])) {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !str_ends_with($_POST['email'], '@gmail.com')) {
            $response = ["success" => false, "message" => "Email harus menggunakan format @gmail.com!"];
        } else {
            $email = validate_input($_POST['email']);
            $updateFields[] = "email='$email'";
        }
    }

    // Validasi nomor HP
    if (!empty($_POST['nohp'])) {
        if (!preg_match('/^\d{10,15}$/', $_POST['nohp'])) {
            $response = ["success" => false, "message" => "No HP hanya bisa 10-15 karakter!"];
        } else {
            $nohp = validate_input($_POST['nohp']);
            $updateFields[] = "nohp='$nohp'";
        }
    }

    // Validasi alamat
    if (!empty($_POST['alamat'])) {
        $alamat = validate_input($_POST['alamat']);
        $updateFields[] = "alamat='$alamat'";
    }

    // Update data pengguna
    if ($response === null && !empty($updateFields)) {
        $updateQuery = "UPDATE pengguna SET " . implode(", ", $updateFields) . " WHERE id_user='$id_user'";
        if (mysqli_query($koneksi, $updateQuery)) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $_POST);
            $response = ["success" => true, "message" => "Data berhasil diperbarui!"];
        } else {
            $response = ["success" => false, "message" => "Gagal memperbarui data pengguna."];
        }
    }

    // Upload foto profil
    if (!empty($_FILES['profile_photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["profile_photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFilePath)) {
                $updateQuery = "UPDATE pengguna SET profile_photo='$targetFilePath' WHERE id_user='$id_user'";
                if (mysqli_query($koneksi, $updateQuery)) {
                    $_SESSION['photo_profile'] = $targetFilePath;
                    $response = ["success" => true, "message" => "Foto berhasil diunggah!"];
                } else {
                    $response = ["success" => false, "message" => "Gagal menyimpan foto ke database."];
                }
            } else {
                $response = ["success" => false, "message" => "Gagal mengunggah foto!"];
            }
        } else {
            $response = ["success" => false, "message" => "Format file tidak didukung!"];
        }
    } elseif (!empty($_POST['selected_photo'])) {
        $selectedPhoto = $_POST['selected_photo'];
        $updateQuery = "UPDATE pengguna SET profile_photo='$selectedPhoto' WHERE id_user='$id_user'";
        if (mysqli_query($koneksi, $updateQuery)) {
            $_SESSION['photo_profile'] = $selectedPhoto;
            $response = ["success" => true, "message" => "Foto berhasil diperbarui!"];
        } else {
            $response = ["success" => false, "message" => "Gagal memperbarui foto di database."];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Pengguna</title>
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
            background: white;
            padding: 30px;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            text-align: center;
            color: #9a9178;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .radio-buttons img {
            width: 50px;
            border-radius: 50%;
            margin: 5px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .radio-buttons input[type="radio"]:checked + img {
            transform: scale(1.2);
            border: 2px solid #9a9178;
        }
        .text-center { text-align: center; }
        .btn { 
            background: #9a9178; 
            color: white; 
            padding: 10px 20px; 
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-secondary { background: gray; }
    </style>
</head>
<body>

<div class="container">
<?php if ($response): ?>
    <div class="message <?= $response['success'] ? 'success' : 'error'; ?>">
        <?= $response['message']; ?>
    </div>
    <script>
        <?php if ($response['success']): ?>
            setTimeout(function() {
                window.location.href = '?page=akun'; 
            }, 500);
        <?php endif; ?>
    </script>
<?php endif; ?>

    <h2>Edit Profil Pengguna</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Nama:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" required>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <label>No HP:</label>
        <input type="text" name="nohp" value="<?= htmlspecialchars($user['nohp']); ?>" required>

        <label>Alamat:</label>
        <input type="text" name="alamat" value="<?= htmlspecialchars($user['alamat']); ?>" required>

        <h3>Ubah Foto Profil</h3>
        <div class="radio-buttons">
            <?php 
            $images = ["1.png", "admin.png", "3.png", "4.png", "5.png", "6.png"];
            foreach ($images as $img) {
                $imgPath = "assets/img/" . $img;
                echo '<label><input type="radio" name="selected_photo" value="'.$imgPath.'" ' . ($user['profile_photo'] == $imgPath ? 'checked' : '') . '> <img src="'.$imgPath.'"></label>';
            }
            ?>
        </div>
        <p>Atau upload foto sendiri:</p>
        <input type="file" name="profile_photo">

        <div class="text-center mt-3">
            <button type="submit" class="btn">Simpan</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
