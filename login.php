<?php
require_once ('db_connection.php');
session_start();
date_default_timezone_set('Europe/Prague');
/* User login process, checks if user exists and password is correct */
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
if (! empty($_POST) && isset($_POST['signin']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Escape username to protect against SQL injections
    try {
        $username = $conn->quote($_POST['username'], PDO::FETCH_ASSOC);
        $result = $conn->query("SELECT * FROM LOGIN WHERE USERNAME = $username AND ACTIVE = 1 LIMIT 1", PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    if ($result->fetchColumn() == 0) { // User doesn't exist
        $_SESSION['message'] = "Uživatel s tímto uživatelským jménem neexistuje!";
        header("location: login.php?success=wronguser");
        exit();
    } else { // User exists
        $result = $conn->query("SELECT * FROM LOGIN WHERE USERNAME = $username AND ACTIVE = 1 LIMIT 1", PDO::FETCH_ASSOC) or die($conn->error());
        $user = @$result->fetchAll()[0];
        if (password_verify(trim($_POST['password']), $user['PASSWORD'])) {
            
            $_SESSION['userID'] = $user['LOGIN_ID'];
            $_SESSION['email'] = $user['EMAIL'];
            $_SESSION['username'] = $user['USERNAME'];
            $_SESSION['firstName'] = $user['FIRSTNAME'];
            $_SESSION['lastName'] = $user['LASTNAME'];
            $_SESSION['active'] = $user['ACTIVE'];
            $_SESSION['lastLogin'] = date("Y-m-d H:i:s");
            
            // This is how we'll know the user is logged in
            $_SESSION['logged_in'] = true;
            header("location: index.php");
        } else {
            $_SESSION['message'] = "Zadal jsi chybné heslo, zkus to znovu!";
            header("location: login.php?success=wrongpassword");
            exit();
        }
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
                if (@$_GET['success'] == 'wronguser') {
                    echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
                    echo "Uživatel s tímto uživatelským jménem neexistuje!";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                }
                if (@$_GET['success'] == 'wrongpassword') {
                    echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
                    echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
                    echo "Zadáno chybné heslo, zkus to znovu!";
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
					<form name="loginform" action="" method="POST">
						<div class="form-group">
							<label for="username">Uživatelské jméno</label> <input
								type="text" class="form-control" name="username"
								placeholder="Uživatelské jméno" pattern="[A-Za-z0-9\.]{1,30}"
								value="<?php echo htmlspecialchars(@$_POST['username']);?>"
								maxlength="30" required />
						</div>
						<div class="form-group">
							<label for="password">Heslo</label> <input type="password"
								class="form-control" id="password" name="password"
								placeholder="Heslo" maxlength="100" required autocomplete="off" />
						</div>
						<div class="checkbox">
							<label for="rememberme"> <input name="rememberme" type="checkbox">
								Pamatuj si mě
							</label> <label class="pull-right"> <a
								href="forgottenpassword.php">Zapomenuté Heslo?</a>
							</label>

						</div>
						<button type="submit"
							class="btn btn-success btn-flat m-b-30 m-t-30" name="signin">Přihlásit</button>
						<!-- 
						<div class="social-login-content">
							<div class="social-button">
								<button type="button"
									class="btn social facebook btn-flat btn-addon mb-3">
									<i class="ti-facebook"></i>Sign in with facebook
								</button>
								<button type="button"
									class="btn social twitter btn-flat btn-addon mt-2">
									<i class="ti-twitter"></i>Sign in with twitter
								</button>
							</div>
						</div>  -->
						<div class="register-link m-t-15 text-center">
							<p>
								Nemáš ještě účet ? <a href="register.php" title="Registration">
									Registruj se zde</a>
							</p>
						</div>
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
