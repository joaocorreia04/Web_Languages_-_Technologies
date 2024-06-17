<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    die();
}

// Fetch user information
if ($isLoggedIn) {
    $username = $_SESSION['username'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Parse the query string to get the item ID
    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);
    $itemId = $queries['id'];

    // Fetch the item to check ownership and get the image path
    $stmt = $db->prepare("SELECT * FROM item WHERE item_id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found or not owned by user']);
        die();
    }

    // Delete the image file
    $imagePath = $item['image_url']; // The path is already correct since it is saved in the database as '../uploads/<filename>'

    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            // Image successfully deleted
            $imageDeleted = true;
        } else {
            // Failed to delete image
            $imageDeleted = false;
        }
    } else {
        // Image file does not exist
        $imageDeleted = false;
    }


        // Admin can delete any item
        $stmt = $db->prepare('DELETE FROM item WHERE item_id = ?');
        $stmt->execute([$itemId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'item_id' => $itemId, 'image_deleted' => $imageDeleted]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete item from database', 'image_deleted' => $imageDeleted]);
        }
    }




?>
