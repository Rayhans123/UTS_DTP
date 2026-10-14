<?php
require_once 'koneksi.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelapor   = trim($_POST['nama_pelapor']   ?? '');
    $nama_fasilitas = trim($_POST['nama_fasilitas'] ?? '');
    $lokasi         = trim($_POST['lokasi']         ?? '');
    $id_kategori    = (int) ($_POST['id_kategori']  ?? 0);
    $deskripsi      = trim($_POST['deskripsi']      ?? '');
    $prioritas      = trim($_POST['prioritas']      ?? '');

    $stmt = $conn->prepare(
        "INSERT INTO laporan_kerusakan
            (nama_pelapor, nama_fasilitas, lokasi, id_kategori, deskripsi, prioritas, status, tanggal_laporan)
         VALUES (?, ?, ?, ?, ?, ?, 'Menunggu', NOW())"
    );
    $stmt->bind_param('sssiss', $nama_pelapor, $nama_fasilitas, $lokasi, $id_kategori, $deskripsi, $prioritas);
    $stmt->execute();
    $stmt->close();

    header('Location: ' . $_SERVER['PHP_SELF'] . '#daftar-laporan');
    exit;
}


$kategori_list = [];
$res_kat = $conn->query("SELECT id_kategori, nama_kategori FROM kategori_fasilitas ORDER BY nama_kategori ASC");
if ($res_kat) {
    while ($row = $res_kat->fetch_assoc()) $kategori_list[] = $row;
    $res_kat->free();
}


$laporan_list = [];
$res_lap = $conn->query(
    "SELECT l.nama_fasilitas, l.lokasi, l.nama_pelapor,
            l.prioritas, l.status, l.tanggal_laporan, k.nama_kategori
     FROM   laporan_kerusakan l
     LEFT   JOIN kategori_fasilitas k ON l.id_kategori = k.id_kategori
     ORDER  BY l.tanggal_laporan DESC"
);
if ($res_lap) {
    while ($row = $res_lap->fetch_assoc()) $laporan_list[] = $row;
    $res_lap->free();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelaporan Kerusakan Fasilitas Sekolah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Sistem Pelaporan Kerusakan Fasilitas Sekolah</h1>
        <p>Catat dan pantau laporan kerusakan fasilitas sekolah.</p>
        <nav aria-label="Navigasi utama">
            <a href="#form-laporan">Buat Laporan</a>
            <a href="#daftar-laporan">Daftar Laporan</a>
        </nav>
    </div>
</header>

<main>


    <section id="form-laporan">
        <h2>Form Pelaporan Kerusakan</h2>
        <p>Lengkapi data berikut untuk melaporkan fasilitas yang rusak.</p>

        <form action="" method="post">
            <div class="form-grid">

                <div class="form-group">
                    <label for="nama_pelapor">Nama Pelapor <span class="required">*</span></label>
                    <input type="text" id="nama_pelapor" name="nama_pelapor" maxlength="100" required placeholder="Contoh: Budi Santoso">
                </div>

                <div class="form-group">
                    <label for="nama_fasilitas">Nama Fasilitas <span class="required">*</span></label>
                    <input type="text" id="nama_fasilitas" name="nama_fasilitas" maxlength="100" required placeholder="Contoh: Proyektor Kelas 7A">
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi <span class="required">*</span></label>
                    <input type="text" id="lokasi" name="lokasi" maxlength="100" required placeholder="Contoh: Ruang Kelas 7A">
                </div>

                <div class="form-group">
                    <label for="id_kategori">Kategori <span class="required">*</span></label>
                    <select id="id_kategori" name="id_kategori" required>
                        <option value="">-- Pilih kategori --</option>
                        <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?= (int) $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="prioritas">Prioritas <span class="required">*</span></label>
                    <select id="prioritas" name="prioritas" required>
                        <option value="">-- Pilih prioritas --</option>
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="deskripsi">Deskripsi <span class="required">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required placeholder="Jelaskan kerusakan secara singkat..."></textarea>
                </div>

            </div>

            <div class="btn-group">
                <button type="submit" class="btn-primary">Kirim Laporan</button>
                <button type="reset" class="btn-secondary">Reset</button>
            </div>
        </form>
    </section>

    
    <section id="daftar-laporan">
        <h2>Daftar Laporan Kerusakan</h2>
        <p>Seluruh laporan yang tercatat pada database ditampilkan di bagian ini.</p>

        <div class="table-wrapper">
            <?php if (empty($laporan_list)): ?>
                <p class="empty">Belum ada laporan kerusakan yang tercatat.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Fasilitas</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Pelapor</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporan_list as $i => $row): ?>
                            <tr>
                                <td class="no"><?= $i + 1 ?></td>
                                <td class="bold"><?= htmlspecialchars($row['nama_fasilitas']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>
                                <td><?= htmlspecialchars($row['prioritas']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td style="white-space:nowrap"><?= date('d M Y', strtotime($row['tanggal_laporan'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="table-footer">Total <?= count($laporan_list) ?> laporan</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<footer>
    &copy; Sistem Pelaporan Kerusakan Fasilitas Sekolah
</footer>

</body>
</html>
