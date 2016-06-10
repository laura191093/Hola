<?php

include '../conexion.php';

$codigo=$_POST['vcod'];
$con=conexion();

$sql="select * from gps where id='".$codigo."'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{

 $row_gps=mysql_fetch_array($res);
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
	<td width="105"><div align="center"><p align="center"><strong>ALERTA</strong></p></div></td>
    <td width="105"><div align="center"><p align="center"><strong>GPS ASIGNADO</strong></p></div></td>
	<td width="119"><div align="center"><p align="center"><strong>N&Uacute;MERO DE COLABORADOR</strong></p></div></td>
	<td width="113"><div align="center"><p align="center"><strong>VENDEDOR</strong></p></div></td>
	</tr>
  
		  <td><div align="center" class="Estilo94 Estilo95 Estilo1">G<?php echo $row_gps['id']; ?></div></td>
      <td><div align="center"><a href="actualiza_gps.php?recordID=<?php echo $row_gps['id']; ?>"><img src="imagenes/edit.png" title="ACTUALIZAR ASIGNACION" width="30" height="30" border="0"></a></div></td>
      <td><div align="center"><a href="borrar_gps.php?recordID=<?php echo $row_gps['id']; ?>"><img src="imagenes/borrar.png" title="BORRAR REGISTRO" width="25" height="25" border="0"></a></div></td>
	   <td width="105"><div align="center"><strong><img src="imagenes/<?php echo utf8_encode($row_gps['alert']); ?>" alt="DETENIDO"width="25" height="29" title="Clic para ampliar imagen" /></strong></div></td>
 <td width="105"><div align="center"><strong><?php echo utf8_encode($row_gps['num_gps']); ?></strong></div></td>
	  <td width="119"><div align="center"><strong><?php echo utf8_encode($row_gps['num_colaborador']); ?></strong></div></td>
	  <td width="113"><div align="center"><strong><?php echo utf8_encode($row_gps['nombrev']); ?></strong></div></td>
      </tr>
   </table>

</div>

</body>
</html>


