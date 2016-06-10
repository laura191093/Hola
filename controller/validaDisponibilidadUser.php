<?php
sleep(1);
require('../connections/conexion_usuarios.php');

if($_REQUEST)
{
	$username = $_REQUEST['username'];
	
	mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
	$queryValidar = "select * from supervisores where username = '".strtolower($username)."'";
	$consultaValidar = mysql_query($queryValidar, $conexion_usuarios) or die(mysql_error());
	
	if(mysql_num_rows(@$consultaValidar) > 0) 
	{
		echo '<div id="Error">USUARIO REGISTRADO</div>';
	}
	else
	{
		echo '<div id="Success">DISPONIBLE</div>';
	}
	
}


?>