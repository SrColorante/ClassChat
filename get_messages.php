<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$stmt = $pdo->prepare("
    SELECT m.message, m.created_at, u.username 
    FROM messages m 
    JOIN users u ON m.user_id = u.id 
    ORDER BY m.created_at DESC 
    LIMIT 50
");
$stmt->execute();
$messages = $stmt->fetchAll();

foreach ($messages as $msg): ?>
    <div class="message">
        <span class="username"><?php echo htmlspecialchars($msg['username']); ?>:</span>
        <span><?php echo htmlspecialchars($msg['message']); ?></span>
        <span class="time">(<?php echo $msg['created_at']; ?>)</span>
    </div>
<?php endforeach;