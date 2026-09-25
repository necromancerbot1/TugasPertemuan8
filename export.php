<?php
require_once 'functions.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_inventaris.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['No', 'Nama Produk', 'Kategori', 'Supplier', 'Stok', 'Harga']);

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT p.*, k.nama_kategori, s.nama_supplier 
                    FROM produk p
                    JOIN kategori k ON p.id_kategori = k.id
                    JOIN supplier s ON p.id_supplier = s.id
                    ORDER BY p.id DESC");
$rows = $stmt->fetchAll();

$no = 1;
foreach ($rows as $row) {
    fputcsv($output, [
        $no++,
        $row['nama_produk'],
        $row['nama_kategori'],
        $row['nama_supplier'],
        $row['stok'],
        $row['harga']
    ]);
}

fclose($output);
exit;
?>