<?php
include 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header('Location: index.php?message=Quest deleted successfully!');
    } else {
        header('Location: index.php?error=Error deleting quest.');
    }
} else {
    header('Location: index.php');
}
exit;