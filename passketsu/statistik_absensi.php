<?php
// Pastikan session sudah start dan koneksi sudah tersedia
$id_user = mysqli_real_escape_string($koneksi, $_SESSION['user']['id_user']);
$level = $_SESSION['user']['level'] ?? 'anggota'; // default anggota
$currentMonth = date('Y-m'); // Tahun-Bulan, misal 2025-07

$kategori = ['Hadir', 'Izin', 'Sakit', 'Alfa'];
$statistik = [];

foreach ($kategori as $ket) {
    if ($level === 'admin') {
        // Admin lihat semua data tanpa filter id_user
        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE keterangan='$ket' AND DATE_FORMAT(tanggal, '%Y-%m') = '$currentMonth'");
    } else {
        // Anggota lihat data sendiri
        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE id_user='$id_user' AND keterangan='$ket' AND DATE_FORMAT(tanggal, '%Y-%m') = '$currentMonth'");
    }
    $data = mysqli_fetch_assoc($query);
    $statistik[$ket] = (int)($data['total'] ?? 0);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Statistik Absensi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            max-width: 700px;
            margin: 40px auto;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
        }
        canvas {
            background: #f7f7f7;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Statistik Absensi Bulan <?= date('F Y'); ?></h2>
    <canvas id="absensiChart" width="600" height="400"></canvas>
</div>

<script>
    const ctx = document.getElementById('absensiChart').getContext('2d');

    const data = {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
        datasets: [{
            label: 'Jumlah Kehadiran',
            data: [
                <?= $statistik['Hadir'] ?>,
                <?= $statistik['Izin'] ?>,
                <?= $statistik['Sakit'] ?>,
                <?= $statistik['Alfa'] ?>
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.7)',   // Hijau - Hadir
                'rgba(23, 162, 184, 0.7)',  // Biru - Izin
                'rgba(255, 193, 7, 0.7)',   // Kuning - Sakit
                'rgba(220, 53, 69, 0.7)'    // Merah - Alfa
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1,
            borderRadius: 6,
            maxBarThickness: 60
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1,
                    title: {
                        display: true,
                        text: 'Jumlah Hari'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Kategori Absensi'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    };

    const absensiChart = new Chart(ctx, config);
</script>
                <a href="javascript:history.back()" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
</body>
</html>
