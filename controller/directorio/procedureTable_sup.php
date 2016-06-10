<?php

include '../conexion.php';

$codigo=$_POST['vcod'];
$con=conexion();

$sql="select * from directorio where Id='".$codigo."'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{

 $row_inv=mysql_fetch_array($res);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="../../agenda/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="../../style/mapsDirectorio/css/styleTable_directorio.css">
<title>Directroio</title>
</head>
<body>

 <div class="CSSTableGenerator">
<table width="103%"  border="0" align="center">

 <tr>
    <td width="135"><div align="center"><strong>NOMBRE</strong></div></td>
    <td width="78"><div align="center"><p align="center"><strong>REGION</strong></p></div></td>
	<td width="97"><div align="center"><p align="center"><strong>CARGO</strong></p></div></td>
    <td width="105"><div align="center"><p align="center"><strong>CELULAR</strong></p></div></td>
	<td width="113"><div align="center"><p align="center"><strong>OFICINA 1</strong></p></div></td>
	<td width="113"><div align="center"><p align="center"><strong>OFICINA 2</strong></p></div></td>
	<td width="79"><div align="center"><p align="center"><strong>RED</strong></p></div></td>	
    <td width="127"><div align="center"><p align="center"><strong>RED CORTA</strong></p></div></td>
    <td width="90"><div align="center"><p align="center"><strong>ZONA</strong></p></div></td>	
	<td width="129"><div align="center"><p align="center"><strong>BASE</strong></p></div></td>	
  </tr>

<td width="135" bordercolor="#333333"><div align="center"><strong><?php echo utf8_encode($row_inv['nombre']); ?></strong></div></td>
      <td width="78"><div align="center"><strong><?php echo utf8_encode($row_inv['region']); ?></strong></div></td>
	  <td width="97"><div align="center"><strong><?php echo utf8_encode($row_inv['cargo']); ?></strong></div></td>
	  <td width="105"><div align="center"><strong><?php echo utf8_encode($row_inv['celular']); ?></strong></div></td>
	  <td width="113"><div align="center"><strong><?php echo utf8_encode($row_inv['oficina1']); ?></strong></div></td>
      <td width="113"><div align="center"><strong><?php echo utf8_encode($row_inv['oficina2']); ?></strong></div></td>
      <td width="79"><div align="center"><strong><?php echo utf8_encode($row_inv['red']); ?></strong></div></td>  
      <td width="127"><div align="center"><strong><?php echo utf8_encode($row_inv['red_corta']); ?></strong></div></td>  	
	  <td width="90"><div align="center"><strong><?php echo utf8_encode($row_inv['zona']); ?></strong></div></td>  
	  <td width="129"><div align="center"><strong><?php echo utf8_encode($row_inv['base']); ?></strong></div></td>  	  
    </tr>
   </table>

</div>


</body>
</html>