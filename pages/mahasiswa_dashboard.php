<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna login dan merupakan mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// Ambil ID pengguna yang login
$user_id = $_SESSION['user_id'];

// Menangani hapus judul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Hapus judul dari tabel
    $query = "DELETE FROM judul_pengajuan WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $delete_id, $user_id);

    if ($stmt->execute()) {
        echo "<p>Judul berhasil dihapus.</p>";
    } else {
        echo "<p>Gagal menghapus judul.</p>";
    }
    // Refresh halaman untuk melihat perubahan
    header("Location: mahasiswa_dashboard.php");
    exit();
}

// Mendapatkan pengajuan judul mahasiswa yang login
$query = "SELECT * FROM judul_pengajuan WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
</head>
<body>
    <h2>Dashboard Mahasiswa</h2>
    <form action="logout.php" method="POST" style="text-align: right;">
        <input type="submit" value="Logout">
    </form>
    <div class="actions">
        <form action="pengajuan_judul.php" method="get">
            <input type="submit" value="Pengajuan Judul">
        </form> <br>
        <form action="hasil_seleksi.php" method="get">
            <input type="submit" value="Lihat Hasil Seleksi Judul">
        </form> <br>
        <form action="pembayaran.php" method="get">
            <input type="submit" value="Pembayaran">
        </form>
    </div>

    <h3>Daftar Pengajuan Judul</h3>
    
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>";
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['judul']}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <!-- Tombol Edit yang mengarahkan ke halaman edit_judul.php -->
                        <a href='edit_judul.php?edit_id={$row['id']}'>Edit</a> |
                        <!-- Form Hapus Judul -->
                        <form action='' method='POST' style='display:inline;'>
                            <input type='hidden' name='delete_id' value='{$row['id']}'>
                            <button type='submit' name='delete'>Hapus</button>
                        </form>
                    </td>
                </tr>";
            $no++;
        }
        echo "</table>";
    } else {
        echo "<p>Belum ada pengajuan judul.</p>";
    }
    ?>

</body>
</html>
