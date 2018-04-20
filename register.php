<?php
/*
 * Registration process, inserts user info into the database
 * and sends account confirmation email message
 */
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('db_connection.php');
require_once ('recaptchalib.php');
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
$config = parse_ini_file(__DIR__ . '/config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
$environment = $config['instance']['test'];
if (! $environment == 'test') {
    $app = 'mujzavod';
} else {
    $app = 'mujzavodtest';
}
$siteKey = trim($config['reCaptcha']['siteKey']);
$secret = trim($config['reCaptcha']['secret']);
$lang = trim($config['reCaptcha']['lang']);
// The response from reCAPTCHA
$resp = null;
// The error code from reCAPTCHA, if any
$error = null;
if (! empty($_POST) && isset($_POST['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST['username'] = trim(@$_POST['username']);
    if (! preg_match("/^[a-zA-Z0-9\-\.]{1,30}$/", $_POST['username'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat uživatelské jméno! Uživatelské jméno může obsahovat  písmena a číslice, pomlčku, tečku a mít délku maximálně 30 znaků.';
    }
    $_POST['firstname'] = trim(@$_POST['firstname']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", $_POST['firstname'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 50 znaků.';
    }
    $_POST['lastname'] = trim(@$_POST['lastname']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}$/", $_POST['lastname'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat příjmení! Příjmení může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['email'] = trim(@$_POST['email']);
    if ($_POST['email'] != '' && ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // filter_var umožňuje jednoduchou kontrolu základních typů dat (čísla, mail, url atp.)
        $faults[] = 'Zadaný e-mail není platný!';
    }
    $_POST['password'] = trim(@$_POST['password']);
    $_POST['passwordcheck'] = trim(@$_POST['passwordcheck']);
    if ($_POST['password'] !== $_POST['passwordcheck']) {
        $faults[] = 'Zadané heslo se neshoduje!';
    }
    $reCaptcha = new ReCaptcha($secret);
    // Was there a reCAPTCHA response?
    if ($_POST["g-recaptcha-response"]) {
        $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
    }
    if ($resp != null && $resp->success) {} else {
        $faults[] = 'Ověření reCaptcha selhalo, zkus to znovu!';
    }
    
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak uložíme získaná data a provedeme redirect
        
        // Set session variables to be used on profile.php page
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['firstName'] = $_POST['firstname'];
        $_SESSION['lastName'] = $_POST['lastname'];
        
        // Escape all $_POST variables to protect against SQL injections
        $firstName = $_POST['firstname'];
        $lastName = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $hash = md5(rand(0, 1000));
        
        // Check if user with that email already exists
        $result_email = $conn->query("SELECT * FROM LOGIN WHERE EMAIL='$email'", PDO::FETCH_ASSOC) or die($conn->error());
        $result_username = $conn->query("SELECT * FROM LOGIN WHERE USERNAME='$username'", PDO::FETCH_ASSOC) or die($conn->error());
        
        // We know user email exists if the rows returned are more than 0
        if ($result_email->fetchColumn() > 0) {
            
            $_SESSION['message'] = 'Uživatel s touto e-mailovou adresu již existuje!';
            header("location: register.php?success=emailexist");
        } elseif ($result_username->fetchColumn() > 0) {
            $_SESSION['message'] = 'Uživatel s tímto uživatelským jménem již existuje!';
            header("location: register.php?success=usernameexist");
        } else { // Email and usernam don't already exist in a database, proceed...
            try {
                // active is 0 by DEFAULT (no need to include it here)
                // Add user to the database
                $stmt = $conn->prepare("INSERT INTO LOGIN (USERNAME, PASSWORD, FIRSTNAME, LASTNAME, EMAIL, HASH) VALUES (:USERNAME, :PASSWORD, :FIRSTNAME, :LASTNAME, :EMAIL, :HASH)");
                $stmt->bindParam(':USERNAME', $username, PDO::PARAM_STR);
                $stmt->bindParam(':PASSWORD', $password, PDO::PARAM_STR);
                $stmt->bindParam(':FIRSTNAME', $firstName, PDO::PARAM_STR);
                $stmt->bindParam(':LASTNAME', $lastName, PDO::PARAM_STR);
                $stmt->bindParam(':EMAIL', $email, PDO::PARAM_STR);
                $stmt->bindParam(':HASH', $hash);
                $stmt->execute();
                
                $_SESSION['active'] = 0; // 0 until user activates their account with verify.php
                $_SESSION['logged_in'] = true; // So we know the user has logged in
                $_SESSION['message'] = "Confirmation link has been sent to $email, please verify
                 your account by clicking on the link in the message!";
                
                // Send registration confirmation link (verify.php)
                $to = $email;
                $subject = 'Dokončení registrace ( MůjZávod | ChytrýOddíl )';
                $message_body = '
        Ahoj ' . $firstName . ',

        Děkuji ti za tvoji registraci do aplikace MůjZávod!

        K aktivaci účtu prosím klikni na tento odkaz:

        http://www.martinkrivda.cz/chytryoddil/' . $app . '/verify.php?email=' . $email . '&hash=' . $hash;
                $headers = "From: chytryoddil@martinkrivda.cz";
                
                mail($to, $subject, $message_body, $headers);
                
                header("location: profile.php?success=ok");
                exit();
            } catch (PDOException $e) {
                echo ("Error: " . $e->getMessage());
                $_SESSION['message'] = 'Registration failed!' . $e->getMessage();
                header("location: register.php?succes=failed");
            }
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
<script src='https://www.google.com/recaptcha/api.js'></script>

</head>
<body class="bg-dark">


	<div class="sufee-login d-flex align-content-center flex-wrap">
		<div class="container">
			<div class="login-content">
			<?php
if (isset($_GET['success'])) {
    if (@$_GET['success'] == 'emailexist') {
        echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
        echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
        echo "Uživatel s touto e-mailovou adresou již existuje!";
        echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        echo "<span aria-hidden='true'>&times;</span>";
        echo "</button>";
        echo "</div>";
    }
    if (@$_GET['success'] == 'usernameexist') {
        echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
        echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
        echo "Uživatel s tímto uživatelským jménem již existuje!";
        echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        echo "<span aria-hidden='true'>&times;</span>";
        echo "</button>";
        echo "</div>";
    }
    if (@$_GET['success'] == 'failed') {
        echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
        echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
        echo $_SESSION['message'];
        echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        echo "<span aria-hidden='true'>&times;</span>";
        echo "</button>";
        echo "</div>";
    }
}
if (! empty($faults)) {
    foreach ($faults as $fault) {
        echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
        echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
        echo $fault;
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
					<form name="registration" id="registration" action="" method="POST">
						<div class="form-group">
							<label for="username">Uživatelské jméno</label> <input
								type="text" id="username" name="username" class="form-control"
								placeholder="Uživatelské jméno" pattern="[A-Za-z0-9\.\-]{1,30}"
								value="<?php echo htmlspecialchars(@$_POST['username']);?>"
								maxlength="30" required />
						</div>
						<div class="form-group">
							<label for="firstname">Křestní jméno</label> <input type="text"
								id="firstname" name="firstname" class="form-control"
								placeholder="Křestní jméno"
								pattern="[a-zA-Z -ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}"
								value="<?php echo htmlspecialchars(@$_POST['firstname']);?>"
								maxlength="50" required />
						</div>
						<div class="form-group">
							<label for="lastname">Příjmení</label> <input type="text"
								id="lastname" name="lastname" class="form-control"
								placeholder="Příjmení"
								pattern="[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}"
								value="<?php echo htmlspecialchars(@$_POST['lastname']);?>"
								maxlength="100" required />
						</div>
						<div class="form-group">
							<label for="email">E-mail</label> <input type="email" id="email"
								name="email" class="form-control" placeholder="E-mail"
								value="<?php echo htmlspecialchars(@$_POST['email']);?>"
								maxlength="100" required />
						</div>
						<div class="form-group">
							<label for="password">Heslo</label> <input type="password"
								id="password" name="password" class="form-control"
								placeholder="Heslo" maxlength="100" required autocomplete="off" />
						</div>
						<div class="form-group">
							<label for="passwordcheck">Heslo znovu</label> <input
								type="password" class="form-control" name="passwordcheck"
								id="passwordcheck" placeholder="Heslo znovu" maxlength="100"
								required autocomplete="off" />
						</div>
						<div class="checkbox">
							<label for="agree"> <input type="checkbox" id="agree"
								name="agree" required /> Souhlasím s podmínkami
							</label>
							<div class="g-recaptcha" data-sitekey="<?php echo $siteKey;?>"></div>
						</div>
						<button type="submit"
							class="btn btn-primary btn-flat m-b-30 m-t-30" name="register"
							id="register">Registrovat</button>
						<!-- 
						<div class="social-login-content">
							<div class="social-button">
								<button type="button"
									class="btn social facebook btn-flat btn-addon mb-3">
									<i class="ti-facebook"></i>Register with facebook
								</button>
								<button type="button"
									class="btn social twitter btn-flat btn-addon mt-2">
									<i class="ti-twitter"></i>Register with twitter
								</button>
							</div>
						</div> -->
						<div class="register-link m-t-15 text-center">
							<p>
								Již vlastníš účet ? <a href="login.php" title="Sign in">
									Přihlásit se</a>
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