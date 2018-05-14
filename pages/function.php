<?php

function get_total_all_records()
{
    require('../db_connection.php');
    $stmt = $conn->prepare("SELECT * FROM RUNNER WHERE DELETED = 0;");
    $stmt->execute();
    $result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $stmt->rowCount();
}

function fill_country(){
    require ('db_connection.php');
    $output = '';
    try {
        $stmt = $conn->prepare("SELECT * FROM COUNTRY");
        $stmt->execute();
    } catch (PDOException $e){
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output .= "<option value='".$row['COUNTRY_CODE']."'>".$row['COUNTRY_NAME']."</option>";
    }
    return $output;
}

?>