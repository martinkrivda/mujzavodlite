 <?php
require_once ('../db_connection.php');
include ('function.php');
$query = '';
$output = array();
$query .= "SELECT * FROM CLUB WHERE DELETED = 0 ";
if (isset($_POST["search"]["value"])) {
    $query .= "AND (NAME LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR NAME2 LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR CLUB_ID LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR CODE LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR STREET LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR CITY LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR POSTALCODE LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR WEB LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR TAXID LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR VATID LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR COUNTRY_CODE LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR EMAIL LIKE '%" . $_POST['search']['value'] . "%' ";
    $query .= "OR PHONE LIKE '%" . $_POST['search']['value'] . "%') ";
}
if (isset($_POST["order"])) {
    $query .= "ORDER BY " . $_POST['order']['0']['column'] . " " . $_POST['order']['0']['dir'] . " ";
} else {
    $query .= "ORDER BY CLUB_ID ASC";
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
    $sub_array[] = htmlspecialchars($row['CLUB_ID']);
    $sub_array[] = htmlspecialchars($row['NAME']) . " " . htmlspecialchars($row['NAME2']);
    $sub_array[] = htmlspecialchars($row['CODE']);
    $sub_array[] = htmlspecialchars($row['STREET']);
    $sub_array[] = htmlspecialchars($row['CITY']);
    $sub_array[] = htmlspecialchars($row['POSTALCODE']);
    $sub_array[] = htmlspecialchars($row['COUNTRY_CODE']);
    $sub_array[] = htmlspecialchars($row['TAXID']);
    $sub_array[] = htmlspecialchars($row['VATID']);
    $sub_array[] = htmlspecialchars($row['WEB']);
    $sub_array[] = htmlspecialchars($row['EMAIL']);
    $sub_array[] = htmlspecialchars($row['PHONE']);
    $sub_array[] = '<button type="button" name="update" id="' . $row["CLUB_ID"] . '" class="btn btn-warning btn-xs update" data-toggle="modal" data-target="#addClub">Upravit</button>';
    $sub_array[] = '<button type="button" name="delete" id="' . $row["CLUB_ID"] . '" class="btn btn-danger btn-xs delete">Smazat</button>';
    $data[] = $sub_array;
}
$output = array(
    "draw" => intval(@$_POST["draw"]),
    "recordsTotal" => $filtered_rows,
    "recordsFiltered" => get_total_all_clubs(),
    "data" => $data
);
echo json_encode($output);
?>