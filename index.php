<?php
$koneksi = new mysqli("localhost", "root", "", "web_andik");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Tambah kegiatan
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kegiatan']);
    $waktu = mysqli_real_escape_string($koneksi, $_POST['waktu_kegiatan']);
    $bukti = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $error = $_FILES['bukti']['error'];
    $ext = strtolower(pathinfo($bukti, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($ext, $allowed)) {
        if ($error === 0) {
            $uniqueName = time() . '_' . $bukti;
            $tujuan = in_array($ext, ['jpg', 'jpeg', 'png']) ? "foto/" : "sertifikat/";
            move_uploaded_file($tmp, $tujuan . $uniqueName);
            mysqli_query($koneksi, "INSERT INTO portofolio VALUES('', '$nama', '$waktu', '$uniqueName')");
            header("Location: index.php");
            exit;
        } else {
            $error_msg = "Terjadi kesalahan saat upload file.";
        }
    } else {
        $error_msg = "Ekstensi file tidak diizinkan.";
    }
}

// Edit kegiatan
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_kegiatan'];
    $waktu = $_POST['waktu_kegiatan'];

    if ($_FILES['bukti']['name'] != '') {
        $bukti = $_FILES['bukti']['name'];
        $tmp = $_FILES['bukti']['tmp_name'];
        $ext = strtolower(pathinfo($bukti, PATHINFO_EXTENSION));
        $uniqueName = time() . '_' . $bukti;
        $tujuan = in_array($ext, ['jpg', 'jpeg', 'png']) ? "foto/" : "sertifikat/";
        move_uploaded_file($tmp, $tujuan . $uniqueName);
        mysqli_query($koneksi, "UPDATE portofolio SET nama_kegiatan='$nama', waktu_kegiatan='$waktu', bukti='$uniqueName' WHERE id=$id");
    } else {
        mysqli_query($koneksi, "UPDATE portofolio SET nama_kegiatan='$nama', waktu_kegiatan='$waktu' WHERE id=$id");
    }

    header("Location: index.php");
    exit;
}

// Hapus kegiatan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM portofolio WHERE id=$id");
    header("Location: index.php");
    exit;
}

// Data untuk form edit (jika ada)
$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $data_edit = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM portofolio WHERE id=$id_edit"));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Andik Prasetyo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="menu">
        <ul>
            <li><a href="#">Beranda</a></li>
            <li><a href="#">Tentang Saya</a></li>
            <li><a href="#">Portofolio</a></li>
            <li><a href="#">Lainnya</a>
                <ul class="dropdown">
                    <li><a href="https://www.instagram.com/andik.coyz?igsh=bnBwZ2NjeG9hZDI1">Instagram</a></li>
                    <li><a href="https://www.facebook.com/share/1ARSZzaAA7/">Facebook</a></li>
                    <li><a href="https://www.tiktok.com/@gludak01?_t=ZS-8vkI4E1Bowx&_r=1">Tiktok</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<main>
    <img src="foto/WhatsApp Image 2025-04-23 at 20.20.22.jpeg">
    <h3>Hallo, Perkenalkan nama saya Andik Prasetyo.<br>Saya adalah Roamer masa depan RRQ</h3>
</main>

<section class="bio">
    <h1>Tentang Saya</h1>
    <div>
        <p>Saya adalah seorang mahasiswa Teknik Informatika di UNUGIRI BOJONEGORO. Saya lulusan SMK Sunan Drajat Lamongan Angkatan 2024</p>
        <img src="foto/WhatsApp Image 2025-04-21 at 15.55.15.jpeg">
    </div>
</section>

<section class="pri">
    <h1>Portofolio</h1>

    <?php if ($data_edit): ?>
    <h2>Edit Kegiatan</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
        Nama Kegiatan: <input type="text" name="nama_kegiatan" value="<?= $data_edit['nama_kegiatan'] ?>"><br><br>
        Waktu Kegiatan: <input type="text" name="waktu_kegiatan" value="<?= $data_edit['waktu_kegiatan'] ?>"><br><br>
        Ganti Bukti: <input type="file" name="bukti"><br><br>
        <button type="submit" name="edit">Update</button>
    </form>
    <?php else: ?>
    <h2>Tambah Kegiatan</h2>
    <form method="POST" enctype="multipart/form-data">
        Nama Kegiatan: <input type="text" name="nama_kegiatan" required><br><br>
        Waktu Kegiatan: <input type="text" name="waktu_kegiatan" required><br><br>
        Bukti Kegiatan: <input type="file" name="bukti" required><br><br>
        <button type="submit" name="tambah">Simpan</button>
    </form>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kegiatan</th>
                <th>Waktu Kegiatan</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($koneksi, "SELECT * FROM portofolio");
        while ($data = mysqli_fetch_array($query)) {
            $ext = strtolower(pathinfo($data['bukti'], PATHINFO_EXTENSION));
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $data['nama_kegiatan'] ?></td>
            <td><?= $data['waktu_kegiatan'] ?></td>
            <td>
                <?php if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                    <img src="foto/<?= $data['bukti'] ?>" width="100">
                <?php elseif ($ext == 'pdf'): ?>
                    <a href="sertifikat/<?= $data['bukti'] ?>" target="_blank">Lihat Sertifikat</a>
                <?php else: ?>
                    File tidak dikenali
                <?php endif; ?>
            </td>
            <td>
                <a href="?edit=<?= $data['id'] ?>" class="btn edit">Edit</a> |
                <a href="?hapus=<?= $data['id'] ?>" class="btn hapus" onclick="return confirm('Hapus data?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</section>

<section class="msn">
    <h1>OPINI</h1>
    <div class="berita">
        <!-- Berita cards di sini (dipotong untuk singkat) -->
    </div>
</section>

<section class="kontak-container">
    <form class="form-kontak">
        <h2>Hubungi Kami</h2>
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="subject">Subjek:</label>
        <input type="text" id="subject" name="subject" required>

        <label for="pesan">Isi Pesan:</label>
        <textarea id="pesan" name="pesan" rows="5" required></textarea>

        <button type="submit">Kirim</button>
    </form>

    <div class="lokasi">
        <h2>Lokasi Kami</h2>
        <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="300" style="border:0;"></iframe>
    </div>
</section>

<footer>
    <p>&copy; Andik Prasetyo</p>
</footer>
</body>
</html>
