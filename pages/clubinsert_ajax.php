<?php
require_once ('../db_connection.php');
include ('function.php');
date_default_timezone_set('Europe/Prague');
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
if (! empty($_POST) && isset($_POST["operation"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST['clubname'] = trim(@$_POST['clubname']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,70}$/", $_POST['clubname'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat název klubu! Název může obsahovat  písmena, pomlčku, tečku a mít délku maximálně 70 znaků.';
    }
    $_POST['clubname2'] = trim(@$_POST['clubname2']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", @$_POST['clubname2']) && @$_POST['clubname2'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Chybně zadaný druhý název klubu. Název může obsahovat jen písmena, mezeru, pomlčku, tečku a mít délku max. 50 znaků.';
    }
    $_POST['clubcode'] = trim(@$_POST['clubcode']);
    if (! preg_match("/^[a-zA-Z\-0-9]{3,10}$/", $_POST['clubcode'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat zkratku klubu! Zkratka musí obsahovat 3 až 10 deset. Jsou povoleny nediakritické znaky, číslice a pomlčka.';
    }
    $_POST['street'] = trim(@$_POST['street']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,30}$/", @$_POST['street']) && @$_POST['street'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Ulice obsahuje nepovolené znaky!';
    }
    $_POST['city'] = trim(@$_POST['city']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,30}$/", @$_POST['city']) && @$_POST['city'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Město obsahuje nepovolené znaky!';
    }
    $_POST['zip'] = trim(@$_POST['zip']);
    $_POST['zip'] = preg_replace('/\s+/', '', @$_POST['zip']);
    if (! preg_match("/^\d{3} ?\d{2}$/", @$_POST['zip']) && @$_POST['zip'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'PSČ obsahuje nepovolené znaky!';
    }
    $_POST['taxid'] = trim(@$_POST['taxid']);
    if (! preg_match("/^\d{8}$/", @$_POST['taxid']) && @$_POST['taxid'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'IČO obsahuje nepovolené znaky!';
    }
    $_POST['vatid'] = trim(@$_POST['vatid']);
    if (! preg_match("/^(CZ|SK)\d{8}$/", @$_POST['vatid']) && @$_POST['vatid'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'DIČ obsahuje nepovolené znaky!';
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
    $_POST['webpage'] = trim(@$_POST['webpage']);
    if (! preg_match("/^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]$/", @$_POST['webpage']) && @$_POST['webpage'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Webová stránka obsahuje nepovolené znaky!';
    }
    $_POST['country'] = trim(@$_POST['country']);
    if (! preg_match("/^[A-Z]{2}$/", @$_POST['country'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat stát!';
    }
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak uložíme získaná data a provedeme redirect
        if (isset($_POST["operation"])) {
            if ($_POST["operation"] == "Add") {
                try {
                    $stmt = $conn->prepare("INSERT INTO CLUB (NAME, NAME2, CODE, STREET, CITY, POSTALCODE, TAXID, VATID, EMAIL, PHONE, COUNTRY_CODE, WEB) VALUES (:NAME, :NAME2, :CODE, :STREET, :CITY, :POSTALCODE, :TAXID, :VATID, :EMAIL, :PHONE, :COUNTRY_CODE, :WEB)");
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
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
                
                if (! empty($result)) {
                    echo "<div class='sufee-alert alert with-close alert-success alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-success'>Úspěch</span>";
                    echo "Klub byl přidán do databáze.";
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