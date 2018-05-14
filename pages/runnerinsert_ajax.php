<?php
require_once ('../db_connection.php');
include ('function.php');
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
        } catch (PDOException $e){
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
        try{
            $stmt = $conn->prepare("UPDATE RUNNER SET FIRSTNAME = :FIRSTNAME, LASTNAME = :LASTNAME, VINTAGE = :VINTAGE, GENDER = :GENDER, EMAIL = :EMAIL, PHONE = :PHONE, COUNTRY_CODE = :COUNTRY_CODE WHERE RUNNER_ID = :RUNNER_ID");
            $stmt->bindParam(':FIRSTNAME', $_POST["firstname"], PDO::PARAM_STR);
            $stmt->bindParam(':LASTNAME', $_POST["lastname"], PDO::PARAM_STR);
            $stmt->bindParam(':VINTAGE', $_POST["vintage"], PDO::PARAM_INT);
            $stmt->bindParam(':GENDER', $_POST["gender"], PDO::PARAM_STR);
            $stmt->bindParam(':EMAIL', $_POST["email"], PDO::PARAM_STR);
            $stmt->bindParam(':PHONE', $_POST["phone"], PDO::PARAM_STR);
            $stmt->bindParam(':COUNTRY_CODE', $_POST["country"], PDO::PARAM_STR);
            $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"], PDO::PARAM_INT);
            $result = $stmt->execute();
        }catch (PDOException $e){
            die("Error: " . $e->getMessage());
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

?>