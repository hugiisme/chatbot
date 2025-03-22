<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_session'])) {
        // Clear all session data
        session_unset();
        session_destroy();

        // Redirect to the same page to refresh the session
        header("Location: " . "../index.php");
        exit;
    }
?>
