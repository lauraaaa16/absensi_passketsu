<?php
function auto_tandai_alfa() {
    global $koneksi; // ambil variabel koneksi dari global scope

    $tanggal_hari_ini = date('Y-m-d');
    $hari_ini = date('l');
    $jam_sekarang = date('H:i');

    $jadwal_ekstra = [
        'Wednesday' => ['selesai' => '17:00'],
        'Friday'    => ['selesai' => '17:00'],
    ];

    if (array_key_exists($hari_ini, $jadwal_ekstra) && $jam_sekarang > $jadwal_ekstra[$hari_ini]['selesai']) {
        $sql_alfa = "
            SELECT p.id_user, p.nama 
            FROM pengguna p
            LEFT JOIN absensi a 
                ON p.id_user = a.id_user AND a.tanggal = '$tanggal_hari_ini'
            WHERE a.id_absensi IS NULL AND p.level = 'anggota'
        ";
        $result_alfa = mysqli_query($koneksi, $sql_alfa);

        while ($row = mysqli_fetch_assoc($result_alfa)) {
            $id_user_alfa = $row['id_user'];
            $nama_alfa = $row['nama'];

            $cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_user='$id_user_alfa' AND tanggal='$tanggal_hari_ini' AND keterangan='Alfa'");
            if (mysqli_num_rows($cek) == 0) {
                mysqli_query($koneksi, "
                    INSERT INTO absensi (id_user, nama, tanggal, status, keterangan) 
                    VALUES ('$id_user_alfa', '$nama_alfa', '$tanggal_hari_ini', 'Alfa', 'Tidak mengisi absensi')
                ");
            }
        }
    }
}
?>
