<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$product = null;

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

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
} else {
    $product_id = null;

}
if (!empty($product_id)) {
    // Fetch a specific product
    $stmt = $db->prepare("SELECT * FROM item WHERE item_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $condition = $_POST['condition'];
    $subcategory = $_POST['subcategory'];
    $size = $_POST['size'];

    // Image upload + getting path url
    $fileName = uniqid() . '_' . $_FILES["image"]["name"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png'];
    $imageExtension = explode('.', $fileName);
    $imageExtension = strtolower(end($imageExtension));
    $imageUrl = "";
    if (!in_array($imageExtension, $validImageExtension)) {
        echo "not valid image extension";
    } else {
        $newImageName = uniqid();
        $newImageName .= '.' . $imageExtension;
        move_uploaded_file($tmpName, '../uploads/' . $newImageName);
        $imageUrl = "../uploads/" . $newImageName;
    }

    // Insert data into the database
    $stmt = $db->prepare("UPDATE item SET `name` = ?, price = ?, `description` = ?, category = ?, condition = ?, sub_category = ?, `size` = ?, image_url = ? WHERE item_id = ?");
    var_dump("kkkk", $product);
    if (!$stmt->execute([$name, $price, $description, $category, $condition, $subcategory, $size, $imageUrl, $product['item_id']])) {
        // Print PDO error message
        print_r($stmt->errorInfo());
    }
    header("Location: profile_page.php");
    return;
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

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?product_id='. $product['item_id']; ?>" method="post" enctype="multipart/form-data">

        <div class="input-group">
            <label for="name">Item name:</label>
            <input type="text" id="name" name="name" value="<?php echo $product['name'] ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" min="0.01" step="0.01" value="<?php echo $product['price'] ?>"
                required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" cols="50"
                required><?php echo $product['description'] ?></textarea>

            <div class="input-group">
                <select id="category" name="category" onchange="updateSubcategoriesAndSizes()" required>
                    <option value selected="">Category</option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                    <option value="Children">Children</option>
                </select>
            </div>
            <div class="input-group">
                <select id="condition" name="condition">
                    <option value selected="">Condition</option>
                    <option value="Great">Great</option>
                    <option value="Good">Good</option>
                    <option value="Bad">Bad</option>
                    <option value="Excellent">Excellent</option>
                </select>
            </div>
            <div class="input-group" id="subcategory-group">
                <select id="subcategory" name="subcategory">
                    <option value="" selected>SubCategory</option>
                    <!-- Subcategory options will be dynamically updated -->
                </select>
            </div>

            <div class="input-group" id="size-group">
                <select id="size" name="size">
                    <option value="" selected>Size</option>
                    <!-- Size options will be dynamically updated -->
                </select>
            </div>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">

            <input type="submit" value="Save Changes">

        </div>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            header("Location: profile_page.php");
            exit;
        }
        ?>
    </form>

    <script src="project.js"></script>
    <script>
        populate(<?php echo json_encode($product) ?>);
    </script>
    <?php include 'footer.php'; ?>
</body>

</html>