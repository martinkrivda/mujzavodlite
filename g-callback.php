<?php
session_start();
require_once ('oauth.php');
require_once ('db_connection.php');
if (isset($_SESSION['access_token']))
    $gClient->setAccessToken($_SESSION['access_token']);
elseif (isset($_GET['code'])) {
    $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
} else {
    header('Location: login.php');
    exit();
}
$oAuth = new Google_Service_Oauth2($gClient);
$userData = $oAuth->userinfo_v2_me->get();
$_SESSION['gid'] = $userData['id'];
$_SESSION['email'] = $userData['email'];
$_SESSION['picture'] = $userData['picture'];

try {
    $_SESSION['gid'] = trim($_SESSION['gid']);
    $stmt = $conn->prepare("SELECT * FROM LOGIN WHERE GOOGLE_ID = :GOOGLE_ID AND ACTIVE = 1 LIMIT 1;");
    $stmt->bindParam(":GOOGLE_ID", $_SESSION['gid']);
    $stmt->execute();
} catch (Exception $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
}
$user = @$stmt->fetchAll(PDO::FETCH_ASSOC)[0];
if ($stmt->rowCount() != 0) {
    if ($user['GOOGLE_ID'] == $_SESSION['gid']) {
        $_SESSION['userID'] = $user['LOGIN_ID'];
        $_SESSION['email'] = $user['EMAIL'];
        $_SESSION['username'] = $user['USERNAME'];
        $_SESSION['firstName'] = $user['FIRSTNAME'];
        $_SESSION['lastName'] = $user['LASTNAME'];
        $_SESSION['active'] = $user['ACTIVE'];
        $_SESSION['lastLogin'] = date("Y-m-d H:i:s");
        
        try {
            $stmt = $conn->prepare("UPDATE LOGIN SET LASTLOGIN = :LASTLOGIN WHERE LOGIN_ID = :LOGIN_ID;");
            $stmt->bindParam(":LASTLOGIN", $_SESSION['lastLogin']);
            $stmt->bindParam(":LOGIN_ID", $_SESSION['userID']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
        
        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = true;
        header("location: index.php");
        exit();
    }
} elseif ($_SESSION['logged_in'] == true && isset($_SESSION['userID'])) {
    try {
        $stmt = $conn->prepare("UPDATE LOGIN SET GOOGLE_ID = :GOOGLE_ID WHERE LOGIN_ID = :LOGIN_ID;");
        $stmt->bindParam(":GOOGLE_ID", $_SESSION['gid']);
        $stmt->bindParam(":LOGIN_ID", $_SESSION['userID']);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    header("location: profile.php?googlepair=success");
    exit();
} else {
    $_SESSION['message'] = "Uživatel s tímto Google účtem není registrován!";
    session_unset();
    $gClient->revokeToken();
    header("location: login.php?success=wronguser");
    session_destroy();
    exit();
}
?>