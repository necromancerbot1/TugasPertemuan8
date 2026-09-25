<?php
session_start();
require_once 'functions.php';

$kategori = getKategori();
$supplier = getSupplier();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = htmlspecialchars(trim($_POST['nama_produk']));
    $id_kategori = (int) $_POST['id_kategori'];
    $id_supplier = (int) $_POST['id_supplier'];
    $stok = (int) $_POST['stok'];
    $harga = (float) $_POST['harga'];

    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga) 
            VALUES (:nama, :id_kategori, :id_supplier, :stok, :harga)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':id_kategori', $id_kategori, PDO::PARAM_INT);
    $stmt->bindParam(':id_supplier', $id_supplier, PDO::PARAM_INT);
    $stmt->bindParam(':stok', $stok, PDO::PARAM_INT);
    $stmt->bindParam(':harga', $harga);

    if ($stmt->execute()) {
        setFlash('success', 'Produk berhasil ditambahkan!');
    } else {
        setFlash('danger', 'Gagal menambahkan produk.');
    }
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>➕ Tambah Produk</h1>
        <form method="POST">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Supplier</label>
                <select name="id_supplier" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php foreach ($supplier as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" min="0" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" min="0" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>