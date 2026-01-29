<?php
include('db.php');

// Get note ID from URL parameter
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($note_id <= 0) {
    die("Invalid note ID!");
}

// Delete the note from database
$sql = "DELETE FROM notes WHERE id = $note_id";

    // Note deleted successfully, redirect to index
if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
    exit();
} else {
    die("Error deleting note: " . $conn->error);
}
?>