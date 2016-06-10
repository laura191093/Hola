<?php
error_reporting(E_ALL ^ E_NOTICE);
//creamos la conexion a la base de datos
$conexion = mysql_connect('localhost','root','');
$db= mysql_select_db('seguridad',$conexion);


$palabra = $_GET['variable'];
if($palabra == ''){
	echo 'BUSQUEDA VACIA';
}else{
$query = "SELECT * FROM asaltos where num_colaborador LIKE '%$palabra%' OR num_colaborador2 LIKE '%$palabra%'";
$respuesta = mysql_query ($query) or die(mysql_error());
if (mysql_fetch_assoc ($respuesta)<=0) {
	
echo "NO HAY ASALTOS DE ESTE EMPLEADO ".'<a>'.$palabra.'<a>'." EN EL AÑO ACTUAL";

			   
}else {
$respuesta = mysql_query ($query) or die(mysql_error());
while($row = mysql_fetch_array($respuesta))
{
	echo '<p>';
	echo '<b>'.$row['nombrev'].'</b><br />';
	echo $row['num_colaborador'];
	echo '<b>'.$row['nombre_vendedor2'].'</b><br />';
	echo $row['num_colaborador2'];
	echo '</p>';
}
}mysql_free_result($respuesta);
}
?>


