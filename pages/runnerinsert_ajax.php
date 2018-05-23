<?php
require_once ('../db_connection.php');
include ('function.php');
date_default_timezone_set('Europe/Prague');
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
if (! empty($_POST) && isset($_POST["operation"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST['firstname'] = trim(@$_POST['firstname']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", $_POST['firstname'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat křestní jméno! Křestní jméno může obsahovat  písmena, pomlčku mít délku maximálně 50 znaků.';
    }
    $_POST['lastname'] = trim(@$_POST['lastname']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}$/", $_POST['lastname'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat příjmení! Příjmení může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['vintage'] = trim(@$_POST['vintage']);
    if (! preg_match("/^\d{4}$/", $_POST['vintage'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat rok narození! Rok narození musí obsahovat jen 4 číslice.';
    }
    $_POST['gender'] = trim(@$_POST['gender']);
    if (! preg_match("/^[A-Za-zŽž]{1,6}$/", @$_POST['gender'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat pohlaví!';
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
    $_POST['country'] = trim(@$_POST['country']);
    if (! preg_match("/^[A-Z]{2}$/", @$_POST['country'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat stát!';
    }
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak uložíme získaná data a provedeme redirect
        if (isset($_POST["operation"])) {
            if ($_POST["operation"] == "Add") {
                try {
                    $stmt = $conn->prepare("INSERT INTO RUNNER (FIRSTNAME, LASTNAME, VINTAGE, GENDER, EMAIL, PHONE, COUNTRY_CODE) VALUES (:FIRSTNAME, :LASTNAME, :VINTAGE, :GENDER, :EMAIL, :PHONE, :COUNTRY_CODE)");
                    $stmt->bindParam(':FIRSTNAME', $_POST["firstname"], PDO::PARAM_STR);
                    $stmt->bindParam(':LASTNAME', $_POST["lastname"], PDO::PARAM_STR);
                    $stmt->bindParam(':VINTAGE', $_POST["vintage"], PDO::PARAM_INT);
                    $stmt->bindParam(':GENDER', $_POST["gender"], PDO::PARAM_STR);
                    $stmt->bindParam(':EMAIL', $_POST["email"], PDO::PARAM_STR);
                    $stmt->bindParam(':PHONE', $_POST["phone"], PDO::PARAM_STR);
                    $stmt->bindParam(':COUNTRY_CODE', $_POST["country"], PDO::PARAM_STR);
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
                
                if (! empty($result)) {
                    echo "<div class='sufee-alert alert with-close alert-success alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-success'>Úspěch</span>";
                    echo "Závodník byl přidán do databáze.";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button></div>";
                }
            }
            if ($_POST["operation"] == "Edit") {
                try {
                    $stmt = $conn->prepare("SELECT LASTUPDATED FROM RUNNER WHERE RUNNER_ID = :RUNNER_ID");
                    $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"], PDO::PARAM_INT);
                    $stmt->execute();
                    $runnerexist = $stmt->fetch();
                    if (! $runnerexist) {
                        die("Nemohu nalézt závodníka!");
                    }
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                } finally {
                    $stmt = $conn->prepare("SELECT LASTUPDATED FROM RUNNER WHERE RUNNER_ID = :RUNNER_ID");
                    $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"], PDO::PARAM_INT);
                    $stmt->execute();
                    $current_last_updated_at = $stmt->fetchColumn();
                }
                
                if ($_POST['lastupdated'] != $current_last_updated_at) {
                    echo '<script src="assets/js/sweetalert2.all.js"></script>';
                    echo '<script type="text/javascript" language="javascript">';
                    echo "swal('Pozor!','Závodníka s tímto ID někdo mezitím editoval!','warning');";
                    echo '</script>';
                } else {
                    try {
                        $stmt = $conn->prepare("UPDATE RUNNER SET FIRSTNAME = :FIRSTNAME, LASTNAME = :LASTNAME, VINTAGE = :VINTAGE, GENDER = :GENDER, EMAIL = :EMAIL, PHONE = :PHONE, COUNTRY_CODE = :COUNTRY_CODE, LASTUPDATED=now() WHERE RUNNER_ID = :RUNNER_ID");
                        $stmt->bindParam(':FIRSTNAME', $_POST["firstname"], PDO::PARAM_STR);
                        $stmt->bindParam(':LASTNAME', $_POST["lastname"], PDO::PARAM_STR);
                        $stmt->bindParam(':VINTAGE', $_POST["vintage"], PDO::PARAM_INT);
                        $stmt->bindParam(':GENDER', $_POST["gender"], PDO::PARAM_STR);
                        $stmt->bindParam(':EMAIL', $_POST["email"], PDO::PARAM_STR);
                        $stmt->bindParam(':PHONE', $_POST["phone"], PDO::PARAM_STR);
                        $stmt->bindParam(':COUNTRY_CODE', $_POST["country"], PDO::PARAM_STR);
                        $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"], PDO::PARAM_INT);
                        $result = $stmt->execute();
                    } catch (PDOException $e) {
                        die("Error: " . $e->getMessage());
                    }
                }
                if (! empty($result)) {
                    echo "<div class='sufee-alert alert with-close alert-success alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-success'>Úspěch</span>";
                    echo "Závodník byl úspěšně editován.";
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