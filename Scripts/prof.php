<?php  
echo "<div id='content'>
	<center>
	<p>Enter Professor's information</p>
	<form method='post' action='newprof.php'>
		Nombre del Professor: <br>
		<input type='text' name='profname'> <br>
		Email: <br>
		<input type='text' name='profemail'> <br>
		Departamento: <br>
		<input type='text' name='profdept'> <br>
		Facultad: <br>
		<input type='text' name='proffaculty'> <br>
		<br>
		<input type='submit'>
	</form>
	</center>
</div>

		<script type='text/javascript'>

	$('form').submit( function() {
			var data = $(this).serialize();
			var url = 'http://ada.uprrp.edu/~chrodriguez/oeae/Scripts/newprof.php?'+data;
			console.log(data);

		$.get(url, function(html) {
				alert('Professor in Database. Password: '+html)
			})

		return false;
		});
		</script>

";

?>