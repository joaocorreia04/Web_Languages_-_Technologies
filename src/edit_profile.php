<?php
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

// Fetch user information
if ($isLoggedIn) {
    $username = $_SESSION['username'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission
    // Validate form data
    $newUsername = $_POST['newUsername'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    if (empty($newUsername)) {
        $error = "Username cannot be empty.";
    } elseif (strlen($phoneNumber) !== 9) {
        $error = "Phone number must have exactly 9 characters.";
    } else {
    // Image upload and update URLs
    $profilePictureUrl = "";
    if (!empty($_FILES["profilePicture"]["name"])) {
        $profilePictureName = 'p' . uniqid() . '_' . $_FILES["profilePicture"]["name"];
        $profilePictureTmpName = $_FILES["profilePicture"]["tmp_name"];
        $profilePictureValidExtensions = ['jpg', 'jpeg', 'png'];
        $profilePictureExtension = strtolower(pathinfo($profilePictureName, PATHINFO_EXTENSION));
        
        if (in_array($profilePictureExtension, $profilePictureValidExtensions)) {
            $newProfilePictureName = uniqid() . '.' . $profilePictureExtension;
            move_uploaded_file($profilePictureTmpName, '../uploads/' . $newProfilePictureName);
            $profilePictureUrl = "../uploads/" . $newProfilePictureName;

            // Delete old profile picture if it's not the default one
            if (!empty($user['profile_img_url']) && $user['profile_img_url'] !== '../uploads/profile_default.png') {
                $oldProfileImagePath = "../uploads/" . basename($user['profile_img_url']);
                if (file_exists($oldProfileImagePath)) {
                    unlink($oldProfileImagePath);
                }
            }
        } else {
            echo "Invalid profile picture file format.";
        }
    } else {
        $profilePictureUrl = $user['profile_img_url']; // Keep the old URL
    }

    $backgroundPictureUrl = "";
    if (!empty($_FILES["backgroundPicture"]["name"])) {
        $backgroundPictureName = 'b' . uniqid() . '_' . $_FILES["backgroundPicture"]["name"];
        $backgroundPictureTmpName = $_FILES["backgroundPicture"]["tmp_name"];
        $backgroundPictureValidExtensions = ['jpg', 'jpeg', 'png'];
        $backgroundPictureExtension = strtolower(pathinfo($backgroundPictureName, PATHINFO_EXTENSION));
        
        if (in_array($backgroundPictureExtension, $backgroundPictureValidExtensions)) {
            $newBackgroundPictureName = uniqid() . '.' . $backgroundPictureExtension;
            move_uploaded_file($backgroundPictureTmpName, '../uploads/' . $newBackgroundPictureName);
            $backgroundPictureUrl = "../uploads/" . $newBackgroundPictureName;

            // Delete old background picture if it's not the default one
            if (!empty($user['profile_background_img_url']) && $user['profile_background_img_url'] !== '/uploads/background_default.jpg') {
                $oldBackgroundImagePath = "../uploads/" . basename($user['profile_background_img_url']);
                if (file_exists($oldBackgroundImagePath)) {
                    unlink($oldBackgroundImagePath);
                }
            }
        } else {
            echo "Invalid background picture file format.";
        }
    } else {
        $backgroundPictureUrl = $user['profile_background_img_url']; // Keep the old URL
    }

    // Update user information in the database
   // Update user information in the database
   $stmt = $db->prepare("UPDATE users SET username = ?, phone_number = ?, email = ?, profile_img_url = ?, profile_background_img_url = ? WHERE username = ?");
   if ($stmt->execute([$newUsername, $phoneNumber, $email, $profilePictureUrl, $backgroundPictureUrl, $username])) {
       // Successful update
       // Update the session with the new username
       $_SESSION['username'] = $newUsername;
       header("Location: profile_page.php");
       exit;
   } else {
       // Error occurred
       echo "Error updating user: ";
       print_r($stmt->errorInfo());
   }
}
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <div id="edit-profile">
    <div class="input-group">
    <h1>Edit Profile</h1>
        <label for="newUsername">New Username:</label>
        <input type="text" id="newUsername" name="newUsername" value="<?php echo $user['username']; ?>"><br><br>

        <label for="phoneNumber">Phone Number:</label>
        <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $user['phone_number']; ?>"><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>"><br><br>

        <label for="profilePicture">Profile Picture:</label>
        <input type="file" id="profilePicture" name="profilePicture" accept=".jpg, .jpeg, .png" ><br><br>

        <label for="backgroundPicture">Background Profile Picture:</label>
        <input type="file" id="backgroundPicture" name="backgroundPicture" accept=".jpg, .jpeg, .png" ><br><br>
        
        <input class="black-button" type="submit" value="Save Changes">
        </div>
        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        </div> 
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>
