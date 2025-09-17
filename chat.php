<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Invia messaggio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $message]);
    }
}

// Ottieni messaggi
$stmt = $pdo->prepare("
    SELECT m.message, m.created_at, u.username 
    FROM messages m 
    JOIN users u ON m.user_id = u.id 
    ORDER BY m.created_at DESC 
    LIMIT 50
");
$stmt->execute();
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Chat Locale</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f0f0f0; 
            margin: 0; 
            padding: 0; 
        }
        .header { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            padding: 20px; 
        }
        .chat-messages { 
            height: 400px; 
            overflow-y: auto; 
            border: 1px solid #ddd; 
            padding: 10px; 
            margin-bottom: 20px; 
            border-radius: 4px; 
        }
        .message { 
            margin-bottom: 10px; 
            padding: 8px; 
            border-radius: 4px; 
            background-color: #f9f9f9; 
        }
        .message .username { 
            font-weight: bold; 
            color: #007bff; 
        }
        .message .time { 
            font-size: 0.8em; 
            color: #999; 
        }
        .message-form { 
            display: flex; 
        }
        .message-input { 
            flex-grow: 1; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .send-button { 
            padding: 10px 20px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            margin-left: 10px; 
            cursor: pointer; 
        }
        .send-button:hover { 
            background-color: #0056b3; 
        }
        .logout-button { 
            background-color: #dc3545; 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .logout-button:hover { 
            background-color: #c82333; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Chat Locale</h1>
        <div>
            <span>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <span class="username"><?php echo htmlspecialchars($msg['username']); ?>:</span>
                    <span><?php echo htmlspecialchars($msg['message']); ?></span>
                    <span class="time">(<?php echo $msg['created_at']; ?>)</span>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="message-form" id="message-form">
            <input type="text" name="message" class="message-input" placeholder="Scrivi un messaggio..." required>
            <button type="submit" class="send-button">Invia</button>
        </form>
    </div>

    <script>
        // Aggiorna la chat ogni 3 secondi
        setInterval(function() {
            fetch('get_messages.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('chat-messages').innerHTML = data;
                    // Scroll automatico verso il basso
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }, 3000);

        // Scroll automatico all'inizio
        window.onload = function() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };
    </script>
</body>
</html>