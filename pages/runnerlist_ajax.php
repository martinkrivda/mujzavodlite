<?php
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('../db_connection.php');
// pristup jen pro prihlaseneho uzivatele
require '../userrequired.php';

$page = isset($_GET['p']) ? $_GET['p'] : '';
if ($page == 'view') {
    try {
        $stmt = $conn->prepare("SELECT * FROM RUNNER WHERE DELETED = 0");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $runners = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $res) {
        
        $runners[] = array(
            "runner_ID" => htmlspecialchars($res['RUNNER_ID']),
            "firstName" => htmlspecialchars($res['FIRSTNAME']),
            "lastName" => htmlspecialchars($res['LASTNAME']),
            "vintage" => htmlspecialchars($res['VINTAGE']),
            "gender" => htmlspecialchars($res['GENDER']),
            "country" => htmlspecialchars($res['COUNTRY_CODE']),
            "email" => htmlspecialchars($res['EMAIL']),
            "phone" => htmlspecialchars($res['PHONE'])
        );
    }
    foreach ($runners as $runner) {
        $data = array(
            "data" => array(
                $runner
            
            )
        );
    }
    echo json_encode($data);
} else {
    
    // Basic example of PHP script to handle with jQuery-Tabledit plug-in.
    // Note that is just an example. Should take precautions such as filtering the input data.
    
    header('Content-Type: application/json');
    
    $input = filter_input_array(INPUT_POST);
    
    if ($input['action'] == 'edit') {
        try {
            $stmt = $conn->prepare("UPDATE RUNNER SET FIRSTNAME = :FIRSTNAME, LASTNAME = :LASTNAME, VINTAGE = :VINTAGE, GENDER = :GENDER, COUNTRY_CODE = :COUNTRY_CODE, EMAIL = :EMAIL, PHONE = :PHONE WHERE RUNNER_ID = :RUNNER_ID");
            $stmt->bindParam(":FIRSTNAME", $input['firstName']);
            $stmt->bindParam(":LASTNAME", $input['lastName']);
            $stmt->bindParam(":VINTAGE", $input['vintage']);
            $stmt->bindParam(":GENDER", $input['gender']);
            $stmt->bindParam(":COUNTRY_CODE", $input['country']);
            $stmt->bindParam(":EMAIL", $input['email']);
            $stmt->bindParam(":PHONE", $input['phone']);
            $stmt->bindParam(":RUNNER_ID", $input['runner_ID']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    } else if ($input['action'] == 'delete') {
        try {
            $stmt = $conn->prepare("UPDATE RUNNER SET DELETED = 1 WHERE RUNNER_ID = :RUNNER_ID");
            $stmt->bindParam(":RUNNER_ID", $input['runner_ID']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    } else if ($input['action'] == 'restore') {
        try {
            $stmt = $conn->prepare("UPDATE RUNNER SET DELETED = 0 WHERE RUNNER_ID = :RUNNER_ID");
            $stmt->bindParam(":RUNNER_ID", $input['runner_ID']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    }
    
    echo json_encode($input);
}
?>