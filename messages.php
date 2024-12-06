<?php
$query = "SELECT * FROM messages WHERE receiver_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message) {
    echo "<div class='message'>";
    echo "<p><strong>From: " . htmlspecialchars($message['sender_username']) . "</strong></p>";
    echo "<p>" . htmlspecialchars($message['content']) . "</p>";
    echo "</div>";
}
?>
