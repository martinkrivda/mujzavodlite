<?php
// Check if user is logged in using the session variable
if ($_SESSION['logged_in'] != true) {
    $_SESSION['message'] = "Musíš být přihlášen před použitím aplikace!";
    header("location: login.php");
    exit();
} else {
    // v session je user id uzivatele, ted ho nacteme z db
    $stmt = $conn->prepare("SELECT * FROM LOGIN WHERE USERNAME = ? AND ACTIVE = 1 LIMIT 1"); // limit 1 jen jako vykonnostni optimalizace, 2 stejne maily se v db nepotkaji
    $stmt->execute(array(
        $_SESSION["username"]
    ));
    // nacte do promenne $user aktualne prihlaseneho usera, bude pristupny z cele aplikace
    $currentUser = $stmt->fetchAll()[0]; // vezmi prvni zaznam z db
                                         // pokud by v db z nejakeho duvodu user nebyl (treba byl mezitim nejak smazan), tak vymaz session a jdi na prihlaseni
    if (! $currentUser) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }
    // Makes it easier to read
    $firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}
?>