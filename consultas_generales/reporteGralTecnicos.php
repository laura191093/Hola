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
$query_tecnicos = sprintf("SELECT * FROM tecnicos_seguridad where Nombre= '%s' ORDER BY semana desc", $colname_RecordsetTable);
$consulta_tecnicos = mysql_query($query_tecnicos, $conexion_usuarios) or die(mysql_error());
$row_tecnicos = mysql_fetch_assoc($consulta_tecnicos);
$totalRows_tecnicos = mysql_num_rows($consulta_tecnicos);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" href="../style/style_tecnico.css">
<link rel="stylesheet" type="text/css" href="../style/table_tecnicos.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../style/style_general.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">

<title>Reporte Tecnico de Seguridadad</title>

<style type="text/css">
<!--
.Estilo2 {
	color: #990000;
	font-weight: bold;
}
-->
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
					<h1>ACTIVIDADES REGISTRADAS</h1>
                    </td>
                  </tr>
                </tbody>
              </table>
			  

          </div>
        </div>
  </br>
  </br>
  </br>
<td width="341"> 
  <div class="sesion"> 
   BIENVENIDO: <?php echo $row_consulta_usuario['Nombre']; ?>
	 <img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="../actividades_tecnicos.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>

<table width="928" border="0">
  <tr>
    <td width="320"><div class="Estilo2" id="actividades">
  TOTAL <?php echo $totalRows_tecnicos?> REGISTROS ENCONTRADOS</div></td>
    <td width="598">
	

</td>
</tr>
</table>

  </br>
</span>
  

<div align="center" class="resultados">
  <?php
if (mysql_num_rows($consulta_tecnicos) == 0) { 

   echo nl2br ("\n \n NO HAY ACTIVIDADES REGISTRADAS DEL AÑO EN CURSO"); 
   
   } 
else { 
?>


<div>
	  <form id="form2" name="form2" method="post" action="descargarActividadesT.php">

  <p align="left"><strong>CLIC SOBRE LA IMAGEN PARA DESCARGAR ACTIVIDADES</strong> 
    <select name="supervisores" id="supervisores" style="display: none;">
      
      <?php
do {  
?>
      <option value="<?php echo $row_consulta_usuario['Nombre']?>"<?php if (!(strcmp($row_consulta_usuario['Nombre'], $_POST['Nombre']))) {echo "selected=\"selected\"";} ?>><?php echo $row_consulta_usuario['Nombre']?></option>
      <?php
} while ($row_consulta_usuario = mysql_fetch_assoc($consulta_usuario));
  $rows = mysql_num_rows($consulta_usuario);
  if($rows > 0) {
  //echo "Dato encontrado"; 
      mysql_data_seek($consulta_usuario, 0);
	  $row_consulta_usuario = mysql_fetch_assoc($consulta_usuario);
  }
?>
    </select>
    <input name="Descargar" type="image" id="Descargar" title="DESCARGAR" value="DESCARGAR" src="../imagenes/descarga.jpg" align="center" width="55" height="45" border="0" target="_blank"/>

</form>
</div>

<div class="CSSTableGenerator" >
   <table width="100%"  border="0" align="center">
   <tr>
    <td width="30"><div align="center"><p align="center"><strong>FOLIO</strong></p></div></td>
	<td width="30"><div align="center"><p align="center"><strong>SEMANA</strong></p></div></td>
    <td width="30"><div align="center"><p align="center"><strong>FECHA </strong></p></div></td>
	<td width="10"><div align="center"><p align="center"><strong>REVISIÓN</strong></p></div></td>
	<td width="30"><div align="center"><p align="center"><strong>ESTATUS</strong></p></div></td>
    <td width="180"><div align="center"><p align="center"><strong>COMENTARIOS</strong></p></div></td>
	
  </tr>
  
   <?php
	do {
?>

   <tr>
      <td><div align="center">T<?php echo $row_tecnicos['id']; ?></div></td>  
	  <td><div align="center"><?php echo $row_tecnicos['semana']; ?></div></td>    
	  <td><div align="center"><?php echo $row_tecnicos['fecha']; ?></div></td>
      <td><div align="center"><?php echo $row_tecnicos['revision']; ?></div></td>
	  <?php echo("<td style='background-color:" . $color[$row_tecnicos['estatus']] . ";'>"), $row_tecnicos['estatus'];  ?>  	  
	  <td><div align="justify"><?php echo $row_tecnicos['comentarios']; ?></div></td>     
    </tr>
    <?php } while ($row_tecnicos = mysql_fetch_assoc($consulta_tecnicos)); ?>

</table>
</div>


<?php
  
  echo  nl2br ("");
  }

?>
</div>
  
</body>
</html>
<?php
mysql_free_result($consulta_usuario);
mysql_free_result($consulta_supervisores);
mysql_free_result($consulta_tecnicos);
?>
