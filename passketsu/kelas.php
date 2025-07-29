<?php
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $kelas = $_POST['kelas'];

    $sql = "INSERT INTO kelas (nama_kelas) VALUES ('$kelas')";

    if (mysqli_query($koneksi, $sql)) {
        echo "Data kelas berhasil disimpan.";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<form method="post" action="">
    <label>Nama Kelas:</label>
    <input type="text" name="kelas" required>
    <input type="submit" name="submit" value="Simpan">
</form>
