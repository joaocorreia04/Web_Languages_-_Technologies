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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission
    // Validate form data
    $username = $_SESSION['username'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $size = $_POST['size'];
    $condition = $_POST['condition'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
//Image upload + getting path url 
    $fileName = uniqid() . '_' . $_FILES["image"]["name"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension=['jpg','jpeg','png'];
    $imageExtension=explode('.',$fileName);
    $imageExtension=strtolower(end($imageExtension));
    $imageUrl = "";
    if(!in_array($imageExtension,$validImageExtension)){
        echo "not valid image extension";
    }else{
        $newImageName=uniqid();
        $newImageName .= '.' . $imageExtension;
        move_uploaded_file($tmpName,'../uploads/'.$newImageName);
        $imageUrl="../uploads/".$newImageName;
    }


    
    // Insert data into the database
    $stmt = $db->prepare("INSERT INTO item (username, name, price, description, size, condition, category, sub_category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt->execute([$username, $name, $price, $description, $size, $condition, $category, $subcategory, $imageUrl])) {
        // Print PDO error message
        print_r($stmt->errorInfo());
    }
    header("Location: profile_page.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Item - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
<div id="sell-div">
    <h1>List your pre-loved items</h1>
    <form id="sell-form"  method="POST" enctype="multipart/form-data">
    <div class="input-group">
        <label for="name">Item name:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="input-group">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" min="0.01" step="0.01" required>
    </div>

    <div class="input-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea>
    </div>
 
    <div class="input-group">
        <select id="category" name="category" onchange="updateSubcategoriesAndSizes()" required>
            <option value selected ="">Category</option>
            <option value="Men">Men</option>
            <option value="Women">Women</option>
            <option value="Children">Children</option>
        </select>
    </div>
    <div class="input-group">
        <select id="condition" name="condition" >
            <option value selected ="">Condition</option>
            <option value="Great">Great</option>
            <option value="Good">Good</option>
            <option value="Bad">Bad</option>
            <option value="Excellent">Excellent</option>
        </select>
    </div>
    <div class="input-group" id="subcategory-group" style="display: none;">
        <select id="subcategory" name="subcategory">
            <option value="" selected>SubCategory</option>
            <!-- Subcategory options will be dynamically updated -->
        </select>
    </div>

    <div class="input-group" id="size-group" style="display: none;">
        <label for="size">Size:</label>
        <select id="size" name="size">
            <!-- Size options will be dynamically updated -->
        </select>
    </div>

    <div class="input-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" >
    </div>
    <div class="input-group">
    <button class="black-button" type="submit">Sell Item</button>
    </div>
    </form>
</div>    
<script src="project.js" defer></script>
<?php include 'footer.php'; ?>
</body>
</html>
