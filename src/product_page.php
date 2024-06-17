<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

// Fetch user information if logged in
if ($isLoggedIn) {
    $username = $_SESSION['username'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch product and seller information
if (isset($_GET['item_id'])) {
    $itemId = $_GET['item_id'];
    $stmt = $db->prepare("SELECT * FROM item WHERE item_id = ?");
    $stmt->execute([$itemId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $sellerStmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $sellerStmt->execute([$product['username']]);
        $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo 'Product not found';
        die();
    }
} else {
    echo 'No product specified';
    die();
}

// Check if the item is in the user's wishlist
$inWishlist = false;
if ($isLoggedIn && isset($itemId)) {
    $stmt = $db->prepare("SELECT * FROM wishlist WHERE username = ? AND item_id = ?");
    $stmt->execute([$username, $itemId]);
    $inWishlist = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
$isSeller = ($isLoggedIn && $product['username'] === $username);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRINTED - Product Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div id="productpage">
        <div id="product">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div id="product-details">
                <p><strong>Product Name: </strong><?php echo htmlspecialchars(ucfirst($product['name'])); ?></p>
                <p><strong>Category:</strong>: <?php echo htmlspecialchars($product['category'] . '/' . $product['sub_category']); ?></p>
                <p><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                <p><strong>Condition: </strong><?php echo htmlspecialchars($product['condition']); ?></p>
                <p><strong>Price: </strong><?php echo htmlspecialchars($product['price']); ?>€</p>
                <h3><strong>Description: </strong></h3>
                <p><?php echo htmlspecialchars(ucfirst($product['description'])); ?></p>
                <div id="product_buttons">
                    <?php if (!$isSeller && $isLoggedIn): ?>
                        <button class="blue-button" onclick="buyItem(<?php echo $itemId; ?>)">Buy Now</button>
                    <?php endif; ?>
                    <?php if ($isLoggedIn && !$isSeller && !$inWishlist): ?>
                        <button class="black-button" onclick="addToWishlist(<?php echo $itemId; ?>)">Add to Wishlist</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="seller">
            <div class="sellerinfo">
                <h3>Seller information:</h3>
                <p><strong>Seller: </strong> <a href="profile_page.php?username=<?php echo htmlspecialchars($product['username']); ?>">
                    <?php echo htmlspecialchars(ucfirst($product['username'])); ?></a></p>
                <p><strong>Email: </strong><?php echo htmlspecialchars($seller['email'] ?? '---'); ?></p>
                <p><strong>Phone: </strong><?php echo htmlspecialchars($seller['phone_number'] ?? '---'); ?></p>    
                <p><strong>Rating: </strong><?php echo htmlspecialchars($seller['seller_rating'] ?? '---'); ?></p>
            </div>
            <div class="sellermessage">
                <h3>Contact the Seller</h3>
                <input id="messageInput" type="text" placeholder="Enter your message here">
                <button class="green-button" onclick="sendMessageCreateRequest('<?php echo htmlspecialchars($product['username']); ?>', false)">SEND</button>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <script src="project.js" defer></script>
</body>
</html>
