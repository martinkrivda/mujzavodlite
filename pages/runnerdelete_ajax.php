 <?php
 require_once ('../db_connection.php');
 include ('function.php');
if(isset($_POST["runner_id"]))
{
    try{
        $stmt = $conn->prepare("UPDATE RUNNER SET DELETED = 1 WHERE RUNNER_ID = :RUNNER_ID;");
        $stmt->bindParam(':RUNNER_ID', $_POST["runner_id"]);
        $result = $stmt->execute();
    } catch (PDOException $e){
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
 
 if(!empty($result))
 {
  echo 'Data Deleted';
 }
}



?>