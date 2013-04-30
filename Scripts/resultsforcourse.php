<?php
/*
	Tahiri Laboy De Jesus
	Script para ver agregados por curso
*/

/* Parametros para conexion a la base de datos */
$server         = 'localhost';
$user           = 'nosotros';
$password       = 'oeaeavaluo2013';
$database       = 'Avaluo';
$link           = mysql_connect($server, $user, $password);

/* Se intenta la conexion, de ser infructuosa se deniega el acceso. */
if ($link) {
	mysql_select_db("Avaluo");
} else {
	echo "Access Denied!";
	exit();
}

session_start();

$curso_id = $_GET['course_id'];

$query = "SELECT codificacion, nombre_curso from Cursos where curso_id=".$curso_id;
$result = mysql_fetch_array(mysql_query($query));
$codif = $result[0];
$nombre_curso = $result[1];

$logrosesperados = array(); // Logros esperados
$actividades = array(); 		// Ids de las actividades
$nombresact = array();		// Nombres de la actividades

/* Se obtienen los logros esperados para cada actividad asociada a un curso. */
$query = mysql_query("SELECT act_id, nombre_act, logro_esperado from Actividades natural join 
	ActividadesCurso where curso_id='$curso_id';")
	or die(mysql_error());

/************************ SI HAY ACTIVIDADES... ************************/
if (mysql_num_rows($query)>0) { 

 while ($result = mysql_fetch_array($query)) {
 	$actividades[] = $result["act_id"];
	$nombresact[] = $result["nombre_act"];
	$logrosesperados[] = $result["logro_esperado"];
 } 

 $i = 0;

 $a_evaluadas = array();	// Retendra ids las actividades que fueron evaluadas
 $nombres_crs = array(); 	// Nombres de los criterios de todas las actividad
 $cr_aprobados = array();	// Indica si se aprobo o no cada criterio en $nombre_crs
 
 // NO SE PA QUE RAYOS ESTOY HACIENDO ESTO
 $contador_estudiantes = 0; /* Lleva record de los estudiantes evaluados en 
							todas las actividades*/

 // Query para verificar que actividades han sido evaluadas
 $query = mysql_query("Select distinct act_id from Actividades natural join ActividadesCurso
	natural join Evaluacion where curso_id ='$curso_id';") or die(mysql_error());
 if (mysql_num_rows($query)>0) { // Si hay actividades evaluadas...
 	while ($result = mysql_fetch_array($query)) {
 		$a_evaluadas[] = $result["act_id"];
	}
 }

 echo "<div id='content'><center>
	  <h3>Resultados para curso ".$codif." (".$nombre_curso.")</h3>";

 $tabla1 ="<table id = grading>
	 <caption><h4>Resumen de actividades</h4>
	</caption>
	       <thead><td><p>Nombre de la actividad</p></td>
	       <td><p>Logro Esperado</p></td>
	       <td><p>Evaluada o no evaluada</p></td>
		   </thead>
	    <tbody>"; 	

 for ($i = 0; $i < sizeof($actividades); $i++) {
	$tabla1.="<tr><td><p>".$nombresact[$i]."</p></td>
 				  <td><p>".$logrosesperados[$i]."</p></td>";
	if (in_array($actividades[$i] ,array_diff($actividades, $a_evaluadas))) {
		$tabla1.="<td><p>No evaluada</p></td>";
	}
	else {
		$tabla1.="<td><p>Evaluada</p></td>";
		
	}
	$tabla1.="</tr>";
 }
 $tabla1.="</tbody></table>";
 
 // Para las actividades que han sido evaluados
 for ($i = 0; $i < sizeof($a_evaluadas); $i++) {
	// Se obtiene el total de estudiantes evaluados en la actividad
	$query = mysql_query("SELECT distinct mat_id FROM Evaluacion WHERE act_id='$a_evaluadas[$i]';")
		or die(mysql_error());
	$estudiantes = mysql_num_rows($query);
	$contador_estudiantes += $estudiantes;

	if ($estudiantes > 0) {

		// Se obtienen los ids y nombres de los criterios en la rubrica. 
		$query = mysql_query("SELECT crit_id, nombre_crit FROM RubricaLocal NATURAL 
			JOIN Actividades NATURAL JOIN Criterios WHERE act_id = '$a_evaluadas[$i]';")
			or die(mysql_error());

		$crit_qty = mysql_num_rows($query);

		// Verifica que hayan resultados
		if ($crit_qty > 0) {
			$cids = array();
			$criterios = array();

			while ($result = mysql_fetch_array($query)) {
				$cids[] = $result["crit_id"];
				$criterios[] = $result["nombre_crit"]; 
			}

		$totales = array(array());

		/* Se obtienen los totales para cada escala en la rubrica. */
		for($criterio=0; $criterio<$crit_qty; $criterio++) { // Criterios - Filas
			$totales[$criterio][9] = 0; // Retiene el total de estudiantes con 6 o mas.
			$totales[$criterio][11] = 0; // Para verificar si se han hecho evaluaciones o no.

			for ($escala=0; $escala<=8; $escala++) { // Escala - Columnas
	    		$query = mysql_query("Select count(ptos_obtenidos) from Evaluacion where 
					ptos_obtenidos=".$escala." && crit_id=".$cids[$criterio]." 
					&& act_id=".$a_evaluadas[$i].";") or die(mysql_error());
				if(mysql_num_rows($query) > 0) {
					$result = mysql_fetch_array($query);
					$totales[$criterio][$escala] = $result[0];
				} 
				else {
					$totales[$criterio][$escala] = 0;
				}		

				// Acumulador para detectar si el criterio fue evaluado.
				$totales[$criterio][11]+=$totales[$criterio][$escala];

				// Se generan los totales de estudiantes que obtuvieron 6 o mas.
				if ($escala >= 6) {
					$totales[$criterio][9]+=$totales[$criterio][$escala];
				}
			}
	
			$totales[$criterio][9] = (($totales[$criterio][9])/$estudiantes)*100;

			// Verificar si el criterio fue evaluado y de ser asi, si fue aprobado.
			if ($totales[$criterio][11] == 0) {
				$totales[$criterio][9] = 'x';
				$totales[$criterio][10] = 'No evaluado';
			}
			else if ($totales[$criterio][9] >= $logrosesperados[$i]) {
				$totales[$criterio][10] = 'Alcanzado';
			}
			else $totales[$criterio][10] = 'No alcanzado';
		} 
		// Copio los resultados que necesito de la tabla 2D a arreglos sencillos.
		for($k = 0; $k < $crit_qty; $k++) {
			$cr_aprobados[] = $totales[$k][10];
			$nombres_crs[] = $criterios[$k];
		}
	}
  }
}

$criterios_norep = array_unique($nombres_crs);

/* Genero tabla de resumen de criterios */
 $tabla2 ="<br><br><br><br><table id = grading>
	 <caption><h4>Resumen de criterios para actividades evaluadas</h4>
	</caption>
	       <thead><td><p>Criterio</p></td>
	       <td><p>Veces que se evaluó</p></td>
	       <td><p>Aprobado</p></td>
		   <td><p>No aprobado</p></td>
		   </thead>
	    <tbody>"; 	

 $alcanzado = 0;
 $noalcanzado = 0;

print_r($nombres_crs);
print_r($criterios_norep);

 foreach ($criterios_norep as $criterio) {
	// Criterio
	$tabla2.="<tr><td><p>".$criterio."</p></td>";
	$result = array_keys($nombres_crs, $criterio);
//	echo "\n\n".$criterio.": ";
//	print_r($result);  
	foreach ($result as $status) {
		if ($cr_aprobados[$status] == "Alcanzado") $alcanzado++;
		else if ($cr_aprobados[$status] == "No alcanzado") $noalcanzado++;
	}
	$veces = $alcanzado + $noalcanzado;
	if ($veces == 0) {
		$veces = "No evaluado";
		$alcanzado = "-";
		$noalcanzado = "-";
	}

	// Veces que se evaluo
	$tabla2.="<td><p>".($alcanzado + $noalcanzado)."</p></td>";
	// Alcanzado
	$tabla2.="<td><p>".$alcanzado."</p></td>";
	// No alcanzado
	$tabla2.="<td><p>".$noalcanzado."</p></td>";			
	$tabla2.="</tr>";

	$alcanzado = 0;
	$noalcanzado = 0;
 }
 $tabla2.="</tbody></table>";
 
// DOMINIOS
/*
if ($contador_estudiantes > 0) { // Si se ha evaluado algun estudiante...

	$query = mysql_query("Select distinct nombre_dom, nombre_crit from Dominios natural 
	join CriterioPertenece natural join Criterios Evaluacion natural join Actividades natural join 
	ActividadesCurso natural join Cursos where curso_id='$curso_id';") or die(mysql_error());
/*
 $query = mysql_query("Select distinct nombre_dom, nombre_crit from Dominios natural 
	join Criterios natural join Evaluacion natural join CriterioPertenece natural join 
	ActividadesCurso where curso_id='$curso_id';") or die(mysql_error());
*//*
select distinct nombre_dom, nombre_crit from RubricaLocal natural join Dominios natural join CriterioPertenece natural join Criterios natural join Actividades natural join Cursos natural join ActividadesCurso natural join Evaluacion where curso_id=1;
*/
/*
$query = mysql_query("select nombre_dom, nombre_crit from RubricaLocal natural join
	Dominios natural join CriterioPertenece natural join Criterios natural join Actividades
	where act_id='$aid';") or die(mysql_error());
*/


/*
 $dominios = array();	// Nombres de los dominios
 $d_span = array();		/* Span de cada dominio (para efecto rowspan en tabla html) -
						   Almacena la cantidad de criterios asociados al dominio */
/* $criterios2 = array();	/* Nombres de los criterios (esta vez en el orden asociado 
						   a los dominios) */
/* $i = -1;	

 while ($result = mysql_fetch_array($query)) {
	
	$criterios2[] = $result['nombre_crit'];

	// Si el dominio no esta ya en el arreglo, se guarda
	if (!in_array($result['nombre_dom'], $dominios)) { 
		$dominios[] = $result['nombre_dom'];
		$i++;	
		$d_span[$i] = 1;
	}
	else { 
		$d_span[$i]++;
	}
 }

 $aprobados = array(); // Acumulador de criterios aprobados por dominio.
 for ($i = 0; $i < sizeof($dominios); $i++) $aprobados[$i] = 0; // Inicializacion

 $porciento = array(); // Almacena el porciento de criterios aprobados
 $dom_alcanzado = array(); // Retiene si se alcanzo o no el dominio

 $first = 0; // Donde se comienza a iterar 
 $last = 0;  // Donde se termina de iterar
 $iter = 0;  // Contador de iteraciones - Cada iteracion corresponde a un dominio

 foreach ($d_span as $asociados) {
	$last += $asociados; /* Termina cuando se itere por todos los criterios
							asociados al dominio. */
	
	/* Se verifica cuantos de los criterios alineados a los dominios fueron aprobados. */
/*	for ($i = $first; $i < $last; $i++) {
		$filas = array_keys($nombres_crs, $criterios2[$i]);
		foreach ($filas as $fila) {
			if ($cr_aprobados[$fila] == 'Aprobado') {
				$aprobados[$iter]++; // Aumento el conteo de criterios aprobados
			}
		}
	}
	// Se computa el porciento de criterios aprobados
	$porciento[] = round(($aprobados[$iter]/$asociados)*100,2);

	// Se verifica si el dominio se alcanzo 
	if (round($porciento[$iter],-1) >= 70) {
		$dom_alcanzado[] = 'Aprobado';
	}
	else $dom_alcanzado[] = 'No aprobado';

	$first = $last; // Establezco desde donde comienza la nueva iteracion.
	$iter++; // Vamos al proximo dominio
 }


 $tabla2 ="<p> Actividades evaluadas: ".count($a_evaluadas)."
	<table id = grading>
	 <caption><h4>Tabla de agregados por dominios</h4></caption>
        <thead>
	       <td><p>Dominios</p></td>
	       <td><p>Criterios Alineados</p></td>
	       <td><p>Criterios Aprobados</p></td>
		   <td><p>Porcentaje</p></td>
		   <td><p>Aprobado o no aprobado</p></td>
	    </thead><tbody>";

 $dom_i = 0;
 $span = 0;

 // Se despliega el contenido de la tabla
 for ($i=0; $i<sizeof($criterios2); $i++) {
	if($i == $span) {
		$tabla2.="<tr><td rowspan=".$d_span[$dom_i]."><p>".$dominios[$dom_i]."</p></td>";
	}
    $tabla2.="<td><p>".$criterios2[$i]."</p></td>";
	if($i == $span) {
		$tabla2.="<td rowspan=".$d_span[$dom_i]."><p>".$aprobados[$dom_i]." de ".$d_span[$dom_i]."</p></td>";
		$tabla2.="<td rowspan=".$d_span[$dom_i]."><p>".$porciento[$dom_i]."</p></td> 
		<td rowspan=".$d_span[$dom_i]."><p>".$dom_alcanzado[$dom_i]."</p></td>" ;
		$span += $d_span[$dom_i];
		$dom_i++;
	}
	$tabla2.="</tr>";
 }
 $tabla2.="</tbody></table>"; 

}

else { 
	$tabla2.="<p> Error: Aún no se han realizado evaluaciones para las actividades
 		de este curso.";
}
*/
}
/************************ SI NO HAY ACTIVIDADES... ************************/
else {
	$tabla2 ="<p> Error: Aún no se han realizado actividades para este curso.";
} 

echo $tabla1.$tabla2;
echo "<br><br><br><br><br><br></center></div>";

?>
