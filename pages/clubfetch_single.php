 <?php
require_once ('../db_connection.php');
include ('function.php');
if (isset($_POST["club_id"])) {
    $output = array();
    $stmt = $conn->prepare("SELECT * FROM CLUB WHERE CLUB_ID = :CLUB_ID LIMIT 1;");
    $stmt->bindParam(":CLUB_ID", $_POST['club_id']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output["club_id"] = htmlspecialchars($row["CLUB_ID"]);
        $output["clubname"] = htmlspecialchars($row["NAME"]);
        $output["clubname2"] = htmlspecialchars($row["NAME2"]);
        $output["clubcode"] = htmlspecialchars($row["CODE"]);
        $output["street"] = htmlspecialchars($row["STREET"]);
        $output["city"] = htmlspecialchars($row["CITY"]);
        $output["zip"] = htmlspecialchars($row["POSTALCODE"]);
        $output["taxid"] = htmlspecialchars($row["TAXID"]);
        $output["vatid"] = htmlspecialchars($row["VATID"]);
        $output["email"] = htmlspecialchars($row["EMAIL"]);
        $output["phone"] = htmlspecialchars($row["PHONE"]);
        $output["country_code"] = htmlspecialchars($row["COUNTRY_CODE"]);
        $output["webpage"] = htmlspecialchars($row["WEB"]);
    }
    echo json_encode($output);
}
?>