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
    <title>About Us - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="about-section">
            <h1>About Us</h1>

            <p>Welcome to TRINTED, where our journey of web development began as part of a curriculum unit exploring the vast world of creating dynamic websites. Founded by a team of passionate learners, TRINTED embodies our collective dedication to mastering the art of web development and crafting engaging online experiences.</p>

            <p>During our curriculum unit, we embarked on an exciting adventure, diving deep into the realms of PHP, HTML, CSS, AJAX, and JavaScript. Through countless hours of exploration, experimentation, and collaboration, we honed our skills and gained invaluable insights into the intricate workings of modern web technologies.</p>

            <p>Our journey was not just about mastering programming languages; it was about understanding the power of creativity, problem-solving, and innovation in shaping the digital landscape. From designing captivating user interfaces to implementing robust backend functionalities, every step of our journey taught us something new and inspired us to push the boundaries of what's possible.</p>

            <p>At TRINTED, we don't just build websites; we craft experiences that resonate with our users, inspire action, and leave a lasting impression. Whether it's designing elegant user interfaces, optimizing performance for seamless browsing, or leveraging the latest technologies to enhance functionality, we're committed to delivering excellence in everything we do.</p>

            <p>TRINTED's Team:.<br> Miguel Figueiredo: 201706105<br>João Correia: 202005015</p>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
