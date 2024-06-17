<?php
// Start the session
session_start();
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php'); // Redirect to login page if not an admin
    exit();
}

// Establish connection to SQLite database
try {
    $db = new PDO('sqlite:../db/trintent.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON;'); // Enable foreign key support

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

// Fetch the list of all users
$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <section id="manage-users">
            <div class="input-group">
            <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucfirst($user['username'])); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number'] ?? '---------'); ?></td>
                            <td>
                                <?php if ($user['is_admin'] == 1): ?>
                                    <button onclick="setAdmin(0,'<?php echo $user['username']; ?>')" class="edit-button black-button">Demote</button>
                                <?php else: ?>
                                    <button onclick="setAdmin(1,'<?php echo $user['username']; ?>')" class="edit-button green-button">Promote</button>
                                <?php endif; ?>
                                <button onclick="deleteUser('<?php echo $user['username']; ?>')" class="red-button">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                                </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Send an AJAX request to delete the user
                fetch('delete_user.php?' + new URLSearchParams({ id: userId }),
                    {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: userId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User deleted successfully.');
                            location.reload();
                        } else {
                            alert('Failed to delete user.');
                        }
                    })
                    .catch(err => {
                        alert('Failed to delete user.');
                    });
            }
        }
        function setAdmin(adminValue, username) {
            if (confirm('Are you sure you want to promote this user to admin?')) {
                // Send an AJAX request to promote the user
                fetch('set_admin.php',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ is_admin: adminValue, username: username })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User promoted to admin successfully.');
                            location.reload();
                        } else {
                            alert('Failed to promote user to admin.');
                        }
                    })
                    .catch(err => {
                        alert('Failed to promote user to admin.');
                    });
            }

        }
    </script>
</body>

</html>