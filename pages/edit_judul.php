<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna login dan merupakan mahasiswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// Ambil ID pengajuan judul yang akan diedit
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $user_id = $_SESSION['user_id'];

    // Ambil data judul yang akan diedit
    $query = "SELECT * FROM judul_pengajuan WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $edit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $judul = $row['judul'];
    } else {
        echo "<p>Judul tidak ditemukan.</p>";
        exit();
    }
} else {
    echo "<p>ID judul tidak ditemukan.</p>";
    exit();
}

// Menangani pembaruan judul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['judul_baru'])) {
    $judul_baru = $_POST['judul_baru'];

    // Update judul di database
    $query = "UPDATE judul_pengajuan SET judul = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $judul_baru, $edit_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<p>Judul berhasil diperbarui.</p>";
        header("Location: mahasiswa_dashboard.php");
        exit();
    } else {
        echo "<p>Gagal memperbarui judul atau tidak ada perubahan.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Judul</title>
</head>
<body>
    <h2>Edit Judul Pengajuan</h2>
    <form action="" method="POST">
        <label for="judul_baru">Judul Baru:</label>
        <input type="text" name="judul_baru" id="judul_baru" value="<?php echo $judul; ?>" required>
        <button type="submit">Update Judul</button>
    </form>
</body>
</html>
