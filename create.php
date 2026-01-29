<?php
include('db.php');

// Initialize variables
$error = '';
$success = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    
    // Validate form data
    if (empty($title)) {
        $error = "Title is required!";

    } elseif (empty($content)) {
        $error = "Content is required!";

    } else {
        // Escape special characters to prevent SQL injection
        $title = $conn->real_escape_string($title);

        $content = $conn->real_escape_string($content);
        
        // Insert into database
        $sql = "INSERT INTO notes (title, content) VALUES ('$title', '$content')";

        $entryRes = $conn->query($sql);

        if ($entryRes === TRUE) {
            $success = "Note created successfully!";

            // Redirect to home page after 1 second
            header("refresh:1;url=index.php");

        } else {
            $error = "Error creating note: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Note - Notes App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Create New Note</h1>
            
            <!-- Show error message if validation failed -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Show success message if note created -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Form to create note -->
            <form method="POST" action="create.php">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        placeholder="Enter note title..." 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="8" 
                        placeholder="Enter note content..." 
                        required
                    ></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Save Note</button>
                    <a href="index.php" class="btn btn-secondary">← Back to Notes</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 