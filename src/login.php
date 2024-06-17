<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header('Location: main_page.php');
    exit;
}
// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        // You can set more session variables if needed

        // Redirect to main page or any other page after successful login
        header('Location: main_page.php');
        exit;
    } else {
        // Invalid username or password
        echo "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Trinted</title>
    <link rel="stylesheet" href="styles.css"> 
</head>

<body>
    <?php include 'header.php'; ?>

    <div id="login_div">
        <section id="login-form">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button class="black-button" type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </section>
    </div>

<?php include 'footer.php'; ?>
</body>
</html>
