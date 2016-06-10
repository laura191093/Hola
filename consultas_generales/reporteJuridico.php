<?php require_once('../connections/conexion_usuarios.php');
error_reporting (0);


//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../ingreso.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../ingreso.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_Table = "SELECT * FROM supervisores";
$consulta_Table = mysql_query($query_consulta_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_Table = mysql_fetch_assoc($consulta_Table);
$totalRows_consulta_Table = mysql_num_rows($consulta_Table);

$colname_consulta_usuarioTable = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_consulta_usuarioTable = (get_magic_quotes_gpc()) ? $_SESSION['MM_Username'] : addslashes($_SESSION['MM_Username']);
}


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_usuarioTable = sprintf("SELECT Nombre FROM supervisores WHERE username = '%s'", $colname_consulta_usuarioTable);
$consulta_usuario = mysql_query($query_consulta_usuarioTable, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuario = mysql_fetch_assoc($consulta_usuario);
$totalRows_consulta_usuario = mysql_num_rows($consulta_usuario);

$colname_RecordsetTable = "-1";
if (isset($_POST['supervisores'])) {
  $colname_RecordsetTable = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_usuarioAll = sprintf("SELECT Nombre FROM supervisores where categoria='SUPERVISOR' AND zona='JURIDICO' order by Nombre asc", $colname_consulta_usuarioAll);
$consulta_usuarioAll = mysql_query($query_consulta_usuarioAll, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll);
$totalRows_consulta_usuarioAll = mysql_num_rows($consulta_usuarioAll);

$colname_RecordsetAll = "-1";
if (isset($_POST['supervisores'])) {
  $colname_RecordsetAll = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_penal_abiertos = sprintf("SELECT p.id, p.semana, p.fecha, CURDATE() as fechaActual,
DATEDIFF (CURDATE(), p.fecha) AS diferencia,r.opcion as region,
c.nombre_instalacion as select2, e.opcion as select3,
p.organizacion, p.delito, p.tipo, p.etapa_procesal, p.actividad_reportar,p.tiempo_invertido,
p.narracion, p.seguimiento, p.fin_captura,
p.estatus, p.supervisor FROM proceso_penal p
LEFT JOIN region r ON r.id_region = p.region
LEFT JOIN select_2 c ON c.id = p.select2
LEFT JOIN select_3 e ON e.id = p.select3 WHERE supervisor = '%s' and estatus='ABIERTO' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_penal = mysql_query($query_penal_abiertos, $conexion_usuarios) or die(mysql_error());
$row_penal = mysql_fetch_assoc($consulta_penal);
$totalRows_penal = mysql_num_rows($consulta_penal);


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_penal_cerrados = sprintf("SELECT p.id, p.semana, fecha,r.opcion as region, c.nombre_instalacion as select2, e.opcion as select3, p.organizacion, p.delito, p.tipo,p.etapa_procesal, p.actividad_reportar,p.tiempo_invertido, p.narracion, p.seguimiento,p.fin_captura,
p.estatus, p.supervisor FROM proceso_penal p LEFT JOIN region r ON r.id_region = p.region LEFT JOIN select_2 c ON c.id = p.select2 LEFT JOIN select_3 e ON e.id = p.select3 WHERE supervisor = '%s' and estatus='CERRADO' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_cerrados = mysql_query($query_penal_cerrados, $conexion_usuarios) or die(mysql_error());
$row_cerrados = mysql_fetch_assoc($consulta_cerrados);
$totalRows_cerrados = mysql_num_rows($consulta_cerrados);


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_ultimo_registro = "SELECT p.id, p.semana, fecha,r.opcion as region, c.nombre_instalacion as select2, e.opcion as select3, p.organizacion, p.delito, p.tipo,p.etapa_procesal, p.actividad_reportar,p.tiempo_invertido, p.narracion, p.seguimiento, p.fin_captura,
p.estatus, p.supervisor FROM proceso_penal p LEFT JOIN region r ON r.id_region = p.region LEFT JOIN select_2 c ON c.id = p.select2 LEFT JOIN select_3 e ON e.id = p.select3 WHERE supervisor = '%s' ORDER BY id DESC";
$consulta_ultimo_registro = mysql_query($query_ultimo_registro, $conexion_usuarios) or die(mysql_error());
$row_ultimo_registro = mysql_fetch_assoc($consulta_ultimo_registro);
$totalRows_consulta_ultimo_registro = mysql_num_rows($consulta_ultimo_registro);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="../style/estilo_Tabla.css">
<link rel="stylesheet" type="text/css" href="../style/style_campos.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link type="text/css" href="../style/style_directorio_select.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../style/cargar.css">

<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">
<link rel="stylesheet" type="text/css" href="../style/style_notificacion.css">
<title>Reporte General</title>

<script type="text/javascript" src="http://www.deperu.com/js/jquery/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>

<script>
	$(window).load(function () {
  $('#cargando').hide();
});
</script>

<style type="text/css">
.botonExcel{cursor:pointer;}
</style>

<style type="text/css">
<!--

.Estilo10 {color: #003399; }
.Estilo11 {
	color: #000099;
	font-weight: bold;
}
-->



#cargando {
width:350px;
height:70px;
clear:both;
background-color:#FFFF00;
color:#CC0000;
}

.Estilo14 {
	color: #005fbf;
	font-weight: bold;
}
</style>

<script src="../jquery/jquery-1.3.2.min.js" type="text/javascript"></script>	
<script>
$(document).ready(function(){
	$("#ckb_penal_abierto").click(function(evento){
		if ($("#ckb_penal_abierto").attr("checked")){
			$("#div_penal_abierto").css("display", "block");
			$("#div_penal").css("display", "block");

		}else{
			$("#div_penal_abierto").css("display", "none");
			$("#div_penal").css("display", "none");
		}
	});
});


$(document).ready(function(){
	$("#ckb_penal_cerrados").click(function(evento){	
			if ($("#ckb_penal_cerrados").attr("checked")){
			$("#div_penal_cerrados").css("display", "block");
			$("#div_penal_cerrados_T").css("display", "block");
		}else{
		$("#div_penal_cerrados").css("display", "none");
		$("#div_penal_cerrados_T").css("display", "none");
		}
	});
});

</script>





</head>

<body>
<div id="body1">

        <div id="cabezal">
          <div class="ancho940">
            <div id="logo" class="col4 primeracol"><img src="../style/menu/img/content/oso.png" width="141" height="130" alt="osoSeguridad">
			<img src="../style/menu/img/content/escudo.jpg" width="125" height="130" alt="escudo"></div>
            
			<div id="contenidocab" class="col8 ">
              <table width="623" border="0" cellpadding="0" cellspacing="0" style="width: 780px;">
                <tbody>
                  <tr>
                    <td>
					<h1>REPORTE PROCESO PENAL</h1>
                    </td>
                  </tr>
                </tbody>
              </table>
			  
            </div>
          </div>
        </div>
</div>
   
   
  </br>
<td width="341"> 
  <div class="sesion"> 
   BIENVENIDO: <?php echo $row_consulta_usuario['Nombre']; ?>
	<img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> |<a href="../principal.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a>| <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>
</br>

<form id="form2" name="form2" method="post" class= "reporte" action="descargarJuridico.php">
  <label></label>
  <p align="left"><strong>-EXPORTAR A EXCEL REPORTE GENERAL </strong> 
    <select name="supervisores" id="supervisores" >
         <option value="- ELIGE -" <?php if (!(strcmp("", $_POST['nombre']))) {echo "selected=\"selected\"";} ?>> - ELIGE -</option>
      <?php
do {  
?>
      <option value="<?php echo $row_consulta_usuarioAll['Nombre']?>"<?php if (!(strcmp($row_consulta_usuarioAll['Nombre'], $_POST['nombre']))) {echo "selected=\"selected\"";} ?>><?php echo $row_consulta_usuarioAll['Nombre']?></option>
      <?php
} while ($row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll));
  $rows = mysql_num_rows($consulta_usuarioAll);
  if($rows > 0) {
  //echo "Dato encontrado"; 
      mysql_data_seek($consulta_usuarioAll, 0);
	  $row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll);
  }
?>
    </select>
    <img src="../imagenes/export_to_excel.gif" alt="exportar_Excel"  />
    <input type="submit" name="Descargar" class="button themed" id="Descargar" value="DESCARGAR"  />
	

    </label>
  </p>

</form>
<br />

<form id="form1" name="form1" method="post" class= "reporte" action="reporteJuridico.php">
  <label></label>
  <p><strong>-SELECIONE SUPERVISOR Y DE CLIC EN CONSULTAR: </strong>
    <select name="supervisores" id="supervisores" onchange="mostrarDiv(this.value);">
      <option value="- ELIGE -" <?php if (!(strcmp("", $_POST['nombre']))) {echo "selected=\"selected\"";} ?>> - ELIGE -</option>
      <?php
do {  
?>
      <option value="<?php echo $row_consulta_usuarioAll['Nombre']?>"<?php if (!(strcmp($row_consulta_usuarioAll['Nombre'], $_POST['Nombre']))) {echo "selected=\"selected\"";} ?>><?php echo $row_consulta_usuarioAll['Nombre']?></option>
	  
      <?php
} while ($row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll));
  $rows = mysql_num_rows($consulta_usuarioAll);
  if($rows > 0) {
  //echo "Dato encontrado"; 
      mysql_data_seek($consulta_usuarioAll, 0);
	  $row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll);
  }
?>
    </select>
    <input type="submit" name="Consultar" id="Consultar" class="button themed" value="CONSULTAR" />
    <br />
	<br />
	<br />
  </p>
  <div id="contenidoAjax" class="element" >
</p>
    
    <div align="center" class="Estilo11">
      <?php echo $row_penal['supervisor']; ?>    </div>  
    <p class="Estilo14">
	<div align="center" class="resultados Estilo14">
SELECCIONAR CASILLA(S) PARA CONSULTAR ESTATUS</div>

</br>
</br>
     <p>
       <input type="checkbox" name="ckb_penal_abierto" value="1" id="ckb_penal_abierto">
     ABIERTOS</p>
    <div class="Estilo10" id="div_penal" style="display: none;"><span class="Estilo10"> <?php echo $totalRows_penal?> REGISTROS INGRESADOS AL PORTAL WEB</span></div>
	
	</br>
	
	<input type="checkbox" name="ckb_penal_cerrados" value="2" id="ckb_penal_cerrados"> 
  CERRADOS </p>
    <div class="Estilo10" id="div_penal_cerrados_T" style="display:none"><span class="Estilo10"> <? echo $totalRows_cerrados ?> REGISTROS INGRESADOS AL PORTAL WEB</span></div>
	
  </div>
</form>
<p>

<br/>

<div id="div_penal_abierto" style="display: none;">

	
<div align="center" class="resultados">
  <?php
if (mysql_num_rows($consulta_penal) == 0) { 

   echo nl2br ("NO SE ENCONTRARON REGISTROS CON ESTATUS ABIERTO"); 
   
   } 
else { 

 echo  nl2br ("");
  }

?>
  </div>
  
<br />	
	<div class="CSSTableGenerator" >
   <table width="2628" border="1" align="center" cellspacing="1" id="Exportar_a_Excel">
      <tr>
        <td width="136"><div align="center">FOLIO</div></td>
        <td width="134"><div align="center">SEMANA</div></td>
		<td width="171"><div align="center">ESTATUS</div></td>
        <td width="134"><div align="center">FECHA</div></td>
		<td width="182"><div align="center">TIEMPO INVERTIDO</div></td>
		<td width="182"><div align="center">DIAS TRANSCURRIDOS</div></td>
        <td width="155"><div align="center">CENTRO DE VENTAS</div></td>
        <td width="164"><div align="center">ENTIDAD</div></td>
        <td width="165"><div align="center">DELITO</div></td>	
		<td width="165"><div align="center">ACTIVIDAD A REPORTAR</div></td>
		<td width="165"><div align="center">ETAPA PROCESAL</div></td>
      </tr>
	  

        <?php
		while ($row_penal = mysql_fetch_array($consulta_penal)) { 
  		$color = array( 
        'ABIERTO' => '#990000',
		'CERRADO' => 'GREEN',
    ); 
		?>	
	 


	  <td><div align="center"><a href="../detalle_a_sup.php?recordID=<?php echo $row_penal['id']; ?>">P<?php echo $row_penal['id']; ?></a></div></td>
	  <td><div align="center"><?php echo $row_penal['semana']; ?></div></td>
	  
	  <?php echo("<td style='text-align: center; color: white; background-color:" . $color[$row_penal['estatus']] . ";'>"), $row_penal['estatus'];  ?>
	  <td><div align="center"><?php echo $row_penal['fecha']; ?></div></td>
	  <td><div align="center" class="estatus"><?php echo $row_penal['tiempo_invertido']; ?></div></td>
	  <td><div align="center" class="estatus"><?php echo $row_penal['diferencia']; ?> DÍAS</div></td>
      <td><div align="center"><?php echo $row_penal['select2']; ?> / <?php echo $row_penal['organizacion']; ?></div></td>
	  <td><div align="center"><?php echo $row_penal['select3']; ?></div></td>
      <td><div align="center"><?php echo $row_penal['delito']; ?></div></td>
      <td><div align="center"><?php echo $row_penal['actividad_reportar']; ?></div></td>
	  <td><div align="center"><?php echo $row_penal['etapa_procesal']; ?></div></td> 

      </tr>
      <?php } while ($row_penal = mysql_fetch_assoc($consulta_penal)); ?>
	  
    </table>
</div>
</div>

<br/>



<div id="div_penal_cerrados" style="display: none;">
<div align="center" class="resultados">
  <?php
if (mysql_num_rows($consulta_cerrados) == 0) { 

   echo nl2br ("NO SE ENCONTRARON REGISTROS CON ESTATUS CERRADO"); 
   
   } 
else { 

 echo  nl2br ("");
  }

?>

<br/>
<br/>

	<div class="CSSTableGenerator" >
   <table width="2628" border="1" align="center" cellspacing="1" id="Exportar_a_Excel">
      <tr>
        <td width="136"><div align="center">FOLIO</div></td>
        <td width="134"><div align="center">SEMANA</div></td>
		<td width="171"><div align="center">ESTATUS</div></td>
        <td width="134"><div align="center">FECHA</div></td>
		<td width="182"><div align="center">TIEMPO INVERTIDO</div></td>
        <td width="155"><div align="center">CENTRO DE VENTAS</div></td>
        <td width="164"><div align="center">ENTIDAD</div></td>
        <td width="165"><div align="center">DELITO</div></td>	
		<td width="165"><div align="center">ACTIVIDAD A REPORTAR</div></td>
		<td width="165"><div align="center">ETAPA PROCESAL</div></td>
      </tr>
 
		 <?php
			
		do { 
			
		?>		
      <tr>
	  	 <td><div align="center"><a href="../detalle_a_sup.php?recordID=<?php echo $row_cerrados['id']; ?>">P<?php echo $row_cerrados['id']; ?></a></div></td>
	  <td><div align="center"><?php echo $row_cerrados['semana']; ?></div></td>
		 <?php echo("<td style='text-align: center; color: white; background-color:" . $color[$row_cerrados['estatus']] . ";'>"), $row_cerrados['estatus'];  ?>
	  <td><div align="center"><?php echo $row_cerrados['fecha']; ?></div></td>
	  <td><div align="center" class="estatus"><?php echo $row_cerrados['tiempo_invertido']; ?></div></td>
      <td><div align="center"><?php echo $row_cerrados['select2']; ?> / <?php echo $row_cerrados['organizacion']; ?></td>
	  <td><div align="center"><?php echo $row_cerrados['select3']; ?></div></td>
      <td><div align="center"><?php echo $row_cerrados['delito']; ?></div></td>
      <td><div align="center"><?php echo $row_cerrados['actividad_reportar']; ?></div></td>
	  <td><div align="center"><?php echo $row_cerrados['etapa_procesal']; ?></div></td> 
      </tr>
      <?php } while ($row_cerrados = mysql_fetch_assoc($consulta_cerrados)); ?>
    </table>
</div>
</div>




</body>
</html>
<?php
mysql_free_result($consulta_penal);
mysql_free_result($consulta_ultimo_registro);

?>