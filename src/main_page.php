<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRINTED</title>
    <link rel="stylesheet" href="styles.css"> <!-- link to the CSS file -->
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="main-shopnow">
                <img src="../uploads/mainpage.jpeg" alt=Welcome>    
                <div class="text-overlay">
                    <p>Give a New life to your Pre Loved items</p>
                    <p>Join us and list your items today!</p>
                </div>
        </div>

        <div class="main-categories">
            <p>Categories</p>
            <div class="category-links">
                <a href="search_products.php?category=Women">
                    <img src="../uploads/women.jpg" alt="Women">
                    <span>Women</span>
                </a>
                <a href="search_products.php?category=Men">
                    <img src="../uploads/men.jpg" alt="Men">
                    <span>Men</span>
                </a>
                <a href="search_products.php?category=Children">
                    <img src="../uploads/children.jpg" alt="Kids">
                    <span>Kids</span>
                </a>
            </div>
           
        </div>
    </main>
   
</body>
<?php include 'footer.php'; ?>
</html>

