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
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Handle message insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) {
        echo json_encode(['success' => false, 'error' => 'User not logged in']);
        exit;
    }

    $from_id = $_SESSION['username'];
    $to_id = $_POST['receiver'];
    $content = $_POST['message'];
    $date = time();

    $stmt = $db->prepare("INSERT INTO message (from_id, to_id, content, date) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$from_id, $to_id, $content, date('Y-m-d H:i:s', $date)])) {
        echo json_encode([
            'success' => true,
            'message' => [
                'from_id' => $from_id,
                'to_id' => $to_id,
                'content' => $content,
                'date' => date('Y-m-d H:i:s', $date)
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
    }
    exit;
}

// Fetch messages for display
if ($isLoggedIn) {
    $username = $_SESSION['username'];
    $stmt = $db->prepare("
        SELECT from_id, to_id, content, date 
        FROM message 
        WHERE from_id = ? OR to_id = ?
        ORDER BY date ASC
    ");
    $stmt->execute([$username, $username]);
    $allMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $conversations = array();
    foreach ($allMessages as $message) {
        $otherUser = $message['from_id'] === $username ? $message['to_id'] : $message['from_id'];
        if (!isset($conversations[$otherUser])) {
            $conversations[$otherUser] = array();
        }
        $conversations[$otherUser][] = $message;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TRINTED</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <section id="messages">
            <h2>Messages</h2>
            <div class="message-container">
                <?php foreach ($conversations as $otherUser => $conversation): ?>
                    <div class="chat-box" data-user="<?php echo htmlspecialchars($otherUser); ?>">
                        <h3>Conversation with <?php echo htmlspecialchars($otherUser); ?></h3>
                        <div class="person-messages">
                            <?php foreach ($conversation as $message): ?>
                                <div class="message">
                                    <p><strong>From: </strong><?php echo htmlspecialchars($message['from_id']); ?></p>
                                    <p><strong>Message: </strong><?php echo htmlspecialchars($message['content']); ?></p>
                                    <p class="date"><strong>Date: </strong><?php echo date('Y-m-d H:i:s', strtotime($message['date'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="send-message">
                            <h3>Send a message to <?php echo htmlspecialchars($otherUser); ?></h3>
                            <input id="message-<?php echo htmlspecialchars($otherUser); ?>" type="text" placeholder="Enter your message here">
                            <button class="green-button" onclick="sendMessageCreateRequest('<?php echo htmlspecialchars($otherUser); ?>', true)">SEND</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script src="project.js" defer></script>
</body>

</html>
