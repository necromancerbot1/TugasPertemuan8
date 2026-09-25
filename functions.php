<?php
require_once 'config/Database.php';

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function showFlash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . $type . '">' . htmlspecialchars($message) . '</div>';
    }
}

function getAllProduk($search = '', $limit = 5, $offset = 0) {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
            FROM produk p
            JOIN kategori k ON p.id_kategori = k.id
            JOIN supplier s ON p.id_supplier = s.id";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE p.nama_produk LIKE :search OR k.nama_kategori LIKE :search OR s.nama_supplier LIKE :search";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

function countProduk($search = '') {
    $db = Database::getInstance()->getConnection();
    $sql = "SELECT COUNT(*) FROM produk p
            JOIN kategori k ON p.id_kategori = k.id
            JOIN supplier s ON p.id_supplier = s.id";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE p.nama_produk LIKE :search OR k.nama_kategori LIKE :search OR s.nama_supplier LIKE :search";
        $params[':search'] = "%$search%";
    }

    $stmt = $db->prepare($sql);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getKategori() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
    return $stmt->fetchAll();
}

function getSupplier() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");
    return $stmt->fetchAll();
}

function getProdukById($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}
?>