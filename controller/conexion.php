<?php

function conexion(){

 $con = mysql_connect("localhost","root","");

 if (!$con){

  die('Error de conexion ' . mysql_error());
 }

 mysql_select_db("seguridad", $con);

 return($con);

}

?>