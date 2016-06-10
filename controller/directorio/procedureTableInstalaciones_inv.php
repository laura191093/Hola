<?php

include '../conexion.php';

$codigo=$_POST['vcod'];
$con=conexion();

$sql="select * from directorio_instalaciones d LEFT JOIN directorio s ON d.id_usuario=s.Id where d.id='".$codigo."'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{

 $row_directorio=mysql_fetch_array($res);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<style type="text/css">
<!--
.Estilo2 {
	color: #990000;
	font-weight: bold;
}
-->
</style>
<head>
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<head>
<link rel="Shortcut Icon" href="../../agenda/favicon.ico" type="image/x-icon" />
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
    <td width="78"><div align="center">
      <p align="center"><strong>ID</strong></p>
    </div></td>
    <td width="135"><div align="center"><strong>REGION</strong></div></td>
    <td width="78"><div align="center">
      <p align="center"><strong>CENTRO DE TRABAJO</strong></p>
    </div></td>
	<td width="97"><div align="center">
	  <p align="center"><strong>ENTIDAD</strong></p>
	</div></td>
    <td width="105"><div align="center">
      <p align="center"><strong>MARCA</strong></p>
    </div></td>
	<td width="119"><div align="center">
	  <p align="center"><strong>TELEFONO</strong></p>
	</div></td>
	<td width="113"><div align="center">
	  <p align="center"><strong>DIRECCI&Oacute;N</strong></p>
	</div></td>
	<td width="113"><div align="center">
	  <p align="center"><strong>CORREO ADMINISTRADOR </strong></p>
	</div></td>
	</tr>
  
      <td><div align="center" class="Estilo94 Estilo95 Estilo1"><?php echo $row_directorio['id']; ?></div></td>
 <td width="135" bordercolor="#333333"><div align="center"><strong><?php echo utf8_encode($row_directorio['region']); ?></strong></div></td>
      <td width="78"><div align="center"><strong><?php echo utf8_encode($row_directorio['select2']); ?></strong></div></td>
	  <td width="97"><div align="center"><strong><?php echo utf8_encode($row_directorio['select3']); ?></strong></div></td>
	  <td width="105"><div align="center"><strong><?php echo utf8_encode($row_directorio['marca']); ?></strong></div></td>
	  <td width="119"><div align="center"><strong><?php echo utf8_encode($row_directorio['tel_ceve']); ?></strong></div></td>
	  <td width="113"><div align="center"><strong><?php echo utf8_encode($row_directorio['direccion']); ?></strong></div></td>
      <td width="113"><div align="center"><strong><?php echo "<a href='mailto:".$row_directorio['administrador']."'>".$row_directorio['administrador']."</a>" ?></strong></div></td>
      </tr><tr>
        <td colspan="10"> <span class="Estilo2">VER EL GOOGLE MAPS</span> <a href="https://www.google.com.mx/maps/place/<?php echo $row_directorio['latitud']; ?> <?php echo $row_directorio['longitud']; ?>" target="_blank">https://www.google.com.mx/maps/place/<?php echo $row_directorio['latitud']; ?> <?php echo $row_directorio['longitud']; ?></a></td>
      </tr>
   </table>

</div>

</body>
</html>
