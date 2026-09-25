<?php
session_start();
require_once 'functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $db->beginTransaction();

        // Ambil data produk sebelum dihapus untuk log
        $stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produk = $stmt->fetch();

        if ($produk) {
            // Hapus produk
            $stmtDelete = $db->prepare("DELETE FROM produk WHERE id = :id");
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Catat log aktivitas
            $aksi = 'DELETE';
            $tabel = 'produk';
            $detail = "Menghapus produk: " . $produk['nama_produk'];
            
            $stmtLog = $db->prepare("INSERT INTO log_aktivitas (aksi, tabel, data_id, detail) VALUES (:aksi, :tabel, :data_id, :detail)");
            $stmtLog->bindParam(':aksi', $aksi);
            $stmtLog->bindParam(':tabel', $tabel);
            $stmtLog->bindParam(':data_id', $id, PDO::PARAM_INT);
            $stmtLog->bindParam(':detail', $detail);
            $stmtLog->execute();

            $db->commit();
            setFlash('success', 'Produk berhasil dihapus dan dicatat di log.');
        } else {
            $db->rollBack();
            setFlash('danger', 'Produk tidak ditemukan.');
        }
    } catch (Exception $e) {
        $db->rollBack();
        setFlash('danger', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

header("Location: index.php");
exit;
?>