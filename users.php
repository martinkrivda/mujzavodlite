<?php
/* Displays user information and some useful messages */
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('db_connection.php');
// pristup jen pro prihlaseneho uzivatele
require 'userrequired.php';
include_once ('pages/function.php');
try {
    $stmt = $conn->prepare("SELECT VINTAGE_ID, NAME FROM RACEVINTAGE");
    $stmt->execute();
    $racevintages = @$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
}
try {
    $stmt = $conn->prepare("SELECT LOGIN_ID, FIRSTNAME, LASTNAME, USERNAME, LASTLOGIN FROM LOGIN");
    $stmt->execute();
    $users = @$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
}
try {
    $stmt = $conn->prepare("SELECT * FROM ROLE");
    $stmt->execute();
    $roles = @$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
}
try {
    $stmt = $conn->prepare("SELECT * FROM USERROLE");
    $stmt->execute();
    $userroles = @$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
if (! empty($_POST) && $_POST['operation'] == 'AddVintage' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
    $_POST['vintagename'] = trim(@$_POST['vintagename']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,70}$/", $_POST['vintagename'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat název ročníku! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 70 znaků.';
    }
    $_POST['vintage'] = trim(@$_POST['vintage']);
    if (! preg_match("/^\d{1,4}$/", $_POST['vintage'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat nčíslo ročníku! Pořadové číslo musí obsahovat 1 až 4 číslice.';
    }
    $_POST['race'] = trim(@$_POST['race']);
    if (! preg_match("/^\d{1,}$/", $_POST['race'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné vybrat závod!';
    }
    $_POST['date'] = date('Y-m-d', strtotime(trim(@$_POST['date'])));
    if (! validateDate($_POST['date'], 'Y-m-d')) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat datum závodu!';
    }
    $_POST['firststart'] = date('H:i', strtotime(trim(@$_POST['firststart'])));
    if (! validateDate($_POST['firststart'], 'H:i')) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat čas startu závodu!';
    }
    $_POST['location'] = trim(@$_POST['location']);
    if (! preg_match("/^[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,50}$/", $_POST['location'])) { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat místo konání závodu! Lokace může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 50 znaků.';
    }
    $_POST['gps'] = trim(@$_POST['gps']);
    if (! preg_match("/^(\-?\d+(\.\d+)?)N?,\s*(\-?\d+(\.\d+)?)E?$/", $_POST['gps']) && @$_POST['gps'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat souřadnice ve správném formátu.';
    }
    $_POST['presentation'] = date('Y-m-d H:i', strtotime(trim(@$_POST['presentation'])));
    if (! validateDate($_POST['presentation'], 'Y-m-d H:i') && @$_POST['presentation'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat čas startu závodu!';
    }
    $_POST['webpage'] = trim(@$_POST['webpage']);
    if (! preg_match("/^(http:|https:)\/\/[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/", @$_POST['webpage']) && @$_POST['webpage'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Webová stránka obsahuje nepovolené znaky!';
    }
    $_POST['entrydate1'] = date('Y-m-d H:i:s', strtotime(trim(@$_POST['entrydate1'])));
    if (! validateDate($_POST['entrydate1'], 'Y-m-d H:i:s') && @$_POST['entrydate1'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat datum přihlášek!';
    }
    $_POST['competition'] = trim(@$_POST['competition']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ\.]{1,155}$/", $_POST['competition']) && @$_POST['competition'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat název soutěže! Soutěž může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 155 znaků.';
    }
    $_POST['eventdirector'] = trim(@$_POST['eventdirector']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}$/", $_POST['eventdirector']) && @$_POST['eventdirector'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno ředitele závodu! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['mainreferee'] = trim(@$_POST['mainreferee']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}$/", $_POST['mainreferee']) && @$_POST['mainreferee'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno hlavního rozhodčího! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['entriesmanager'] = trim(@$_POST['entriesmanager']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,100}$/", $_POST['entriesmanager']) && @$_POST['entriesmanager'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno zpracovatele přihlášek! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['jury1'] = trim(@$_POST['jury1']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", $_POST['jury1']) && @$_POST['jury1'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno JURY! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['jury2'] = trim(@$_POST['jury2']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", $_POST['jury2']) && @$_POST['jury2'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno JURY! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    $_POST['jury3'] = trim(@$_POST['jury3']);
    if (! preg_match("/^[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}$/", $_POST['jury3']) && @$_POST['jury3'] != '') { // preg_match kontroluje pomocí regulárního výrazu
        $faults[] = 'Je nutné zadat jméno JURY! Jméno může obsahovat jen písmena, mezeru a pomlčku a mít délku max. 100 znaků.';
    }
    if (empty($faults)) {
        // pokud nebyly nalezeny chyby, tak uložíme získaná data a provedeme redirect
        if (isset($_POST["operation"])) {
            if ($_POST["operation"] == "AddVintage") {
                try {
                    $stmt = $conn->prepare("INSERT INTO RACEVINTAGE (NAME, VINTAGE, RACE_ID, DATE, FIRSTSTART, LOCATION, GPS, PRESENTATION, WEB, ENTRYDATE1, COMPETITION, EVENTDIRECTOR, MAINREFEREE, ENTRIESMANAGER, JURY1, JURY2, JURY3) VALUES (:NAME, :VINTAGE, :RACE_ID, :DATE, :FIRSTSTART, :LOCATION, :GPS, :PRESENTATION, :WEB, :ENTRYDATE1, :COMPETITION, :EVENTDIRECTOR, :MAINREFEREE, :ENTRIESMANAGER, :JURY1, :JURY2, :JURY3)");
                    $stmt->bindParam(':NAME', $_POST["vintagename"], PDO::PARAM_STR);
                    $stmt->bindParam(':VINTAGE', $_POST["vintage"], PDO::PARAM_INT);
                    $stmt->bindParam(':RACE_ID', $_POST["race"], PDO::PARAM_INT);
                    $stmt->bindParam(':DATE', $_POST["date"], PDO::PARAM_STR);
                    $stmt->bindParam(':FIRSTSTART', $_POST["firststart"], PDO::PARAM_STR);
                    $stmt->bindParam(':LOCATION', $_POST["location"], PDO::PARAM_STR);
                    $stmt->bindParam(':GPS', $_POST["gps"], PDO::PARAM_STR);
                    $stmt->bindParam(':PRESENTATION', $_POST["presentation"], PDO::PARAM_STR);
                    $stmt->bindParam(':WEB', $_POST["webpage"], PDO::PARAM_STR);
                    $stmt->bindParam(':ENTRYDATE1', $_POST["entrydate1"], PDO::PARAM_STR);
                    $stmt->bindParam(':COMPETITION', $_POST["competition"], PDO::PARAM_STR);
                    $stmt->bindParam(':EVENTDIRECTOR', $_POST["eventdirector"], PDO::PARAM_STR);
                    $stmt->bindParam(':MAINREFEREE', $_POST["mainreferee"], PDO::PARAM_STR);
                    $stmt->bindParam(':ENTRIESMANAGER', $_POST["entriesmanager"], PDO::PARAM_STR);
                    $stmt->bindParam(':JURY1', $_POST["jury1"], PDO::PARAM_STR);
                    $stmt->bindParam(':JURY2', $_POST["jury2"], PDO::PARAM_STR);
                    $stmt->bindParam(':JURY3', $_POST["jury3"], PDO::PARAM_STR);
                    $result = $stmt->execute();
                    header("location: races.php?newvintage=success");
                    exit();
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
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
<link rel="stylesheet"
	href="assets/css/lib/datatable/dataTables.bootstrap.min.css">
<!-- <link rel="stylesheet" href="assets/css/bootstrap-select.less"> -->
<link rel="stylesheet" href="assets/scss/style.css">

<link
	href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800'
	rel='stylesheet' type='text/css'>

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

</head>
<body>


	<!-- Left Panel -->

	<aside id="left-panel" class="left-panel">
		<nav class="navbar navbar-expand-sm navbar-default">

			<div class="navbar-header">
				<button class="navbar-toggler" type="button" data-toggle="collapse"
					data-target="#main-menu" aria-controls="main-menu"
					aria-expanded="false" aria-label="Toggle navigation">
					<i class="fa fa-bars"></i>
				</button>
				<a class="navbar-brand" href="./"><img src="images/logo.png"
					alt="Logo"></a> <a class="navbar-brand hidden" href="./"><img
					src="images/logo2.png" alt="Logo"></a>
			</div>

			<div id="main-menu" class="main-menu collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="index.php"> <i
							class="menu-icon fa fa-dashboard"></i>Dashboard
					</a></li>
					<h3 class="menu-title">Závody</h3>
					<!-- /.menu-title -->
					<?php
    foreach ($racevintages as $racevintage) {
        ?>
					<!-- /.menu-title -->
					<li class="menu-item-has-children dropdown"><a href="#"
						class="dropdown-toggle" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"> <i
							class="menu-icon fa fa-laptop"></i><?= htmlspecialchars($racevintage['NAME'])?>
					</a>
						<ul class="sub-menu children dropdown-menu">
							<li><i class="fa fa-puzzle-piece"></i><a
								href="information.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Informace</a></li>
							<li><i class="fa fa-id-badge"></i><a
								href="categories.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Kategorie</a></li>
							<li><i class="fa fa-id-card-o"></i><a
								href="registartions.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Registrace</a></li>
							<li><i class="fa fa-money"></i><a
								href="payments.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Platby</a></li>
							<li><i class="fa fa-share-square-o"></i><a
								href="additionalservices.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Dopňkové
									služby</a></li>
							<li><i class="fa  fa-rss"></i><a
								href="rfidreader.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Čtečka
									RFID</a></li>
							<li><i class="fa fa-list"></i><a
								href="startlists.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Startovní
									listina</a></li>
							<li><i class="fa fa-list-alt"></i><a
								href="resultlists.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Výsledková
									listina</a></li>
							<li><i class="fa fa-microphone"></i><a
								href="speaker.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Komentátor</a></li>
							<li><i class="fa fa-bar-chart-o"></i><a
								href="statistics.php?id=<?= htmlspecialchars($racevintage['VINTAGE_ID'])?>">Statistiky</a></li>
						</ul></li>
						<?php } ?>

					<h3 class="menu-title">Adresář</h3>
					<!-- /.menu-title -->

					<li class="menu-item-has-children dropdown"><a href="#"
						class="dropdown-toggle" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"> <i
							class="menu-icon fa fa-book"></i>Evidence
					</a>
						<ul class="sub-menu children dropdown-menu">
							<li><i class="menu-icon fa fa-user"></i><a href="runners.php">Závodníci</a></li>
							<li><i class="menu-icon fa fa-home"></i><a href="clubs.php">Kluby</a></li>
						</ul></li>
					<li><a href="email.php"> <i class="menu-icon ti-email"></i>E-mail
					</a></li>
					<h3 class="menu-title">Nastavení</h3>
					<!-- /.menu-title -->
					<li class="menu-item-has-children dropdown"><a href="#"
						class="dropdown-toggle" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"> <i
							class="menu-icon fa fa-gears"></i>Předvolby
					</a>
						<ul class="sub-menu children dropdown-menu">
							<li><i class="fa fa-users"></i><a href="users.php">Uživatelé</a></li>
							<li><i class="fa fa-trophy"></i><a href="races.php">Závody</a></li>
							<li><i class="fa fa-tags"></i><a href="tags.php"> RFID čipy</a></li>
							<li><i class="fa fa-wrench"></i><a href="settings.php"> Pokročilé</a></li>
						</ul></li>
					<li class="menu-item"><a href="organiser.php"> <i
							class="menu-icon fa fa-briefcase"></i>Organizátor
					</a></li>
					<li class="menu-item"><a href="newsletters.php"> <i
							class="menu-icon fa fa-bullhorn"></i>Newslettery
					</a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>
	</aside>
	<!-- /#left-panel -->

	<!-- Left Panel -->

	<!-- Right Panel -->

	<div id="right-panel" class="right-panel">

		<!-- Header-->
		<header id="header" class="header">

			<div class="header-menu">

				<div class="col-sm-7">
					<a id="menuToggle" class="menutoggle pull-left"><i
						class="fa fa fa-tasks"></i></a>
					<div class="header-left">
						<button class="search-trigger">
							<i class="fa fa-search"></i>
						</button>
						<div class="form-inline">
							<form class="search-form">
								<input class="form-control mr-sm-2" type="text"
									placeholder="Vyhledat ..." aria-label="Search">
								<button class="search-close" type="submit">
									<i class="fa fa-close"></i>
								</button>
							</form>
						</div>

						<div class="dropdown for-notification">
							<button class="btn btn-secondary dropdown-toggle" type="button"
								id="notification" data-toggle="dropdown" aria-haspopup="true"
								aria-expanded="false">
								<i class="fa fa-bell"></i> <span class="count bg-danger">5</span>
							</button>
							<div class="dropdown-menu" aria-labelledby="notification">
								<p class="red">You have 3 Notification</p>
								<a class="dropdown-item media bg-flat-color-1" href="#"> <i
									class="fa fa-check"></i>
									<p>Server #1 overloaded.</p>
								</a> <a class="dropdown-item media bg-flat-color-4" href="#"> <i
									class="fa fa-info"></i>
									<p>Server #2 overloaded.</p>
								</a> <a class="dropdown-item media bg-flat-color-5" href="#"> <i
									class="fa fa-warning"></i>
									<p>Server #3 overloaded.</p>
								</a>
							</div>
						</div>

						<div class="dropdown for-message">
							<button class="btn btn-secondary dropdown-toggle" type="button"
								id="message" data-toggle="dropdown" aria-haspopup="true"
								aria-expanded="false">
								<i class="ti-email"></i> <span class="count bg-primary">9</span>
							</button>
							<div class="dropdown-menu" aria-labelledby="message">
								<p class="red">You have 4 Mails</p>
								<a class="dropdown-item media bg-flat-color-1" href="#"> <span
									class="photo media-left"><img alt="avatar"
										src="images/avatar/1.jpg"></span> <span
									class="message media-body"> <span class="name float-left">Martin
											Křivda</span> <span class="time float-right">Just now</span>
										<p>Hello, this is an example msg</p>
								</span>
								</a> <a class="dropdown-item media bg-flat-color-4" href="#"> <span
									class="photo media-left"><img alt="avatar"
										src="images/avatar/2.jpg"></span> <span
									class="message media-body"> <span class="name float-left">Martin
											Křivda</span> <span class="time float-right">5 minutes ago</span>
										<p>Lorem ipsum dolor sit amet, consectetur</p>
								</span>
								</a> <a class="dropdown-item media bg-flat-color-5" href="#"> <span
									class="photo media-left"><img alt="avatar"
										src="images/avatar/3.jpg"></span> <span
									class="message media-body"> <span class="name float-left">Pavel
											Švadlena</span> <span class="time float-right">10 minutes ago</span>
										<p>Hello, this is an example msg</p>
								</span>
								</a> <a class="dropdown-item media bg-flat-color-3" href="#"> <span
									class="photo media-left"><img alt="avatar"
										src="images/avatar/4.jpg"></span> <span
									class="message media-body"> <span class="name float-left">Bedřich
											Tvrdohlavý</span> <span class="time float-right">15 minutes
											ago</span>
										<p>Lorem ipsum dolor sit amet, consectetur</p>
								</span>
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-5">
					<div class="user-area dropdown float-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false"> <img
							class="user-avatar rounded-circle" src="images/admin.jpg"
							alt="User Avatar">
						</a>

						<div class="user-menu dropdown-menu">
							<a class="nav-link" href="profile.php"><i class="fa fa- user"></i>Můj
								profil</a> <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications
								<span class="count">13</span></a> <a class="nav-link" href="#"><i
								class="fa fa -cog"></i>Settings</a> <a class="nav-link"
								href="logout.php"><i class="fa fa-power -off"></i>Odhlásit se</a>
						</div>
					</div>

					<div class="language-select dropdown" id="language-select">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown"
							id="language" aria-haspopup="true" aria-expanded="true"> <i
							class="flag-icon flag-icon-cz"></i>
						</a>
						<div class="dropdown-menu" aria-labelledby="language">
							<div class="dropdown-item">
								<span class="flag-icon flag-icon-fr"></span>
							</div>
							<div class="dropdown-item">
								<i class="flag-icon flag-icon-de"></i>
							</div>
							<div class="dropdown-item">
								<i class="flag-icon flag-icon-gb"></i>
							</div>
							<div class="dropdown-item">
								<i class="flag-icon flag-icon-us"></i>
							</div>
						</div>
					</div>

				</div>
			</div>

		</header>
		<!-- /header -->
		<!-- Content-->

		<div class="breadcrumbs">
			<div class="col-sm-4">
				<div class="page-header float-left">
					<div class="page-title">
						<h1>Uživatelská práva</h1>
					</div>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="page-header float-right">
					<div class="page-title">
						<ol class="breadcrumb text-right">
							<li><a href="index.php">Dashboard</a></li>
							<li><a href="#">Nastavení</a></li>
							<li class="active">Uživatelé</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<div class="content mt-3">
			<div class="animated fadeIn">
				<div class="row">

					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<strong class="card-title">Uživatelé</strong>
							</div>
							<div class="card-body">
								<?php
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
        if (isset($_GET['newvintage']) && @$_GET['newvintage'] == 'success') {
            echo '<script src="assets/js/sweetalert2.all.js"></script>';
            echo '<script type="text/javascript" language="javascript">';
            echo "swal('Hotovo!','Uživatel byl úspěšně přidán do databáze!','success');";
            echo '</script>';
        }
        ?>
								<span id="result"></span>
								<!-- Button trigger modal -->
								<button type="button" id="addUserBtn"
									class="btn btn-secondary mb-1" onclick="window.location = 'register.php'">
									<i class="fa fa-plus"></i> Přidat uživatele
								</button>
								<!-- Table with runners -->
								<div class="table-responsive">
									<table id="users-table" class="table table-striped table-hover">
										<thead>
											<tr class="tableheader">
												<th>ID</th>
												<th>Uživatelské jméno</th>
												<th>Jméno</th>
												<?php foreach($roles as $role) {?>
												<th id="<?= htmlspecialchars($role['ROLE_ID']) ?>"><?= ucfirst (strtolower(htmlspecialchars($role['ROLEDESCRIPTION']))) ?></th>
												<?php } ?>
												<th>Poslední přihlášení</th>
											</tr>
										</thead>
										<tbody id="tbodyuser">
											<?php foreach($users as $user) {?>
												<tr>
												<td><?= htmlspecialchars($user['LOGIN_ID']) ?></td>
												<td><?= htmlspecialchars($user['USERNAME']) ?></td>
												<td><?= htmlspecialchars($user['LASTNAME']) ?> <?= htmlspecialchars($user['FIRSTNAME']) ?></td>
												<?php foreach($roles as $role) {?>
													<?php foreach($userroles as $userrole) {
														if ($userrole['ROLE_ID'] == $role['ROLE_ID'] && $userrole['LOGIN_ID'] == $user['LOGIN_ID']){
														
														?>
														<td><label class="switch switch-text switch-info switch-pill"><input
								type="checkbox" class="switch-input" checked="true"> <span
								data-on="On" data-off="Off" class="switch-label"></span> <span
								class="switch-handle"></span></label></td>

												<?php }else{

													?>
													<td><label class="switch switch-text switch-info switch-pill"><input
								type="checkbox" class="switch-input" checked="false"> <span
								data-on="On" data-off="Off" class="switch-label"></span> <span
								class="switch-handle"></span></label></td>
												<?php }}?>
												<?php } ?>
												<td><?= htmlspecialchars($user['LASTLOGIN']) ?></td>
											</tr>
												<?php } ?>
										</tbody>
									</table>
									<div id="output"></div>
								</div>
							</div>

						</div>
					</div>


				</div>
			</div>
			<!-- .animated -->
			<!--Footer-->
			<footer class="page-footer"
				style="text-align: center; position: fixed; z-index: 9999999; bottom: 0; width: 100%; cursor: pointer; line-height: 0; display: block !important;">

				<!--Copyright-->
				<div class="footer-copyright py-3 text-center">
					© 2018 Copyright: <a
						href="https://mdbootstrap.com/material-design-for-bootstrap/">
						MDBootstrap.com </a>
				</div>
				<!--/.Copyright-->

			</footer>
			<!--/.Footer-->


		</div>
		<!-- .content -->
	</div>
	<!-- /#right-panel -->

	<!-- Right Panel -->


	<!-- <script src="assets/js/vendor/jquery-2.1.4.min.js"></script> -->
	<script src="assets/js/vendor/jquery-3.3.1.js"></script>


	<script src="assets/js/main.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/plugins.js"></script>


	<script src="assets/js/lib/data-table/datatables.min.js"></script>
	<script src="assets/js/jquery.tabledit.js"></script>

	<script src="assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>


	<script src="assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>

	<script src="assets/js/lib/data-table/dataTables.buttons.min.js"></script>
	<script src="assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
	<script src="assets/js/lib/data-table/jszip.min.js"></script>
	<script src="assets/js/lib/data-table/pdfmake.min.js"></script>
	<script src="assets/js/lib/data-table/vfs_fonts.js"></script>
	<script src="assets/js/lib/data-table/buttons.html5.min.js"></script>
	<script src="assets/js/lib/data-table/buttons.print.min.js"></script>
	<script src="assets/js/lib/data-table/buttons.colVis.min.js"></script>
	<script src="assets/js/lib/data-table/datatables-init.js"></script>
	<script src="assets/js/sweetalert2.all.js"></script>
</body>
</html>
