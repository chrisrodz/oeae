<?php 

/*Christian A. Rodriguez Encarnacion
Por ahora este codigo lo que hace es deslplegar los estudiantes del
primer curso, pero cuando Alex termine el display de los estudiantes
lo hago mas dinamico.*/


session_start();

// Connect to the Database
$db = mysql_connect("localhost","nosotros","oeaeavaluo2013");

// Choose the database 'Avaluo'
if ($db) {
  mysql_select_db("Avaluo");
} else {
  echo "no salio";
  exit();
}

$curso_id = $_GET['course_id'];

// Select students query
$studentquery = "SELECT nombre_est, est_id FROM Estudiantes natural join Matricula where curso_id=$curso_id";
$students = mysql_query($studentquery);

$content='
				<div id="content"><center>
					<table id="thumb0">
						<tr>
							<td>
					<ul class="thumbnails">
					  <li id="thumb1">
					    <a href="#" class="thumbnail" >
					    	<img src="http://api.webthumbnail.org?width=275&height=175&screen=1280&format=png&url=http://renewable.uprrp.edu" alt="Captured by webthumbnail.org" />
							<h3>Rubrics</h3>
					     </a>
					  </li>
					   <li id="thumb2">
					    <a href="#" class="thumbnail" >
					    	<img src="http://api.webthumbnail.org?width=275&height=175&screen=1280&format=png&url=http://uprrp.edu" alt="Captured by webthumbnail.org" />
							<h3>Graphs</h3>
					     </a>
					  </li>

					  <li id="students">
					    <a href="#" class="thumbnail" id="'.$curso_id[0].'" >
					    	<img src="http://api.webthumbnail.org?width=275&height=175&screen=1280&format=png&url=http://ccom.uprrp.edu" alt="Captured by webthumbnail.org" />
							<h3>Students</h3>
					     </a>
					  </li>
					  
					</ul>
				</td>
				</tr>
				</table>
				<h1>'.$_SESSION['nombre_act'].'</h1>
			<ul id="studentlist">';

while ($row = mysql_fetch_array($students)) {
	$content = $content."<li><a href='#' class='evaluacion'><h2 id='".$row[1]."'>".$row[0]."</h2></a> <h2>Score:15/24</h2> </li><br>";
}

$content = $content."</ul></center></div>
			<script type='text/javascript'>

			$('#students a').on('click', function() {
				var course = $(this).attr('id');
				var url = 'http://ada.uprrp.edu/~chrodriguez/oeae/Scripts/students.php?course_id='+course;
				$.get(url, function(html) {
					$('#content').hide()
					$('#content').replaceWith(html)
				})

			});

			$('.evaluacion').on('click', function () {
				var est_id = $(this).children('h2').attr('id');
				console.log(est_id)
				var url = 'http://ada.uprrp.edu/~chrodriguez/oeae/Scripts/eval.php?est_id='+est_id;

				$.get(url, function(html) {
					$('#content').hide()
					$('#content').replaceWith(html)
					console.log('salioox')
				})
			});
			</script>";
echo $content;

?>