<?php
    include("koneksi.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Login Absensi Digital" />
    <meta name="author" content="Absensi Passketsu" />
    <title>Absensi Passketsu Digital</title>
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
<?php

if (isset($_SESSION['reset_success'])) {
    echo "<script>alert('" . $_SESSION['reset_success'] . "');</script>";
    unset($_SESSION['reset_success']);
}
?>

<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-lg border-0 rounded-lg mt-5 fade-in">
                            <div class="card-header text-center">
                                <img src="assets/img/logopas.png" alt="Library Logo" width="80" class="mb-2">
                                <h3 class="font-weight-light my-3">Absensi Passketsu Digital</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($_POST['login'])) {
                                    $email = $_POST['email'];
                                    $password = md5($_POST['password']);

                                    // Query memakai tabel pengguna
                                    $query = "SELECT * FROM pengguna WHERE email='$email' AND password='$password'";
                                    $result = mysqli_query($koneksi, $query);
                                    $user = mysqli_fetch_assoc($result);

                                    if ($user) {
                                        if ($user['status'] === 'blokir') {
                                            echo '<div class="alert alert-danger text-center">Anda telah diblokir karena terlambat mengembalikan buku!</div>';
                                        } else {
                                            $_SESSION['user'] = $user;
                                            echo '<script>alert("Selamat datang, ' . htmlspecialchars($user['username']) . '!"); location.href="index.php";</script>';
                                            exit();
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger text-center">Email atau Password salah!</div>';
                                    }
                                }
                                ?>
                                <form method="post">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" type="email" name="email" placeholder="Email" required />
                                        <label>Email</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" type="password" name="password" placeholder="Password" required />
                                        <label>Password</label>
                                    </div>
                                    <button class="btn btn-primary w-100" type="submit" name="login">Login</button>
                                </form>
                                <div class="text-center mt-3">
                                    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
                                </div>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small">
                                    Bantuan? 082143859183 (Contact Admin)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
</body>
</html>
