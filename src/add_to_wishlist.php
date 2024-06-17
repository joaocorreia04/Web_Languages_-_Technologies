<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'No item ID provided']);
    exit;
}

$username = $_SESSION['username'];
$item_id = $data['item_id'];

try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if item is already in wishlist
    $stmt = $db->prepare("SELECT * FROM wishlist WHERE username = ? AND item_id = ?");
    $stmt->execute([$username, $item_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Item is already in wishlist']);
        exit;
    }
    
    // Add item to wishlist
    $stmt = $db->prepare("INSERT INTO wishlist (username, item_id, added_at) VALUES (?, ?, datetime('now'))");
    $stmt->execute([$username, $item_id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
