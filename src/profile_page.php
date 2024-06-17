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

// Fetch user information based on the URL parameter or session
if (isset($_GET['username'])) {
    $profileUsername = $_GET['username'];
} elseif ($isLoggedIn) {
    $profileUsername = $_SESSION['username'];
} else {
    echo 'User not specified and not logged in';
    die();
}

$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$profileUsername]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo 'User not found';
    die();
}

// Fetch products listed by the user
$stmt = $db->prepare("SELECT * FROM item WHERE username = ?");
$stmt->execute([$profileUsername]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch wishlist items if viewing own profile
if ($isLoggedIn && $profileUsername === $_SESSION['username']) {
    $stmt = $db->prepare("SELECT item.*, wishlist.added_at FROM wishlist JOIN item ON wishlist.item_id = item.item_id WHERE wishlist.username = ? ORDER BY wishlist.added_at DESC LIMIT 4");
    $stmt->execute([$profileUsername]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section id="user-info">
            <div class="user-div">
                <img class="profile-back-picture" src="<?php echo $user['profile_background_img_url']; ?>" alt="Profile Background Picture">
                <img class="profile-picture" src="<?php echo $user['profile_img_url']; ?>" alt="Profile Picture">
                <div id="user_details"> 
                    <p><strong><?php echo ucfirst($user['username']); ?></strong><br><?php echo $user['email']. str_repeat("&nbsp;", 4)."Phone: ".$user['phone_number']; ?></p>
                    <p><strong><?php  echo htmlspecialchars($seller['seller_rating'] ?? '------'); ?></strong><br>Seller Rating</p>
                    <p><strong><?php echo htmlspecialchars($seller['seller_rating'] ?? '------');?></strong><br>Buyer Rating</p>
                    <?php if ($isLoggedIn && $_SESSION['username'] === $user['username']): ?>
                        <a href="edit_profile.php" class="edit-profile-button">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
       
    <section id="products-listed">
        <h2>Listed Items:</h2>
        <?php if (empty($products)) : ?>
            <?php if ($isLoggedIn && $profileUsername === $user['username']) : ?>
                <p>You can check your listed items here. Right now, you aren't selling any items.</p>
                <p>Sell your old clothes<a href="sell.php"> Here</a></p>
            <?php else : ?>
                <p>This user has no items available.</p>
            <?php endif; ?>
        <?php else : ?>
            <div class="product-container">
                <?php foreach ($products as $product): ?>
                    <div class="product" data-id="<?php echo $product['item_id']; ?>">
                        <a href="product_page.php?item_id=<?php echo $product['item_id']; ?>">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                        <p><?php echo ucfirst($product['name'])." - ".$product['price']."€"; ?></p>
                        </a>
                        <button onclick="location.href='edit_product.php?product_id=<?php echo $product['item_id']; ?>'" class="green-button edit-button">Edit</button>
                        <button class="remove-button">Remove</button>
                    <!-- Display other product details as needed -->
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($isLoggedIn && ($profileUsername === $_SESSION['username'])) : ?>

        <section id="wishlist">
        <p><h2>My Wishlist : </h2> You can check here your full <a href="wishlist.php">Wishlist</a>.</p>
        <?php if (empty($wishlistItems)) : ?>
            <p><a href="search_products.php">Browse</a> our store  to add items you're interested in, so you can later check them here.</p>
        <?php else : ?>
            <div class="wishlist-container">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="wishlist-item" data-id="<?php echo $item['item_id']; ?>">
                    <a href="product_page.php?item_id=<?php echo $item['item_id']; ?>">
                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                    <p><?php echo ucfirst($item['name'])." : ".$item['price']."€"; ?></p>
                    </a>
                    <button class="remove-button">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        </section>
    <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="project.js" defer></script>
</body>
</html>
