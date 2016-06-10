<?php

include '../conexion.php';

$codigo=$_POST['vcod'];
$con=conexion();

$sql="select * from datos_tecnicos where Id='".$codigo."'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{

 $row_directorio=mysql_fetch_array($res);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<head>
<link rel="Shortcut Icon" href="../../agenda/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="../../style/mapsDirectorio/css/styleTable_directorio.css">
<title>Directroio</title>
<style type="text/css">
<!--
.Estilo1 {
	color: #003399;
	font-size: 14px;
}
-->
</style>
</head>
<body>

 <div class="CSSTableGenerator">
<table width="103%"  border="0" align="center">

 <tr>
    <td width="78"><div align="center"><p align="center"><strong>FOLIO</strong></p></div></td>
	<td><div align="center" class="Estilo41"><p align="center">ACTUALIZAR</p></div></td>
	<td><div align="center" class="Estilo42"><p align="center">ELIMINAR</p></div></td>
    <td width="135"><div align="center"><strong>NOMBRE</strong></div></td>
    <td width="78"><div align="center"><p align="center"><strong>REGION</strong></p></div></td>
	<td width="97"><div align="center"><p align="center"><strong>CENTRO DE VENTAS</strong></p></div></td>
    <td width="105"><div align="center"><p align="center"><strong>ENTIDAD</strong></p></div></td>
	<td width="119"><div align="center"><p align="center"><strong>MARCA</strong></p></div></td>
	<td width="113"><div align="center"><p align="center"><strong>ROL</strong></p></div></td>
	<td width="113"><div align="center"><p align="center"><strong>SUPERVISOR</strong></p></div></td>
  </tr>
  
      <td><div align="center" class="Estilo94 Estilo95 Estilo1"><?php echo $row_directorio['Id']; ?></div></td>
      <td><div align="center"><a href="actualiza_datosTecnicos.php?recordID=<?php echo $row_directorio['Id']; ?>"><img src="imagenes/edit.png" title="ACTUALIZAR INFORMACION" width="30" height="30" border="0"></a></div></td>
      <td><div align="center"><a href="borrar_TecnicoID.php?recordID=<?php echo $row_directorio['Id']; ?>"><img src="imagenes/borrar.png" title="BORRAR REGISTRO" width="25" height="25" border="0"></a></div></td>
 <td width="135" bordercolor="#333333"><div align="center"><strong><?php echo utf8_encode($row_directorio['Nombre']); ?></strong></div></td>
      <td width="78"><div align="center"><strong><?php echo utf8_encode($row_directorio['region']); ?></strong></div></td>
	  <td width="97"><div align="center"><strong><?php echo utf8_encode($row_directorio['select2']); ?></strong></div></td>
	  <td width="105"><div align="center"><strong><?php echo utf8_encode($row_directorio['select3']); ?></strong></div></td>
	  <td width="119"><div align="center"><strong><?php echo utf8_encode($row_directorio['organizacion']); ?></strong></div></td>
	  <td width="113"><div align="center"><strong><?php echo utf8_encode($row_directorio['rol']); ?></strong></div></td>
	  <td width="113"><div align="center"><strong><?php echo utf8_encode($row_directorio['supervisor']); ?></strong></div></td>
    </tr>
   </table>

</div>

</body>
</html>
