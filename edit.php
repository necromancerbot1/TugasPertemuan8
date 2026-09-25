<?php
session_start();
require_once 'functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$produk = getProdukById($id);

if (!$produk) {
    setFlash('danger', 'Produk tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$kategori = getKategori();
$supplier = getSupplier();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = htmlspecialchars(trim($_POST['nama_produk']));
    $id_kategori = (int) $_POST['id_kategori'];
    $id_supplier = (int) $_POST['id_supplier'];
    $stok = (int) $_POST['stok'];
    $harga = (float) $_POST['harga'];

    $db = Database::getInstance()->getConnection();
    $sql = "UPDATE produk SET nama_produk = :nama, id_kategori = :id_kategori, 
            id_supplier = :id_supplier, stok = :stok, harga = :harga 
            WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':id_kategori', $id_kategori, PDO::PARAM_INT);
    $stmt->bindParam(':id_supplier', $id_supplier, PDO::PARAM_INT);
    $stmt->bindParam(':stok', $stok, PDO::PARAM_INT);
    $stmt->bindParam(':harga', $harga);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        setFlash('success', 'Produk berhasil diupdate!');
    } else {
        setFlash('danger', 'Gagal mengupdate produk.');
    }
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Produk</h1>
        <form method="POST">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $k['id'] == $produk['id_kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Supplier</label>
                <select name="id_supplier" required>
                    <?php foreach ($supplier as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $produk['id_supplier'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama_supplier']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= htmlspecialchars($produk['stok']) ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?= htmlspecialchars($produk['harga']) ?>" min="0" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>