 <?php
header('Content-Type: application/json; charset=UTF-8');
require_once ('../db_connection.php');
include ('function.php');
$response = array();
if (isset($_POST["club_id"])) {
    try {
        $stmt = $conn->prepare("UPDATE CLUB SET DELETED = 1 WHERE CLUB_ID = :CLUB_ID;");
        $stmt->bindParam(":CLUB_ID", $_POST['club_id']);
        $result = $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    
    if (! empty($result)) {
        $response['status'] = 'success';
        $response['message'] = "Klub byl úspěšně odstraněn!";
    } else {
        $response['status'] = 'error';
        $response['message'] = "Klub nelze odebrat!";
    }
    echo json_encode($response);
}

?>