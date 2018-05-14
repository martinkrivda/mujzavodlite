 <?php
 header('Content-Type: application/json; charset=UTF-8');
require_once ('../db_connection.php');
include ('function.php');
$response = array();
if (isset($_POST["runner_id"])) {
    try {
        $stmt = $conn->prepare("UPDATE RUNNER SET DELETED = 1 WHERE RUNNER_ID = :RUNNER_ID;");
        $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"]);
        $result = $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    
    if (! empty($result)) {
        $response['status'] ='success'; 
        $response['message'] ="Závodník byl úspěšně odstraněn!"; 
    } else {
        $response['status'] ='error';
        $response['message'] ="Závodníka nelze odebrat!"; 
    }
    echo json_encode($response); 
}

?>