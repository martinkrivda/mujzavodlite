<?php
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('../db_connection.php');
// pristup jen pro prihlaseneho uzivatele
require '../userrequired.php';

$page = isset($_GET['p']) ? $_GET['p'] : '';
if ($page == 'view') {
    try {
        $stmt = $conn->prepare("SELECT * FROM RACE");
        $stmt->execute();
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    $races = array();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $res) {
        ?>
<tr>
	<td><?php echo htmlspecialchars($res['RACE_ID']); ?></td>
	<td><?php echo htmlspecialchars($res['NAME']); ?></td>
	<td><?php echo htmlspecialchars($res['LOCATION']); ?></td>
	<td><?php echo htmlspecialchars($res['ORGANISER']); ?></td>
	<td><?php echo htmlspecialchars($res['WEB']); ?></td>
	<td><?php echo htmlspecialchars($res['EMAIL']); ?></td>
	<td><?php echo htmlspecialchars($res['PHONE']); ?></td>
</tr>
<?php
    }
} else {
    
    // Basic example of PHP script to handle with jQuery-Tabledit plug-in.
    // Note that is just an example. Should take precautions such as filtering the input data.
    
    header('Content-Type: application/json');
    
    $input = filter_input_array(INPUT_POST);
    
    if ($input['action'] == 'edit') {
        try {
            $stmt = $conn->prepare("UPDATE RACE SET NAME = :NAME, LOCATION = :LOCATION, ORGANISER = :ORGANISER, WEB = :WEB, EMAIL = :EMAIL, PHONE = :PHONE WHERE RACE_ID = :RACE_ID");
            $stmt->bindParam(":NAME", $input['racename']);
            $stmt->bindParam(":LOCATION", $input['location']);
            $stmt->bindParam(":ORGANISER", $input['organiser']);
            $stmt->bindParam(":WEB", $input['webpage']);
            $stmt->bindParam(":EMAIL", $input['email']);
            $stmt->bindParam(":PHONE", $input['phone']);
            $stmt->bindParam(":RACE_ID", $input['race_id']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    } else if ($input['action'] == 'delete') {
        try {
            $stmt = $conn->prepare("DELETE FROM RACE WHERE RACE_ID = :RACE_ID");
            $stmt->bindParam(":RACE_ID", $input['race_id']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    } else if ($input['action'] == 'restore') {
        try {
            $stmt = $conn->prepare("UPDATE RACE SET DELETED = 0 WHERE RACE_ID = :RACE_ID");
            $stmt->bindParam(":RACE_ID", $input['race_id']);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Oh noes! There's an error in the query!" . $e->getMessage());
        }
    }
    
    echo json_encode($input);
}
?>