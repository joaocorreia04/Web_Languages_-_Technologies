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
    <title>Return Policy - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="policy-section">
            <h1>Return Policy</h1>
            <p>At TRINTED, we strive to ensure your satisfaction with every purchase. If you are not entirely satisfied with your purchase, we're here to help.</p>

            <h2>Returns</h2>
            <p>You have 7 days to return an item from the date you received it. To be eligible for a return, your item must be in the same condition that you received it. Your item needs to have or proof of purchase.</p>

            <h2>Refunds</h2>
            <p>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item. If your return is approved, we will initiate a refund to your credit card (or original method of payment). You will receive the credit within a certain amount of days, depending on your card issuer's policies.</p>

            <h2>Shipping</h2>
            <p>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are nonrefundable. If you receive a refund, the cost of return shipping will be deducted from your refund.</p>

            <h2>Contact Us</h2>
            <p>If you have any questions on how to return your item to us, contact us at support@trinted.com.</p>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
