<?php

include('db.php');

// Fetch all notes from database, ordered by newest first
$sql = "SELECT * FROM notes ORDER BY created_at DESC";
$result = $conn->query($sql);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Store all notes in an array
$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

$note_count = count($notes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes - Notes App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <div class="header">
            <h1>📓 My Notes</h1>
            <p class="note-count">Total: <?php echo $note_count; ?> note(s)</p>
            <a href="create.php" class="btn btn-primary">+ New Note</a>
        </div>
        
        <!-- Show message if no notes exist -->
        <?php if ($note_count === 0): ?>
            <div class="empty-state">
                <p>📭 No notes yet. Create your first note!</p>
                <a href="create.php" class="btn btn-primary">Create First Note</a>
            </div>
        <?php else: ?>

            <div class="notes-grid">

                <?php foreach ($notes as $note): ?>
                    <div class="note-card">
                        <h2 class="note-title">
                            <?php echo htmlspecialchars($note['title']); ?>
                        </h2>
                        
                        <!-- preview -->
                        <p class="note-content">
                            <?php 
                            // Show first 150 characters of content
                            $preview = substr(htmlspecialchars($note['content']), 0, 150);
                            echo $preview;
                            if (strlen($note['content']) > 150) echo '...';
                            ?>
                        </p>
                        
                        <p class="note-meta">
                            Created: <?php echo date('M d, Y H:i', strtotime($note['created_at'])); ?>
                        </p>
                        
                        <!-- Buttons -->
                        <div class="note-actions">
                            <a href="edit.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">
                                Edit
                            </a>

                            <a href="delete.php?id=<?php echo $note['id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Delete this note?');">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>