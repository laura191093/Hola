<?php require_once('../connections/conexion_usuarios.php'); ?>
<?php 
//NO MOSTRAR ERRORES WARNING
error_reporting(0);
?>

<?php
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
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

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

$MM_restrictGoTo = "ingreso.php";
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
?>
<?php
$colname_consulta_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_consulta_usuario = (get_magic_quotes_gpc()) ? $_SESSION['MM_Username'] : addslashes($_SESSION['MM_Username']);
}
mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_usuario = sprintf("SELECT Nombre FROM supervisores WHERE username = '%s'", $colname_consulta_usuario);
$consulta_usuario = mysql_query($query_consulta_usuario, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuario = mysql_fetch_assoc($consulta_usuario);
$totalRows_consulta_usuario = mysql_num_rows($consulta_usuario);

$colname_consulta_supervisores = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_consulta_supervisores = (get_magic_quotes_gpc()) ? $_SESSION['MM_Username'] : addslashes($_SESSION['MM_Username']);
}
mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_supervisores = sprintf("SELECT Nombre FROM supervisores WHERE username = '%s'", $colname_consulta_supervisores);
$consulta_supervisores = mysql_query($query_consulta_supervisores, $conexion_usuarios) or die(mysql_error());
$row_consulta_supervisores = mysql_fetch_assoc($consulta_supervisores);
$totalRows_consulta_supervisores = mysql_num_rows($consulta_supervisores);

$colname_consulta_r_a = "-1";
if (isset($_POST['id'])) {
  $colname_consulta_r_a = (get_magic_quotes_gpc()) ? $_POST['id'] : addslashes($_POST['id']);
}
mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_inv = "SELECT i.id, i.semana, i.fechaInicio,
r.opcion as region, c.opcion as select2, e.opcion as select3, i.asunto, i.responsable_caso,
i.tipo_investigacion, i.descripcion_asunto, i.seguimiento, i.aprendio,
i.mejora_implementar, i.estatus, i.fecha_cierre, i.marca, i.con_marca,
i.con_region, i.con_asunto, i.con_ceve, i.con_fecha, i.con_sup, i.fecha_captura
FROM investigaciones i
LEFT JOIN region r ON r.id_region = i.region
LEFT JOIN select_2 c ON c.id = i.select2
LEFT JOIN select_3 e ON e.id = i.select3";
$consulta_inv = mysql_query($query_inv, $conexion_usuarios) or die(mysql_error());
$row_inv = mysql_fetch_assoc($consulta_inv);
$totalRows_inv = mysql_num_rows($consulta_inv);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="../style/consulta_investigaciones.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../style/style_general.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">


<title>Consulta Investigaciones</title>
<style type="text/css">
<!--

.Estilo6 {
	color: #000000;
	font-weight: bold;
}
.Estilo7 {
	font-size: 14px;
	font-weight: bold;
}

</style>
</head>
<body>

<div id="body1">

        <div id="cabezal">
            <div id="logo" class="col4 primeracol"><img src="../style/menu/img/content/oso.png" width="141" height="130" alt="osoSeguridad">
			<img src="../style/menu/img/content/escudo.jpg" width="125" height="130" alt="escudo"></div>
            
			<div id="contenidocab" class="col8 ">
              <table width="623" border="0" cellpadding="0" cellspacing="0" style="width: 780px;">
                <tbody>
                  <tr>
                    <td>
					<h1>CONSULTA INVESTIGACIONES</h1>
                     </td>
                  </tr>
                </tbody>
              </table>
			  

          </div>
        </div>
	
    


</br>
<td width="341"> 
  <div class="sesion"> 
   BIENVENIDO: <?php echo $row_consulta_usuario['Nombre']; ?>
	 <img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="../principal.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>
</br>

  <div class="CSSTableGenerator" >
<table width="150%"  border="0" align="center">
  <tr>
    <td width="20"><div align="center" class="Estilo40"><p align="center"><strong>FOLIO</strong></p></div></td>
    <td width="60"><div align="center"><strong>CODIGO INVESTIGACI&Oacute;N</strong></div></td>
    <td width="20"><div align="center" class="Estilo74"><p align="center"><strong>ESTATUS</strong></p></div></td>
	<td width="40"><div align="center" class="Estilo74"><p align="center"><strong>RESPONSABLE DEL CASO</strong></p></div></td>
    <td width="20"><div align="center" class="Estilo43"><p align="center"><strong>SEMANA</strong></p></div></td>
	<td width="20"><div align="center" class="Estilo44"><p align="center"><strong>FECHA INICIO </strong></p></div></td>
	<td width="20"><div align="center" class="Estilo44"><p align="center"><strong>FECHA CIERRE</strong></p></div></td>
	<td width="20"><div align="center" class="Estilo44"><p align="center"><strong>REGION</strong></p></div></td>	
    <td width="50"><div align="center" class="Estilo46"><p align="center"><strong>CENTRO DE VENTAS</strong></p></div></td>
	<td width="20"><div align="center" class="Estilo45"><p align="center"><strong>MARCA</strong></p></div></td>
    <td width="60"><div align="center" class="Estilo49"><p align="center"><strong>TIPO INV.</strong></p></div></td>
    <td width="30"><div align="center" class="Estilo47"><p align="center"><strong>ASUNTO</strong></p></div></td>	
	<td width="90"><div align="center" class="Estilo47"><p align="center"><strong>DESCRIPCION</strong></p></div></td>	
    <td width="100"><div align="center" class="Estilo47"><p align="center"><strong>SEGUIMIENTO</strong></p></div></td>	
  </tr>
  
  <?php
		while ($row_inv = mysql_fetch_array($consulta_inv)) { 
  		$color = array( 
        'ABIERTO' => 'RED',
		'CERRADO' => 'WRITE',
    ); 
?>	
  
      <td><div align="center"><span class="Estilo7">INV<?php echo $row_inv['id']; ?></span></div></td>    
    <td><div align="center"><span class="Estilo6"><?php echo $row_inv['con_marca']; ?>/<?php echo $row_inv['con_region']; ?>/<?php echo $row_inv['con_asunto']; ?>/<?php echo $row_inv['con_ceve']; ?><?php echo $row_inv['con_fecha']; ?><?php echo $row_inv['con_sup']; ?></span></div></td> 
	

<?php echo("<td style='background-color:" . $color[$row_inv['estatus']] . ";'>"), $row_inv['estatus'];  ?>
	  <td><div align="center"><?php echo $row_inv['responsable_caso']; ?></div></td>
      <td><div align="center"><?php echo $row_inv['semana']; ?></div></td>
	  <td><div align="center"><?php echo $row_inv['fechaInicio']; ?></div></td>
	  <td><div align="center"><?php echo $row_inv['fecha_cierre']; ?></div></td>
	  <td><div align="center"><?php echo $row_inv['region']; ?></div></td>
      <td><div align="center"><?php echo $row_inv['select2']; ?></div></td>
      <td><div align="center"><?php echo $row_inv['marca']; ?></div></td>  
      <td><div align="center"><?php echo $row_inv['tipo_investigacion']; ?></div></td>  	  
	  <td><div align="center"><?php echo $row_inv['asunto']; ?></div></td>  
	  <td><div align="justify"><?php echo $row_inv['descripcion_asunto']; ?></div></td>  
	  <td><div align="justify"><?php echo $row_inv['seguimiento']; ?></div></td>  
	  
    </tr>
    <?php } while ($row_inv = mysql_fetch_assoc($consulta_inv)); ?>
</table>
</div>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($consulta_usuario);
mysql_free_result($consulta_supervisores);
mysql_free_result($consulta_inv);
?>
