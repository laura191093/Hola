<?php
sleep(5);
require('../connections/conexion_usuarios.php');

if($_REQUEST)
{
	$num_empleado = $_REQUEST['num_empleado'];
	
	mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
	$queryValidar = "select * from bajas where num_empleado = '".strtolower($num_empleado)."'";
	$consultaValidar = mysql_query($queryValidar, $conexion_usuarios) or die(mysql_error());
	
	if(mysql_num_rows(@$consultaValidar) > 0) 
	{
		echo '<div id="Error">BAJA CON NUM DE EMPLEADO QUE INTENTA INGRESAR YA HA SIDO REGISTRADA, CONTACTE A HD SEGURIDAD</div>';
	}
	else
	{
		echo '<div id="Success">NUMERO DE EMPLEADO VALIDADO</div>';
	}
	
}


?>