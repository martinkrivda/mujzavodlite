 <?php
require_once ('../db_connection.php');
include ('function.php');
$query = '';
$output = array();
$query .= "SELECT * FROM RUNNER WHERE DELETED = 0 ";
if (isset($_POST["search"]["value"])) {
    $query .= "AND (FIRSTNAME LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR LASTNAME LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR RUNNER_ID LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR VINTAGE LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR GENDER LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR COUNTRY_CODE LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR EMAIL LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR PHONE LIKE '%" . $_POST['search']['value'] . "%') ";
}
if (isset($_POST["order"])) {
    $query .= "ORDER BY " . $_POST['order']['0']['column'] . " " . $_POST['order']['0']['dir'] . " ";
} else {
    $query .= "ORDER BY RUNNER_ID ASC";
}
if ($_POST["length"] != - 1) {
    $query .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
}
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data = array();
$filtered_rows = $stmt->rowCount();
foreach ($result as $row) {
    $sub_array = array();
    $sub_array[] = htmlspecialchars($row['RUNNER_ID']);
    $sub_array[] = htmlspecialchars($row['FIRSTNAME']);
    $sub_array[] = htmlspecialchars($row['LASTNAME']);
    $sub_array[] = htmlspecialchars($row['VINTAGE']);
    $sub_array[] = htmlspecialchars($row['GENDER']);
    $sub_array[] = htmlspecialchars($row['COUNTRY_CODE']);
    $sub_array[] = htmlspecialchars($row['EMAIL']);
    $sub_array[] = htmlspecialchars($row['PHONE']);
    $sub_array[] = '<button type="button" name="update" id="' . $row["RUNNER_ID"] . '" class="btn btn-warning btn-xs update" data-toggle="modal" data-target="#addRunner">Update</button>';
    $sub_array[] = '<button type="button" name="delete" id="' . $row["RUNNER_ID"] . '" class="btn btn-danger btn-xs delete">Delete</button>';
    $data[] = $sub_array;
}
$output = array(
    "draw" => intval(@$_POST["draw"]),
    "recordsTotal" => $filtered_rows,
    "recordsFiltered" => get_total_all_runners(),
    "data" => $data
);
echo json_encode($output);
?>