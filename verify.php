<?php
/*
 * Verifies registered user email, the link to this page
 * is included in the register.php email message
 */
require_once ('db_connection.php');
session_start();

// Make sure email and hash variables aren't empty
if (isset($_GET['email']) && ! empty($_GET['email']) and isset($_GET['hash']) && ! empty($_GET['hash'])) {
    $email = $conn->quote($_GET['email']);
    $hash = $conn->quote($_GET['hash']);
    
    // Select user with matching email and hash, who hasn't verified their account yet (active = 0)
    $result = $conn->query("SELECT * FROM LOGIN WHERE EMAIL=$email AND HASH=$hash AND ACTIVE='0'");
    
    if ($result->fetchColumn() == 0) {
        $_SESSION['message'] = "Účet již byl aktivován nebo URL adresa je neplatná!";
        header("location: error.php");
        exit();
    } else {
        $_SESSION['message'] = "Tvůj účet byl aktivován!";
        
        // Set the user status to active (active = 1)
        try {
            $conn->query("UPDATE LOGIN SET ACTIVE='1' WHERE EMAIL=$email");
            $_SESSION['active'] = 1;
            header("location: index.php?success=ok");
            exit();
        } catch (PDOException $e) {
            echo ("Error: " . $e->getMessage());
            $_SESSION['message'] = 'Verification failed!' . $e->getMessage();
            header("location: error.php");
        }
    }
} else {
    $_SESSION['message'] = "Neplatné parametry pro ověření tvého účtu!";
    header("location: error.php");
    exit();
}
?>