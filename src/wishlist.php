<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
if ($isLoggedIn) {
    // Establish connection to SQLite database
    try {
        $db = new PDO('sqlite:../db/trintent.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        die();
    }

    // Fetch wishlist items
    $username = $_SESSION['username'];
    $stmt = $db->prepare("SELECT item.*, wishlist.added_at FROM wishlist JOIN item ON wishlist.item_id = item.item_id WHERE wishlist.username = ?");
    $stmt->execute([$username]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
    <div id="wishlist-div" >   
    <h1>Wishlist :</h1>

        <?php if ($isLoggedIn && !empty($wishlistItems)) : ?>
            <div class="wishlist-container">
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="wishlist-item" data-id="<?php echo $item['item_id']; ?>">
                        <a href="product_page.php?item_id=<?php echo $item['item_id']; ?>">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                            <p><?php echo ucfirst($item['name'])." : ".$item['price']."€"; ?></p>
                        </a>
                        <button class="remove-button">Remove</button> <!-- Add remove button here -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>Your wishlist is empty. <a href="search_products.php">Browse</a> our store to add items.</p>
        <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="project.js" defer></script> <!-- Include your JS file -->
</body>
</html>
