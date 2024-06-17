<?php
// Start the session
session_start();
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="faq-section">
            <h1>Frequently Asked Questions</h1>
            
            <div class="faq-item">
                <h2>What is TRINTED?</h2>
                <p>TRINTED is a platform for buying and selling pre-loved items. Our goal is to give a new life to second-hand treasures and promote sustainable fashion.</p>
            </div>

            <div class="faq-item">
                <h2>How do I sell an item?</h2>
                <p>To sell an item, simply create an account, click on the 'Sell' button, and follow the prompts to list your item. You will need to provide a description, price, and upload photos of the item.</p>
            </div>

            <div class="faq-item">
                <h2>How do I buy an item?</h2>
                <p>Browse through our categories by clicking the browse tab, to find items you are interested in. Once you find something you like, click on the item to view details and proceed to purchase.</p>
            </div>

            <div class="faq-item">
                <h2>What payment methods are accepted?</h2>
                <p>We accept various payment methods including credit/debit cards and PayPal. All transactions are secure and encrypted.</p>
            </div>

            <div class="faq-item">
                <h2>How do I contact customer support?</h2>
                <p>If you have any questions or need assistance, you can contact our support team at support@trinted.com. We are here to help you.</p>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
