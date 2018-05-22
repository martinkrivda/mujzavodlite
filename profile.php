<?php
/* Displays user information and some useful messages */
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('db_connection.php');
require_once ('oauth.php');
$loginGUrl = $gClient->createAuthUrl();
// pristup jen pro prihlaseneho uzivatele
require 'userrequired.php';
include_once ('pages/function.php');
if (isset($_SESSION['userID'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM LOGIN WHERE LOGIN_ID = :LOGIN_ID AND ACTIVE = 1 LIMIT 1");
        $stmt->bindParam(":LOGIN_ID", $_SESSION['userID']);
        $stmt->execute();
        $user = @$stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    } catch (PDOException $e) {
        die("Oh noes! There's an error in the query!" . $e->getMessage());
    }
    if (! empty($_POST) && isset($_POST['user']) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $faults = []; // pracovní proměnná, do které budeme shromažďovat info o chybách
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
        if (empty($faults)) {
            try {
                $stmt = $conn->prepare("UPDATE LOGIN SET FIRSTNAME = :FIRSTNAME, LASTNAME = :LASTNAME, EMAIL = :EMAIL WHERE LOGIN_ID = :LOGIN_ID");
                $stmt->bindParam(":FIRSTNAME", $_POST['firstname'], PDO::PARAM_STR);
                $stmt->bindParam(":LASTNAME", $_POST['lastname'], PDO::PARAM_STR);
                $stmt->bindParam(":EMAIL", $_POST['email'], PDO::PARAM_STR);
                $stmt->bindParam(":LOGIN_ID", $_SESSION['userID'], PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                die("Oh noes! There's an error in the query!" . $e->getMessage());
            }
        }
    }
}
try {
    $stmt = $conn->prepare("SELECT VINTAGE_ID, NAME FROM RACEVINTAGE");
    $stmt->execute();
    $racevintages = @$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Oh noes! There's an error in the query!" . $e->getMessage());
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
						<h1>Uživatelský profil</h1>
					</div>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="page-header float-right">
					<div class="page-title">
						<ol class="breadcrumb text-right">
							<li><a href="index.php">Dashboard</a></li>
							<li class="active">Uživatelský profil</li>
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
								<strong class="card-title">Osobní údaje</strong>
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
        if (isset($_GET['googlepair']) && @$_GET['googlepair'] == 'success') {
            echo '<script src="assets/js/sweetalert2.all.js"></script>';
            echo '<script type="text/javascript" language="javascript">';
            echo "swal('Hotovo!','Tvůj účet byl úspěšně spárován s Google!','success');";
            echo '</script>';
        }
        ?>
								<form action="" method="POST" name="user" id="user"
									enctype="multipart/form-data">
									<div class="form-group">
										<label for="firstname" class=" form-control-label">Jméno</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<input class="form-control" type="text" id="firstname"
												name="firstname" placeholder="Vložte křestní jméno"
												pattern="[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}"
												value="<?php echo htmlspecialchars(@$user['FIRSTNAME']);?>"
												maxlength="50" required />
										</div>
										<small class="form-text text-muted">ex. Jan</small>
									</div>
									<div class="form-group">
										<label for="lastname" class=" form-control-label">Příjmení</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-user"></i>
											</div>
											<input class="form-control" type="text" id="lastname"
												name="lastname" placeholder="Vložte příjmení"
												pattern="[a-zA-Z \-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,70}"
												value="<?php echo htmlspecialchars(@$user['LASTNAME']);?>"
												maxlength="70" required />
										</div>
										<small class="form-text text-muted">ex. Novák</small>
									</div>
									<div class="form-group">
										<label for="email" class=" form-control-label">E-mail</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-envelope"></i>
											</div>
											<input class="form-control" type="email" id="email"
												name="email" placeholder="Vložte email"
												value="<?php echo htmlspecialchars(@$user['EMAIL']);?>"
												maxlength="100" required />
										</div>
										<small class="form-text text-muted">ex. jan.novak@gmail.com</small>
									</div>
									<div class="form-group">
										<label for="avatar" class=" form-control-label">Profilová
											fotografie</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-picture-o"></i>
											</div>
											<input class="form-control" type="file" id="avatar"
												name="avatar" placeholder="Vložte profilovou fotografii"
												value="<?php echo htmlspecialchars(@$user['AVATAR']);?>"
												accept=".jpg, .jpeg, .png" />
										</div>
										<small class="form-text text-muted">Vyberte prosím fotografii
											ve formátu .jpeg, .jpg, .png</small>
										<div id="uploaded_image"></div>
									</div>
									<div class="form-group">
										<label for="lastlogin" class=" form-control-label">Poslední
											přihlášení</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-clock-o"></i>
											</div>
											<input class="form-control" type="datetime" id="lastlogin"
												name="lastlogin" placeholder="Poslední přihlášení"
												value="<?php echo htmlspecialchars(@$user['LASTLOGIN']);?>"
												readonly />
										</div>
										<small class="form-text text-muted">ex. 2018-05-21 08:30:00</small>
									</div>
									<button type="submit" id="user" name="user"
										class="btn btn-success btn-sm">
										<i class="fa fa-magic"></i>&nbsp; Uložit
									</button>
									<button type="button" class="btn btn-warning btn-sm"
										onclick="window.location = 'forgottenpassword.php'">
										<i class="fa fa-exchange"></i>&nbsp;Změnit heslo
									</button>
								<?php
        if (@$user['GOOGLE_ID'] == '') {
            ?>
									<button type="button" class="btn btn-danger btn-sm"
										onclick="window.location = '<?php echo $loginGUrl;?>'">
										<i class="fa fa-google-plus"></i>&nbsp;Spárovat účet s Google
									</button>
									<?php
        }
        ?>
							</form>
							</div>
							<div class="modal fade" id="uploadPhoto" tabindex="-1"
								role="dialog" aria-labelledby="uploadPhotoLabel"
								aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="uploadPhotoLabel">Nahrát
												fotografii</h5>
											<button type="button" class="close" data-dismiss="modal"
												aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
											<div class="row">
												<div class="col-md-8 text-center">
													<div id="image_preview"></div>
												</div>
											</div>

										</div>
										<div class="modal-footer">
											<input type="hidden" name="userID" id="userID" value="" /> <input
												type="hidden" name="operation" id="operation" value="upload" />
											<button type="button" class="btn btn-secondary"
												data-dismiss="modal">Cancel</button>
											<button type="submit" id="save" name="save"
												class="btn btn-primary">Confirm</button>
										</div>
										</form>
									</div>
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
					© 2018 Copyright: <a href="https://martinkrivda.cz/">
						martinkrivda.cz </a>
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
	<script src="assets/js/sweetalert2.all.js" async></script>
	<script src="assets/js/croppie.min.js" async></script>

	<script type="text/javascript">
		$( document ).ready(function() {
			$image_crop = $('#image_preview').croppie({
			enableExif: true,
			viewport: {
				width: 200,
				height: 200,
				type: 'circle' //square
			},
			boundary: {
				width: 300,
				height: 300
			}
		});
		$('#avatar').on('change', function(){
			var reader = FileReader();
			reader.onload = function (event){
				$image_crop.croppie('bind', {
					url: event.target.result
				}).then(function(){
					console.log('jQuery bind complete');
				});
			}
			reader.readAsDataURL(this.files[0]);
			$('uploadPhoto').modal('show');
		});
	});
	</script>


</body>
</html>
