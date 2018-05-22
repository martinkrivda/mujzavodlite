<?php

function get_total_all_runners()
{
    require ('../db_connection.php');
    $stmt = $conn->prepare("SELECT * FROM RUNNER WHERE DELETED = 0;");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $stmt->rowCount();
}

function get_total_all_clubs()
{
    require ('../db_connection.php');
    $stmt = $conn->prepare("SELECT * FROM CLUB WHERE DELETED = 0;");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $stmt->rowCount();
}

function fill_country()
{
    require ('db_connection.php');
    $output = '';
    try {
        $stmt = $conn->prepare("SELECT * FROM COUNTRY;");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output .= "<option value='" . htmlspecialchars($row['COUNTRY_CODE']) . "'>" . htmlspecialchars($row['COUNTRY_NAME']) . "</option>";
    }
    return $output;
}

function fill_club()
{
    require ('db_connection.php');
    $output = '';
    try {
        $stmt = $conn->prepare("SELECT CLUB_ID, NAME, CODE FROM CLUB WHERE DELETED = 0;");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output .= "<option value='" . htmlspecialchars($row['NAME']) . "'/>";
    }
    return $output;
}

function fill_organiser()
{
    require ('db_connection.php');
    $output = '';
    try {
        $stmt = $conn->prepare("SELECT * FROM ORGANISER;");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output .= "<option value='" . htmlspecialchars($row['CODE']) . "'>" . htmlspecialchars($row['NAME']) . "</option>";
    }
    return $output;
}

function fill_race()
{
    require ('db_connection.php');
    $output = '';
    try {
        $stmt = $conn->prepare("SELECT * FROM RACE;");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output .= "<option value='" . htmlspecialchars($row['RACE_ID']) . "'>" . htmlspecialchars($row['NAME']) . "</option>";
    }
    return $output;
}
?>