<?php 

session_start();

session_destroy();

 ?>

<!doctype html> 
<html lang="en">

<!-- Comienza el head -->
<head>
	<meta charset="utf-8" />
	<title>Avaluo!!</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="bootstrap-responsive.css">
	<link rel="stylesheet" href="bootstrap.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<script type="text/javascript" src="effects.js"></script>
	<script type="text/javascript" src="holder.js"></script>

</head>
<!-- Termina el head -->

<!-- Comienza el body -->
<body>
	<!-- Esto es la barra de arriba, que tiene el log-out -->
	<div id="top_bar">
		<img src="OEAE.png" style="width:100px; padding-left:10px;">
		<!-- <a href=""><p id="logout">Logout</p></a> -->
	</div>
	<!-- Termina la barra -->

	<!-- Este wrapper contiene todo lo que esta debajo de la barra -->
	<div id="main_wrapper">

		<!-- Este es el navegador drop down que esta a la izquierda -->
		<div id="nav_drop">
			<!-- Aqui va todo lo que va en la parte izquierda -->
			
					<form name="input" action="no-index.php" method="post" id="login">
						Email: <br><input type = "text" name = "email"> <br>
						Password: <br><input type = "password" name = "passwd"> <br>
						<input type="submit" value="Submit"> 
					</form>
				
					<a href="changepass.php" > <p id = "recovery"> Change/Forgot Password </p></a>
			
		</div>
		<!-- Termina el navegador drop down -->

		<!-- Este es el contenedor que tiene todo lo que va a la derecha del navegador drop down-->
		<div id="main_sub">

			<!-- Esto contiene el outline/title -->
			<div id="top_main">
				<a href="#"><div id="show_hide"><img src="hide-show.png" id="effect"></div></a>

			</div>
			<!-- Termina contenedor-->

			<!-- Esto contiene la informacion principal -->
			<div id="main_content">
			
				<center>
					<h1> OEAE </h1>
					<p id = "placeholder"> 
					Welcome to the OEAE Rubric Website <br>
					This website is a work in progress developed by: <br>
					Christian Rodríguez <br>
					Alex Santos <br>
					Tahirí Laboy <br>
					José Reyes
					</p>

					<img src="UPR.png" style="width:300px; ">
			</div>
  	
 
  </div>
</div>
			</div>
			<!-- Termina contenido principal -->
			
		</div>
		<!-- Termina contenedor-->
	</div>
	<!-- Termina el wrapper -->
</body>
<!-- Termina el body -->
