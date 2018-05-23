 <?php
require_once ('../db_connection.php');
include ('function.php');
if (isset($_POST["runner_id"])) {
    $output = array();
    $stmt = $conn->prepare("SELECT * FROM RUNNER WHERE RUNNER_ID = :RUNNER_ID LIMIT 1;");
    $stmt->bindParam("RUNNER_ID", $_POST['runner_id']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $output["runner_id"] = htmlspecialchars($row["RUNNER_ID"]);
        $output["firstName"] = htmlspecialchars($row["FIRSTNAME"]);
        $output["lastName"] = htmlspecialchars($row["LASTNAME"]);
        $output["vintage"] = htmlspecialchars($row["VINTAGE"]);
        $output["gender"] = htmlspecialchars($row["GENDER"]);
        $output["email"] = htmlspecialchars($row["EMAIL"]);
        $output["phone"] = htmlspecialchars($row["PHONE"]);
        $output["country_code"] = htmlspecialchars($row["COUNTRY_CODE"]);
        $output["lastupdated"] = htmlspecialchars($row["LASTUPDATED"]);
    }
    echo json_encode($output);
}
?>