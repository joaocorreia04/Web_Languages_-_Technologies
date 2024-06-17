<?php
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
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        die();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location:login.php");
        echo "Registration successful!";
        
    } else {
        echo "Error occurred while registering.";
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pre-Loved Items Marketplace</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
        

    <main>
        <div id="register_div">
            <h1>Trinted</h1>
        <section id="register-form">
            <h2>Who are you?</h2>
            <form action="register.php" method="post">
    
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <div class="input-group">
                    <button class="black-button"type="submit">Register</button>
                </div>
            </form>
            <div class="input-group">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </section>
    </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
