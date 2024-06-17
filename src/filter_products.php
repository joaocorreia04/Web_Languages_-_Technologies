<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);

// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM item";
    $params = array();
    $conditions = array();

    foreach ($_GET as $key => $value) {
        if ($key === 'price' && strpos($value, '-') !== false) {
            // Handle price range
            list($minPrice, $maxPrice) = explode('-', $value);
            $conditions[] = "price BETWEEN ? AND ?";
            $params[] = $minPrice;
            $params[] = $maxPrice;
        } else {
            $conditions[] = "$key = ?";
            $params[] = $value;
        }
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Prepare and execute the SQL query
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
}


function handlePriceRange($value, &$sql, &$params, &$first)
{
    $priceValues = explode('-', $value);
    if (count($priceValues) == 2) {
        if ($first) {
            $sql .= " WHERE";
            $first = false;
        } else {
            $sql .= " AND";
        }
        $sql .= " price > ? AND price < ?";
        $params[] = $priceValues[0];
        $params[] = $priceValues[1];
    }
}
