<?php
require_once ('db_connection.php');
/* Password reset process, updates database with new user password */
$faults = array(); // pracovní proměnná, do které budeme shromažďovat info o chybách
session_start();
// Check if form submitted with method="post"
if (! empty($_POST) && isset($_POST['reset']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['newpassword'] = trim(@$_POST['newpassword']);
    $_POST['confirmpassword'] = trim(@$_POST['confirmpassword']);
    // Make sure the two passwords match
    if ($_POST['newpassword'] === $_POST['confirmpassword']) {
        
        $newPassword = password_hash($_POST['newpassword'], PASSWORD_BCRYPT);
        try {
            // We get $_POST['email'] and $_POST['hash'] from the hidden input field of reset.php form
            $email = trim($_GET['email']);
            $hash = trim($_GET['hash']);
            
            $sql = "UPDATE LOGIN SET PASSWORD=:PASSWORD WHERE EMAIL=:EMAIL AND HASH=:HASH;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':PASSWORD', $newPassword, PDO::PARAM_STR);
            $stmt->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $stmt->bindParam(':HASH', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['message'] = "Your password has been reset successfully!";
            header("location: success.php");
            exit();
        } catch (PDOException $e) {
            die("Jejda, něco se porouchalo. " . $e->getMessage());
        }
    } else {
        $faults[] = 'Zadaná hesla se neshodují!';
        $_SESSION['message'] = "Two passwords you entered don't match, try again!";
        header("location: resetpassword.php?success=wrongpassword");
        exit();
    }
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="cs" xmlns="http://www.w3.org/1999/html">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>MůjZávod | ChytrýOddíl</title>
<meta name="keywords"
	content="závod,běh,web,app,aplikace,rfid,organizace,race,run">
<meta name="description"
	content="Webová aplikace pro organizaci a měření běžeckých závodů.">
<meta name="author" content="Martin Krivda">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="apple-touch-icon" href="apple-icon.png">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

<link rel="stylesheet" href="assets/css/normalize.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/css/themify-icons.css">
<link rel="stylesheet" href="assets/css/flag-icon.min.css">
<link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
<!-- <link rel="stylesheet" href="assets/css/bootstrap-select.less"> -->
<link rel="stylesheet" href="assets/scss/style.css">

<link
	href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800'
	rel='stylesheet' type='text/css'>

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

</head>
<body class="bg-dark">


	<div class="sufee-login d-flex align-content-center flex-wrap">
		<div class="container">
			<div class="login-content">
            <?php
            if (isset($_GET['success'])) {
                if (@$_GET['success'] == 'wrongpassword') {
                    echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
                    echo 'Zadaná hesla se neshodují!';
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                }
            }
            ?>
                <div class="login-logo">
					<a href="index.php"> <img class="align-content"
						src="images/logo.png" alt="">
					</a>
				</div>
				<div class="login-form">
					<form name="resetpassword" action="" method="POST">
						<div class="form-group">
							<label for="newpassword">Nové heslo</label> <input
								type="password" class="form-control" name="newpassword"
								id="newpassword" placeholder="Nové heslo" maxlength="100"
								required autocomplete="off" />
						</div>
						<div class="form-group">
							<label for="confirmpassword">Potvrzení hesla</label> <input
								type="password" class="form-control" name="confirmpassword"
								id="confirmpassword" placeholder="Potvrzení hesla"
								maxlength="100" required autocomplete="off" />
						</div>
						<button type="submit"
							class="btn btn-success btn-flat m-b-30 m-t-30" name="reset">Změnit
							heslo</button>
					</form>
				</div>
			</div>
		</div>
	</div>


	<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/plugins.js"></script>
	<script src="assets/js/main.js"></script>


</body>
</html>