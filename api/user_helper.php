<?php
// This file provides helper functions for user management

/**
 * Ensures the userId is available in the session
 * If only username is available, it will fetch the userId from the database
 */
function ensure_user_id() {
    if (!isset($_SESSION['userId']) && isset($_SESSION['username'])) {
        require_once 'skeletondb.php';
        
        // Get userId from username
        $conn = get_conn();
        $query = "SELECT UserID FROM users WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        
        if ($user) {
            $_SESSION['userId'] = $user['UserID'];
            return true;
        }
        return false;
    }
    return isset($_SESSION['userId']);
} 