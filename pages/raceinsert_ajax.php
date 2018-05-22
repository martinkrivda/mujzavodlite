<?php
require_once ('../db_connection.php');
include ('function.php');
date_default_timezone_set('Europe/Prague');
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
if (! empty($_POST) && isset($_POST["operation"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST['racename'] = trim(@$_POST['racename']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,70}$/", $_POST['racename'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat název závodu! Název může obsahovat  písmena, pomlčku, tečku, číslice a mít délku maximálně 70 znaků.';
    }
    $_POST['location'] = trim(@$_POST['location']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,50}$/", @$_POST['location'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Chybně zadané místo konání závodu. místo může obsahovat jen písmena, mezeru, pomlčku, tečku, čísloce a mít délku max. 50 znaků.';
    }
    $_POST['organiser'] = trim(@$_POST['organiser']);
    if (! preg_match("/^[a-zA-Z\-0-9]{3,10}$/", $_POST['organiser'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat organizátora! Zkratka organizátora musí obsahovat 3 až 10 deset. Jsou povoleny nediakritické znaky, číslice a pomlčka.';
    }
    $_POST['webpage'] = trim(@$_POST['webpage']);
    if (! preg_match("/^http:\/\/[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/", @$_POST['webpage']) && @$_POST['webpage'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Webová stránka obsahuje nepovolené znaky!';
    }
    $_POST['email'] = trim(@$_POST['email']);
    if ($_POST['email'] != '' && ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // filter_var umožňuje jednoduchou kontrolu základních typů dat (čísla, mail, url atp.)
        $faults[] = 'Zadaný e-mail není platný!';
    }
    $_POST['phone'] = trim(@$_POST['phone']);
    $_POST['phone'] = preg_replace('/\s+/', '', @$_POST['phone']);
    if (! preg_match("/^[\+]?[()\/0-9\. \-]{9,}$/", @$_POST['phone']) && @$_POST['phone'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat platné telefonní číslo!';
    }
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak uložíme získaná data a provedeme redirect
        if (isset($_POST["operation"])) {
            if ($_POST["operation"] == "Add") {
                try {
                    $stmt = $conn->prepare("INSERT INTO RACE (NAME, LOCATION, ORGANISER, WEB, EMAIL, PHONE, CREATOR) VALUES (:NAME, :LOCATION, :ORGANISER, :WEB, :EMAIL, :PHONE, :CREATOR)");
                    $stmt->bindParam(':NAME', $_POST["racename"], PDO::PARAM_STR);
                    $stmt->bindParam(':LOCATION', $_POST["location"], PDO::PARAM_STR);
                    $stmt->bindParam(':ORGANISER', $_POST["organiser"], PDO::PARAM_STR);
                    $stmt->bindParam(':WEB', $_POST["webpage"], PDO::PARAM_STR);
                    $stmt->bindParam(':EMAIL', $_POST["email"], PDO::PARAM_STR);
                    $stmt->bindParam(':PHONE', $_POST["phone"], PDO::PARAM_STR);
                    $stmt->bindParam(':CREATOR', $a = 1);
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
                
                if (! empty($result)) {
                    echo "<div class='sufee-alert alert with-close alert-success alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-success'>Úspěch</span>";
                    echo "Závod byl přidán do databáze.";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button></div>";
                }
            }
            if ($_POST["operation"] == "Edit") {
                try {
                    $stmt = $conn->prepare("UPDATE CLUB SET NAME = :NAME, NAME2 = :NAME2, CODE = :CODE, STREET = :STREET, CITY = :CITY, POSTALCODE = :POSTALCODE, TAXID = :TAXID, VATID = :VATID, EMAIL = :EMAIL, PHONE = :PHONE, COUNTRY_CODE = :COUNTRY_CODE, WEB = :WEB WHERE CLUB_ID = :CLUB_ID");
                    $stmt->bindParam(':NAME', $_POST["clubname"], PDO::PARAM_STR);
                    $stmt->bindParam(':NAME2', $_POST["clubname2"], PDO::PARAM_STR);
                    $stmt->bindParam(':CODE', $_POST["clubcode"], PDO::PARAM_STR);
                    $stmt->bindParam(':STREET', $_POST["street"], PDO::PARAM_STR);
                    $stmt->bindParam(':CITY', $_POST["city"], PDO::PARAM_STR);
                    $stmt->bindParam(':POSTALCODE', $_POST["zip"], PDO::PARAM_INT);
                    $stmt->bindParam(':TAXID', $_POST["taxid"], PDO::PARAM_INT);
                    $stmt->bindParam(':VATID', $_POST["vatid"], PDO::PARAM_STR);
                    $stmt->bindParam(':EMAIL', $_POST["email"], PDO::PARAM_STR);
                    $stmt->bindParam(':PHONE', $_POST["phone"], PDO::PARAM_STR);
                    $stmt->bindParam(':COUNTRY_CODE', $_POST["country"], PDO::PARAM_STR);
                    $stmt->bindParam(':WEB', $_POST["webpage"], PDO::PARAM_STR);
                    $stmt->bindParam(':CLUB_ID', $_POST["club_id"], PDO::PARAM_INT);
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
                if (! empty($result)) {
                    echo "<div class='sufee-alert alert with-close alert-success alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-success'>Úspěch</span>";
                    echo "Klub byl úspěšně editován.";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button></div>";
                }
            }
        }
    } else {
        echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
        echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
        echo '<ul style="color:red;">';
        foreach ($faults as $fault) {
            echo "<li>" . $fault . "</li>";
        }
        echo "</ul>";
        echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        echo "<span aria-hidden='true'>&times;</span>";
        echo "</button>";
        echo "</div>";
    }
}
?>