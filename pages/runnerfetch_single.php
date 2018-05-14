 <?php
 require_once ('../db_connection.php');
 include ('function.php');
if(isset($_POST["runner_id"]))
{
 $output = array();
 $stmt = $conn->prepare(
  "SELECT * FROM RUNNER 
  WHERE RUNNER_ID = '".$_POST["runner_id"]."' 
  LIMIT 1;"
 );
 $stmt->execute();
 $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
 foreach($result as $row)
 {
 $output["runner_id"] = $row["RUNNER_ID"];
 $output["firstName"] = $row["FIRSTNAME"];
  $output["lastName"] = $row["LASTNAME"];
  $output["vintage"] = $row["VINTAGE"];
  $output["gender"] = $row["GENDER"];
  $output["email"] = $row["EMAIL"];
  $output["phone"] = $row["PHONE"];
  $output["country_code"] = $row["COUNTRY_CODE"];
 }
 echo json_encode($output);
}
?>