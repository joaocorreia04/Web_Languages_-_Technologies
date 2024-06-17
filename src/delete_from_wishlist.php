<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    die();
}

$username = $_SESSION['username'];

// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    die();
}

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $itemId = $queries['id'] ?? null;

    if ($itemId === null) {
        echo json_encode(['success' => false, 'message' => 'No item ID provided']);
        die();
    }

    // Fetch the wishlist item to check ownership
    $stmt = $db->prepare("SELECT * FROM wishlist WHERE item_id = ? AND username = ?");
    $stmt->execute([$itemId, $username]);
    $wishlistItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wishlistItem) {
        echo json_encode(['success' => false, 'message' => 'Wishlist item not found or not owned by user']);
        die();
    }

    // Delete the wishlist item from the database
    $stmt = $db->prepare('DELETE FROM wishlist WHERE item_id = ? AND username = ?');
    $stmt->execute([$itemId, $username]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'item_id' => $itemId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete wishlist item from database']);
    }
}
?>
