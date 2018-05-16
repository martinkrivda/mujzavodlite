<?php
/* Displays user information and some useful messages */
session_start();
date_default_timezone_set('Europe/Prague');
require_once ('db_connection.php');
// pristup jen pro prihlaseneho uzivatele
require 'userrequired.php';

include_once ('pages/function.php');

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
					<li class="menu-item-has-children dropdown"><a href="#"
						class="dropdown-toggle" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"> <i
							class="menu-icon fa fa-laptop"></i>26. ročník MCVV
					</a>
						<ul class="sub-menu children dropdown-menu">
							<li><i class="fa fa-puzzle-piece"></i><a href="ui-buttons.html">Informace</a></li>
							<li><i class="fa fa-id-badge"></i><a href="ui-badges.html">Kategorie</a></li>
							<li><i class="fa fa-id-card-o"></i><a href="ui-tabs.html">Registrace</a></li>
							<li><i class="fa fa-money"></i><a href="ui-tabs.html">Platby</a></li>
							<li><i class="fa fa-share-square-o"></i><a
								href="ui-social-buttons.html">Dopňkové služby</a></li>
							<li><i class="fa  fa-rss"></i><a href="rfidreader.php">Čtečka
									RFID</a></li>
							<li><i class="fa fa-list"></i><a href="ui-cards.html">Startovní
									listina</a></li>
							<li><i class="fa fa-list-alt"></i><a href="ui-alerts.html">Výsledková
									listina</a></li>
							<li><i class="fa fa-microphone"></i><a href="ui-progressbar.html">Komentátor</a></li>
							<li><i class="fa fa-bar-chart-o"></i><a href="ui-modals.html">Statistiky</a></li>
						</ul></li>

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
							<a class="nav-link" href="#"><i class="fa fa- user"></i>My
								Profile</a> <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications
								<span class="count">13</span></a> <a class="nav-link" href="#"><i
								class="fa fa -cog"></i>Settings</a> <a class="nav-link"
								href="logout.php"><i class="fa fa-power -off"></i>Logout</a>
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
						<h1>Evidence klubů</h1>
					</div>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="page-header float-right">
					<div class="page-title">
						<ol class="breadcrumb text-right">
							<li><a href="index.php">Dashboard</a></li>
							<li><a href="#">Adresář</a></li>
							<li class="active">Kluby</li>
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
								<strong class="card-title">Kluby</strong>
							</div>
							<div class="card-body">
								<span id="result"></span>
								<!-- Button trigger modal -->
								<button type="button" id="addClubBtn"
									class="btn btn-secondary mb-1" data-toggle="modal"
									data-target="#addClub">
									<i class="fa fa-plus"></i> Přidat klub
								</button>
								<!-- Table with runners -->
								<div class="table-responsive">
									<table id="clubs-table"
										class="table table-striped table-bordered table-hover">
										<thead>
											<tr class="tableheader">
												<th>ID</th>
												<th>Název</th>
												<th>Zkratka</th>
												<th>Ulice</th>
												<th>Město</th>
												<th>PSČ</th>
												<th>Země</th>
												<th>IČO</th>
												<th>DIČ</th>
												<th>Web</th>
												<th>E-mail</th>
												<th>Telefon</th>
												<th width="5%">Upravit</th>
												<th width="5%">Smazat</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
									<div id="output"></div>
								</div>
							</div>
							<div class="modal fade" id="addClub" tabindex="-1" role="dialog"
								aria-labelledby="addClubLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="addClubLabel">Přidat klub</h5>
											<button type="button" class="close" data-dismiss="modal"
												aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<form name="addClubForm" id="addClubForm" action=""
											method="POST" enctype="multipart/form-data">
											<div class="modal-body">
												<div class="row">
													<div class="col-5">
														<div class="form-group">
															<label for="clubname" class=" form-control-label">Název
																klubu</label> <input type="text" id="clubname"
																name="clubname" placeholder="Vložte název klubu"
																pattern="[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,70}"
																value="<?php echo htmlspecialchars(@$_POST['clubname']);?>"
																class="form-control" maxlength="70" required />
														</div>
													</div>
													<div class="col-4">
														<div class="form-group">
															<label for="clubname2" class=" form-control-label">Druhý
																název</label><input type="text" id="clubname2"
																name="clubname2" placeholder="Vložte druhý název"
																pattern="[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ]{1,50}"
																value="<?php echo htmlspecialchars(@$_POST['clubname2']);?>"
																class="form-control" maxlength="50" />
														</div>
													</div>
													<div class="col-3">
														<div class="form-group">
															<label for="clubcode" class=" form-control-label">Zkratka</label><input
																type="text" id="clubcode" name="clubcode"
																placeholder="Vložte zkratku"
																pattern="[a-zA-Z\-0-9]{3,10}"
																value="<?php echo htmlspecialchars(@$_POST['clubcode']);?>"
																class="form-control" maxlength="10" required />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-5">
														<div class="form-group">
															<label for="street" class=" form-control-label">Ulice</label><input
																type="text" id="street" name="street"
																placeholder="Vložte ulici"
																pattern="[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,30}"
																value="<?php echo htmlspecialchars(@$_POST['street']);?>"
																class="form-control" maxlength="30" />
														</div>
													</div>
													<div class="col-4">
														<div class="form-group">
															<label for="city" class=" form-control-label">Město</label><input
																type="text" id="city" name="city"
																placeholder="Vložte město"
																pattern="[a-zA-Z \.\-ěščřžýáíéóúůďťňĎŇŤŠČŘŽÝÁÍÉÚŮ0-9]{1,30}"
																value="<?php echo htmlspecialchars(@$_POST['city']);?>"
																class="form-control" maxlength="30" />
														</div>
													</div>
													<div class="col-3">
														<div class="form-group">
															<label for="zip" class="form-control-label">PSČ</label><input
																type="text" id="zip" name="zip" pattern="\d{3} ?\d{2}"
																placeholder="Vložte PSČ"
																value="<?php echo htmlspecialchars(@$_POST['zip']);?>"
																class="form-control" maxlength="6" />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-8">
														<div class="form-group">
															<label for="email" class="form-control-label">E-mail</label><input
																type="email" id="email" name="email"
																placeholder="Vložte email"
																value="<?php echo htmlspecialchars(@$_POST['email']);?>"
																class="form-control" maxlength="100" />
														</div>
													</div>
													<div class="col-4">
														<div class="form-group">
															<label for="phone" class="form-control-label">Telefon</label><input
																type="tel" id="phone" name="phone"
																placeholder="Vložte telefon"
																value="<?php echo htmlspecialchars(@$_POST['phone']);?>"
																class="form-control" maxlength="13" />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-6">
														<div class="form-group">
															<label for="taxid" class="form-control-label">IČO</label><input
																type="text" id="taxid" name="taxid"
																placeholder="Vložte IČO" pattern="^\d{8}$"
																value="<?php echo htmlspecialchars(@$_POST['taxid']);?>"
																class="form-control" maxlength="8" />
														</div>
													</div>
													<div class="col-6">
														<div class="form-group">
															<label for="vatid" class="form-control-label">DIČ</label><input
																type="text" id="vatid" name="vatid"
																placeholder="Vložte DIČ" pattern="^(CZ|SK)\d{8}$"
																value="<?php echo htmlspecialchars(@$_POST['vatid']);?>"
																class="form-control" maxlength="10" />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-5">
														<div class="form-group">
															<label for="country" class="form-control-label">Země</label>
															<select name="country" id="country"
																style="height: calc(2.25rem + 2px);"
																class="form-control">
																<option value="0">Prosím vyberte</option>
																<?php echo fill_country(); ?>
														</select>
														</div>
													</div>
													<div class="col-5">
														<div class="form-group">
															<label for="webpage" class=" form-control-label">Web</label><input
																type="text" id="webpage" name="webpage"
																placeholder="Vložte webovou stránku"
																pattern="^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$"
																value="<?php echo htmlspecialchars(@$_POST['webpage']);?>"
																class="form-control" maxlength="50" />
														</div>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<input type="hidden" name="club_id" id="club_id" value="" />
												<input type="hidden" name="operation" id="operation"
													value="Add" />
												<button type="button" class="btn btn-secondary"
													data-dismiss="modal">Zrušit</button>
												<button type="submit" class="btn btn-primary">Uložit</button>
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



	<script type="text/javascript" language="javascript">
        $(document).ready(function(){     
        	$('#addClubBtn').click(function(){
        		  $('#addClubForm')[0].reset();
        		  $('.modal-title').text("Přidat klub");
        		  $('#action').val("Add");
        		  $('#operation').val("Add");
        		 });
         
        	var dataTable = $('#clubs-table').DataTable({
                "processing":true,
                "serverSide":true,
                "order":[],
                "ajax":{
                 url:"pages/clubfetch_ajax.php",
                 type:"POST"
                },
                "columnDefs":[
              	   {
              	    "targets":[12, 13],
              	    "orderable":false,
              	   },
              	  ],
              
               });
        
         $(document).on('submit', '#addClubForm', function(event){
          event.preventDefault();
          var clubName = $('#clubname').val();
          var clubName2 = $('#clubname2').val();
          var clubCode = $('#clubcode').val();
          var street = $('#street').val();
          var city = $('#city').val();
          var zip = $('#zip').val();
          var email = $('#email').val();
          var phone = $('#phone').val();
          var taxid = $('#taxid').val();
          var vatid = $('#vatid').val();
          var country = $('#country').val();
          var webpage = $('#webpage').val();
          if(clubName != '' && clubCode != '')
          {
           $.ajax({
            url:"pages/clubinsert_ajax.php",
            method:'POST',
            data:new FormData(this),
            contentType:false,
            processData:false,
            success:function(data)
            {
        		$('#result').html(data);
        		$("#result").delay(2400).fadeOut("slow");
             $('#addClubForm')[0].reset();        
             $('#addClub').modal('hide');
             $("[data-dismiss=modal]").trigger({ type: "click" });
             dataTable.ajax.reload();
            }
           });
          }
          else
          {
           alert("Fields are Required");
          }
         });
         
         $(document).on('click', '.update', function(){
          var club_id = $(this).attr("id");
          $.ajax({
           url:"pages/clubfetch_single.php",
           method:"POST",
           data:{club_id:club_id},
           dataType:"json",
           success:function(data)
           {
            $('#addClub').modal('show');
            $('#clubname').val(data.clubname);
            $('#clubname2').val(data.clubname2);
            $('#clubcode').val(data.clubcode);
            $('#street').val(data.street);
            $('#city').val(data.city);
            $('#zip').val(data.zip);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#taxid').val(data.taxid);
            $('#vatid').val(data.vatid);
            $('#country').val(data.country_code);
            $('#webpage').val(data.webpage);
            $('.modal-title').text("Upravit klub");
            $('#club_id').val(club_id);
            $('#action').val("Edit");
            $('#operation').val("Edit");
           }
          })
         });
         
         $(document).on('click', '.delete', function(e){
          var club_id = $(this).attr("id");
          SwalDelete(club_id);
          e.preventDefault();
         });
        });
         function SwalDelete(club_id){
             swal({
                 title: 'Odstranit klub?',
                 text: "Odstranit klub s ID: "+club_id+" ?",
                 type: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#3085d6',
                 cancelButtonColor: '#d33',
                 confirmButtonText: 'Odstranit',
                 showLoaderOnConfirm: true,
    
                preConfirm: function() {
                    return new Promise(function(resolve){
                    	$.ajax({
                            url:'pages/clubdelete_ajax.php',
                            method:'POST',
                            data:{club_id:club_id},
                            dataType: 'json'
                    	})
                    	.done(function(response){
                        	swal('Smazáno',response.message, response.status)
                        	$('#clubs-table').DataTable().ajax.reload();
                        	
                    	})
                    	.fail(function(){
                        	swal('Oops...', 'Something went wrong with ajax !', 'error');
                    	});
                });
            },
            allowOutsideClick: false
         });
        }
    </script>
</body>
</html>
