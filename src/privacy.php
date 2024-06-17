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
    <title>Privacy Policy - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="privacy-section">
            <h1>Privacy Policy</h1>

            <h2>1. Information We Collect</h2>
            <p>At TRINTED, we collect personal information such as name, email address, and phone number when you create an account or make a purchase.</p>

            <h2>2. How We Use Your Information</h2>
            <p>We use your information to process transactions, improve our services, and communicate with you about your account and our products.</p>

            <h2>3. Data Security</h2>
            <p>We take data security seriously and implement measures to protect your information from unauthorized access, alteration, disclosure, or destruction.</p>

            <h2>4. Cookies</h2>
            <p>Our website uses cookies to enhance your browsing experience and analyze website traffic. You can adjust your browser settings to disable cookies if you prefer.</p>

            <h2>5. Third-Party Links</h2>
            <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these websites.</p>

            <h2>6. Changes to Privacy Policy</h2>
            <p>We may update our privacy policy from time to time. Any changes will be reflected on this page, and we encourage you to review our policy periodically.</p>

            <h2>7. Contact Us</h2>
            <p>If you have any questions or concerns about our privacy policy, please contact us at privacy@trinted.com.</p>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
