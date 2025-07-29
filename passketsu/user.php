<h1 class="mt-4">
    <i class="fas fa-users"></i> Data Pengguna
</h1>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        // Ambil data dari tabel pengguna
                        $query = mysqli_query($koneksi, "SELECT * FROM pengguna");
                        while ($data = mysqli_fetch_array($query)) {
                            $id_user = $data['id_user'];
                            $status = $data['status'];
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                <td><?php echo htmlspecialchars($data['email']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($data['level'])); ?></td>
                                <td>
                                    <?php if ($status == 'aktif') { ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Diblokir</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="?page=user_detail&id=<?php echo $data['id_user']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <!-- Hanya admin yang bisa blokir/buka blokir -->
                                    <?php if ($_SESSION['user']['level'] == 'admin') { ?>
                                        <?php if ($status == 'aktif') { ?>
                                            <a href="blokir.php?id=<?php echo $data['id_user']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Apakah Anda yakin ingin memblokir pengguna ini?')">
                                               <i class="fas fa-ban"></i> Blokir
                                            </a>
                                        <?php } else { ?>
                                            <a href="buka_blokir.php?id=<?php echo $data['id_user']; ?>" 
                                               class="btn btn-success btn-sm" 
                                               onclick="return confirm('Apakah Anda yakin ingin membuka blokir pengguna ini?')">
                                               <i class="fas fa-unlock"></i> Buka Blokir
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
