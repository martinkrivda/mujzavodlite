<?php
require_once ('db_connection.php');
/* Reset your password form, sends reset.php password link */
$faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
$config = parse_ini_file(__DIR__ . '/config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
$environment = $config['instance']['test'];
if (! $environment == 'test') {
    $app = 'mujzavod';
} else {
    $app = 'mujzavodtest';
}
session_start();
date_default_timezone_set('Europe/Prague');
// Check if form submitted with method="post"
if (! empty($_POST) && isset($_POST['forgot']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['email'] = trim(@$_POST['email']);
    if ($_POST['email'] != '' && ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // filter_var umožňuje jednoduchou kontrolu základních typů dat (čísla, mail, url atp.)
        $faults[] = 'Zadaný e-mail není platný!';
    }
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak zašleme odkaz na obnovu hesla
        try {
            $email = $conn->quote($_POST['email'], PDO::FETCH_ASSOC);
            $result = $conn->query("SELECT * FROM LOGIN WHERE EMAIL=$email;", PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Jejda, něco se porouchalo. " . $e->getMessage());
        }
        if ($result->fetchColumn() == 0) // User doesn't exist
        {
            $_SESSION['message'] = "Uživatel s tímto emailem neexistuje!";
            header("location: forgottenpassword.php?success=wronguser");
        } else { // User exists (num_rows != 0)
            $result = $conn->query("SELECT * FROM LOGIN WHERE EMAIL=$email;", PDO::FETCH_ASSOC);
            $user = $result->fetchAll(PDO::FETCH_ASSOC)[0]; // $user becomes array with user data
            
            $email = $user['EMAIL'];
            $hash = $user['HASH'];
            $firstName = $user['FIRSTNAME'];
            
            // Session message to display on success.php
            $_SESSION['message'] = "<p>Na Váš e-mail <span>$email</span>" . " vám byl zaslán potvrzovací odkaz pro dokončení resetu hesla!</p>";
            
            // Send registration confirmation link (reset.php)
            $to = $email;
            $subject = 'Obnova hesla ( MůjZávod | ChytrýOddíl )';
            $message_body = '
        Ahoj ' . $firstName . ',
            
        Požadoval jsi reset hesla!
            
        Prosím klikni na tento odkaz pro dokončení obnovy hesla:
        http://www.martinkrivda.cz/chytryoddil/' . $app . '/resetpassword.php?email=' . $email . '&hash=' . $hash;
            $headers = "From: chytryoddil@martinkrivda.cz";
            
            mail($to, $subject, $message_body, $headers);
            
            header("location: success.php");
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
                    echo "Uživatel s tímto e-mailem neexistuje!";
                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                }
            }
            if (! empty($faults)) {
                echo "<div class='sufee-alert alert with-close alert-danger alert-dismissible fade show'>";
                echo "<span class='badge badge-pill badge-danger'>Chyba</span>";
                echo $faults[0];
                echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
                echo "</div>";
            }
            ?>
                <div class="login-logo">
					<a href="index.html"> <img class="align-content"
						src="images/logo.png" alt="">
					</a>
				</div>
				<div class="login-form">
					<form name="forgottenpassword" action="" method="POST">
						<div class="form-group">
							<label for="email">E-mail</label> <input type="email"
								class="form-control" name="email" id="email"
								placeholder="E-mail"
								value="<?php echo htmlspecialchars(@$_POST['email']);?>"
								maxlength="100" required />
						</div>
						<button type="submit"
							class="btn btn-success btn-flat m-b-30 m-t-30" name="forgot">Resetovat
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
