<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

$category = $_GET['category'] ?? null;

if ($category !== null) {
    // Fetch products in the specified category
    $stmt = $db->prepare("SELECT * FROM item WHERE category = ?");
    $stmt->execute([$category]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all products
    $stmt = $db->prepare("SELECT * FROM item");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>


    <div id="filtersgrid">
        <div id="filters">
            <h2>Search Products </h1>
                <label for="categorySelect">Category:</label>
                <select id="category" name="category" onchange="updateSubcategoriesAndSizes(true)">
                    <option value="" selected></option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                    <option value="Children">Children</option>
                </select>

                
                <div class="input-group" id="subcategory-group" style="display: none;">
                    <label for="subcategory">Sub Category:</label>
                    <select id="subcategory" name="subcategory">
                        <option value="" selected>SubCategory</option>
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>

                <label for="priceSelect">Price Range:</label>
                <select name="Price Range" id="priceSelect">
                    <option value="" selected></option>
                    <option value="0-10">&lt;10 EUR</option>
                    <option value="10-25">&gt;10 &amp; &lt;25 EUR</option>
                    <option value="25-50">&gt;25 &amp; &lt;50 EUR</option>
                    <option value="50-100">&gt;50 &amp; &lt;100 EUR</option>
                    <option value="100-250">&gt;100 &amp; &lt;250 EUR</option>
                    <option value="250-1000000">&gt;250 EUR</option>
                </select>
                
                <div class="input-group" id="size-group" style="display: none;">
                    <label for="size">Size:</label>
                    <select id="size" name="size">
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <button class="black-button" id="applyFiltersBtn">Apply Filters</button>
        </div>

        <div id="content">
            <?php foreach ($products as $product): ?>
                <div class="product" data-id="<?php echo $product['item_id']; ?>">
                    <a href="product_page.php?item_id=<?php echo $product['item_id']; ?>">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                        <p><strong><?php echo ucfirst($product['name'])?></strong></p>
                        <p>Price - <?php echo$product['price']."€"?></p>
                    </a>
                    <?php if ($isAdmin): ?>
                            <button class="remove-button">Delete</button>
                        <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="project.js" defer></script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
            updateSubcategoriesAndSizes(true);
        });
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    </script>

</body>

</html>