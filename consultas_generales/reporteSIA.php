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
$query_consulta_usuarioAll = sprintf("SELECT Nombre FROM supervisores where categoria='SUPERVISOR' AND zona='SIA' order by Nombre asc", $colname_consulta_usuarioAll);
$consulta_usuarioAll = mysql_query($query_consulta_usuarioAll, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuarioAll = mysql_fetch_assoc($consulta_usuarioAll);
$totalRows_consulta_usuarioAll = mysql_num_rows($consulta_usuarioAll);

$colname_RecordsetAll = "-1";
if (isset($_POST['supervisores'])) {
  $colname_RecordsetAll = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_SIA_respuesta = "SELECT s.id, s.semana,  s. fecha_solicitud, CURDATE() as fechaActual, DATEDIFF (CURDATE(), s.fecha_solicitud) AS diferencia,
s.fecha_entrega, r.opcion as region, c.opcion as select2, e.opcion as select3,
s.organizacion, s.solicitante, s.narracion, s.estatus, s.abierto_por, s.motivo_abierto, s.requerimiento, s.oracle, s.dsd, s.barcel_pro, s.lib_ruta, s.qlik_view, s.kronos, s.portal_seg, s.meta4, s.internet, s.nomina, s.acl, s.easitrax, s.otro 
FROM sia s
LEFT JOIN region r ON r.id_region = s.region
LEFT JOIN select_2 c ON c.id = s.select2
LEFT JOIN select_3 e ON e.id = s.select3 HAVING diferencia >=2 and s.estatus='ABIERTO'";
$consulta_respuestaSIA = mysql_query($query_SIA_respuesta, $conexion_usuarios) or die(mysql_error());
$row_consulta_respuestaSIA = mysql_fetch_assoc($consulta_respuestaSIA);
$totalRows_consulta_respuestaSIA = mysql_num_rows($consulta_respuestaSIA);	



mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_SIA_cerrado = "SELECT s.id, s.semana,  s. fecha_solicitud, CURDATE() as fechaActual, DATEDIFF (CURDATE(), s.fecha_solicitud) AS diferencia,
s.fecha_entrega, r.opcion as region, c.opcion as select2, e.opcion as select3,
s.organizacion, s.solicitante, s.narracion, s.estatus, s.abierto_por, s.motivo_abierto, s.requerimiento, s.acciones_hallazgos, s.oracle, s.dsd, s.barcel_pro, s.lib_ruta, s.qlik_view, s.kronos, s.portal_seg, s.meta4, s.internet, s.nomina, s.acl, s.easitrax, s.otro 
FROM sia s
LEFT JOIN region r ON r.id_region = s.region
LEFT JOIN select_2 c ON c.id = s.select2
LEFT JOIN select_3 e ON e.id = s.select3 HAVING diferencia >=2 and s.estatus='CERRADO'";
$consulta_cerradoSIA = mysql_query($query_SIA_cerrado, $conexion_usuarios) or die(mysql_error());
$row_consulta_cerradoSIA = mysql_fetch_assoc($consulta_cerradoSIA);
$totalRows_consulta_cerradoSIA = mysql_num_rows($consulta_cerradoSIA);	


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_ultimo_registro = sprintf("SELECT s.solicitante, s.narracion, s.estatus, s.abierto_por, s.motivo_abierto, s.requerimiento, s.acciones_hallazgos, s.oracle, s.dsd, s.barcel_pro, s.lib_ruta, s.qlik_view, s.kronos, s.portal_seg, s.meta4, s.internet, s.nomina, s.acl, s.easitrax, s.otro, s.fin_captura FROM sia s ORDER BY id DESC", $colname_consulta_ss);
$consulta_ultimo_registro = mysql_query($query_ultimo_registro, $conexion_usuarios) or die(mysql_error());
$row_consulta_ultimo_registro = mysql_fetch_assoc($consulta_ultimo_registro);

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
<title>Solicitudes SIA</title>

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


#cargando {
width:350px;
height:70px;
clear:both;
background-color:#FFFF00;
color:#CC0000;
}


	
body,td,th {
	color: #0066CC;
}
</style>


<script src="../jquery/jquery-1.3.2.min.js" type="text/javascript"></script>	
<script>
$(document).ready(function(){
	$("#ckb_sia_abierto").click(function(evento){
		if ($("#ckb_sia_abierto").attr("checked")){
			$("#div_sia_abierto").css("display", "block");
			$("#div_sia").css("display", "block");

		}else{
			$("#div_sia_abierto").css("display", "none");
			$("#div_sia").css("display", "none");
		}
	});
});


$(document).ready(function(){
	$("#ckb_penal_cerrados").click(function(evento){	
			if ($("#ckb_penal_cerrados").attr("checked")){
			$("#div_sia_cerrados").css("display", "block");
			$("#div_sia_cerrados_T").css("display", "block");
		}else{
		$("#div_sia_cerrados").css("display", "none");
		$("#div_sia_cerrados_T").css("display", "none");
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
					<h1>CONSULTAR SOLICITUDES SIA</h1>
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




<form id="form1" name="form1" method="post" class= "reporte" action="reporteSIA.php">

<div class="ingreso">ÚLTIMO REGISTRO: <?php echo $row_consulta_ultimo_registro['fin_captura']; ?>	<br />
	<br />
</div>

  </p>
  <div id="contenidoAjax" class="element" >
</p>
    
    <div align="center" class="Estilo11">
      <?php echo $row_SIA['supervisor']; ?>    </div>  
    <p class="Estilo14">
	<div align="center" class="resultados Estilo14">
SELECCIONAR CASILLA(S) PARA CONSULTAR ESTATUS</div>

</br>
</br>
     <p>
       <input type="checkbox" name="ckb_sia_abierto" value="1" id="ckb_sia_abierto">
     ABIERTOS</p>
    <div class="Estilo10" id="div_sia" style="display: none;"><span class="Estilo10"> <?php echo $totalRows_consulta_respuestaSIA ?> SOLICITUDES INGRESADAS AL PORTAL WEB</span></div>
	
	</br>
	
	<input type="checkbox" name="ckb_penal_cerrados" value="2" id="ckb_penal_cerrados"> 
  CERRADOS </p>
    <div class="Estilo10" id="div_sia_cerrados_T" style="display:none"><span class="Estilo10"> <? echo $totalRows_consulta_cerradoSIA?> SOLICITUDES INGRESADAS AL PORTAL WEB</span></div>
	
  </div>
</form>
<p>

<br/>


<div id="div_sia_abierto" style="display:none">

<form id="form2" name="form2" method="post" action="descargarSIA_abiertos.php">

  <p align="left"><strong>-EXPORTAR SOLICITUDES </strong><img src="../imagenes/export_to_excel.gif" alt="exportar_Excel" width="28" height="17"  />
    <input type="submit" name="Descargar" class="button themed" id="Descargar" value="DESCARGAR" />
	

    </label>
  </p>
</form>
	
<div align="center" class="resultados">
              <!--<img src="imagenes/alerta1.gif" title="QUITAR PARAMETROS" width="40" height="40" border="0" />-->
              <?php
if (mysql_num_rows($consulta_respuestaSIA ) == 0) { 

   echo nl2br ("\n \n NO HAY SOLICITUDES ABIERTAS POR ACTUALIZAR"); 
   
   } 
else { 

?>


</div>
            <div align="center" class="notificacion">
              <?php
  echo  nl2br ("\n TIEMPO DE RESPUESTA AGOTADO");
  
  ?>
   <br/>
    <br/>
    
   TOTAL <?php echo $totalRows_consulta_respuestaSIA ?> REGISTROS</br>
  <br/>
    <br/>
              <div class="CSSTableGenerator">
                <form method="post" name="form1" class= "reporte" action="">
                  <table width="50%" border="1" align="center">
                    <tr>
                      <td width="12%"><div align="center"><strong>FOLIO</strong></div></td>
                      <td width="25%"><div align="center"><strong>ACTUALIZAR ESTATUS</strong></div></td>
					  <td width="25%"><div align="center"><strong>ABIERTO POR</strong></div></td>
                      <td width="25%"><div align="center"><strong>CENTRO DE TRABAJO</strong></div></td>
                      <td width="25%"><div align="center"><strong>REQUERIMIENTO</strong></div></td>
					  <td width="12%"><div align="center"><strong>FECHA SOLICITUD</strong></div></td>
                      <td width="12%"><div align="center"><strong>DIAS ABIERTOS</strong></div></td>
					  <td width="12%"><div align="center"><strong>SISTEMAS DE LOS QUE SE REQUIRE INFORMACIÓN</strong></div></td>
                      <td width="15%"><div align="center"><strong>SOLICITANTE</strong></div></td>
                    </tr>
                    <?php do { ?>
                    <tr>
                      <td width="12%"><div align="center">INV-I<?php echo $row_consulta_respuestaSIA ['id']; ?></div></td>
                      <td width="25%"><div align="center"> <a href="actualiza_SIA.php?recordID=<?php echo $row_consulta_respuestaSIA ['id']; ?>"></a><a href="actualiza_SIA.php?recordID=<?php echo $row_consulta_respuestaSIA ['id']; ?>"><img src="../imagenes/clock.svg" title="ACTUALIZAR_ESTATUS" width="30" height="30" border="0" /></a> </div></td>

					  <td width="25%"><div align="center"><?php echo $row_consulta_respuestaSIA ['abierto_por']; ?>  <br/> <br/> <?php echo $row_consulta_respuestaSIA ['motivo_abierto']; ?></div></td>
                      <td width="25%"><div align="center"><?php echo $row_consulta_respuestaSIA ['select2']; ?></div></td>
                      <td width="25%"><div align="center"><?php echo $row_consulta_respuestaSIA ['requerimiento']; ?></div></td>
					 <td width="25%"><div align="center"><?php echo $row_consulta_respuestaSIA ['fecha_solicitud']; ?></div></td>
                      <td width="12%"><div align="center"><span class="Estilo54"><?php echo $row_consulta_respuestaSIA ['diferencia']; ?></span></div></td>
					  
					 <td width="15%"><div align="center" class="Estilo2"><?php echo $row_consulta_respuestaSIA  ['oracle']; ?><?php echo $row_consulta_respuestaSIA  ['dsd']; ?> <?php echo $row_consulta_respuestaSIA  ['barcel_pro']; ?> <?php echo $row_consulta_respuestaSIA  ['lib_ruta']; ?> <?php echo $row_consulta_respuestaSIA  ['qlik_view']; ?> <?php echo $row_consulta_respuestaSIA  ['kronos']; ?> <?php echo $row_consulta_respuestaSIA  ['portal_seg']; ?> <?php echo $row_consulta_respuestaSIA  ['meta4']; ?> <?php echo $row_consulta_respuestaSIA  ['internet']; ?> <?php echo $row_consulta_respuestaSIA  ['nomina']; ?> <?php echo $row_consulta_respuestaSIA  ['acl']; ?> <?php echo $row_consulta_respuestaSIA  ['easitrax']; ?></div></td>
                      <td width="15%"><div align="center" class="Estilo2"><?php echo $row_consulta_respuestaSIA  ['solicitante']; ?></div></td>
                    </tr>
                    <?php } while ($row_consulta_respuestaSIA  = mysql_fetch_assoc($consulta_respuestaSIA)); ?>
                  </table>
                  <input type="hidden" name="MM_update" value="form1">
                  <input type="hidden" name="id" value="<?php echo $row_consulta_respuestaSIA ['id']; ?>">
                </form>
              </div>
              <br/>
              <?php 
  }

?>

  		</div>
</div>




<div id="div_sia_cerrados" style="display:none" >

<form id="form2" name="form2" method="post" action="descargarSIA_cerrados.php">

  <p align="left"><strong>-EXPORTAR SOLICITUDES </strong><img src="../imagenes/export_to_excel.gif" alt="exportar_Excel" width="28" height="17"  />
    <input type="submit" name="Descargar" class="button themed" id="Descargar" value="DESCARGAR" />
	

    </label>
  </p>
</form>
	
<div align="center" class="resultados">
              <!--<img src="imagenes/alerta1.gif" title="QUITAR PARAMETROS" width="40" height="40" border="0" />-->
              <?php
if (mysql_num_rows($consulta_cerradoSIA) == 0) { 

	   echo nl2br ("\n \n NO HAY SOLICITUDES CON ESTATUS CERRADO"); 
   
   } 
else { 

?>


</div>
            <div align="center" class="notificacion">
              <?php
  echo  nl2br ("\n RESULTADOS CON ESTATUS CERRADO");
  
  ?>
   <br/>
    <br/>
    
   TOTAL <?php echo $totalRows_consulta_cerradoSIA ?> REGISTROS</br>
  <br/>
    <br/>
              <div class="CSSTableGenerator">
                <form method="post" name="form1" class= "reporte" action="">
                  <table width="50%" border="1" align="center">
                    <tr>
                      <td width="12%"><div align="center"><strong>FOLIO</strong></div></td>
                      <td width="25%"><div align="center"><strong>ESTATUS</strong></div></td>
					  <td width="12%"><div align="center"><strong>FECHA SOLICITUD</strong></div></td>
					  <td width="12%"><div align="center"><strong>FECHA ENTREGA</strong></div></td>
                      <td width="25%"><div align="center"><strong>CENTRO DE TRABAJO</strong></div></td>
					   <td width="12%"><div align="center"><strong>SISTEMAS DE LOS QUE SE REQUIRE INFORMACIÓN</strong></div></td>  
                      <td width="25%"><div align="center"><strong>REQUERIMIENTO</strong></div></td>
					  <td width="25%"><div align="center"><strong>ACCIONES Y HALLAZGOS</strong></div></td>
					  <td width="15%"><div align="center"><strong>SOLICITANTE</strong></div></td>	

                      
                    </tr>
			<?php
			while ($row_consulta_cerradoSIA = mysql_fetch_array($consulta_cerradoSIA)) { 
			$color = array( 
			'ABIERTO' => '#990000',
			'CERRADO' => 'GREEN',
		); 
			?>	
                    <tr>
                      <td width="12%"><div align="center">INV-I<?php echo $row_consulta_cerradoSIA ['id']; ?></div></td>
					  <?php echo("<td style='text-align: center; color: white; background-color:" . $color[$row_consulta_cerradoSIA['estatus']] . ";'>"), $row_consulta_cerradoSIA['estatus'];  ?>
					  <td width="25%"><div align="center"><?php echo $row_consulta_cerradoSIA ['fecha_solicitud']; ?></div></td>
					  <td width="25%"><div align="center"><?php echo $row_consulta_cerradoSIA ['fecha_entrega']; ?></div></td>
                      <td width="25%"><div align="center"><?php echo $row_consulta_cerradoSIA ['select2']; ?> / <?php echo $row_consulta_cerradoSIA ['select3']; ?></div></td>
					  <td width="15%"><div align="center" class="Estilo2"> <?php echo $row_consulta_cerradoSIA  ['oracle']; ?><?php echo $row_consulta_cerradoSIA  ['dsd']; ?> <?php echo $row_consulta_cerradoSIA  ['barcel_pro']; ?> <?php echo $row_consulta_cerradoSIA  ['lib_ruta']; ?> <?php echo $row_consulta_cerradoSIA  ['qlik_view']; ?> <?php echo $row_consulta_cerradoSIA  ['kronos']; ?> <?php echo $row_consulta_cerradoSIA  ['portal_seg']; ?> <?php echo $row_consulta_cerradoSIA  ['meta4']; ?> <?php echo $row_consulta_cerradoSIA  ['internet']; ?> <?php echo $row_consulta_cerradoSIA  ['nomina']; ?> <?php echo $row_consulta_cerradoSIA  ['acl']; ?> <?php echo $row_consulta_cerradoSIA  ['easitrax']; ?></div></td>
                      <td width="25%"><div align="center"><?php echo $row_consulta_cerradoSIA ['requerimiento']; ?></div></td>						
					  <td width="25%"><div align="center"><?php echo $row_consulta_cerradoSIA ['acciones_hallazgos']; ?></div></td>
					  <td width="15%"><div align="center" class="Estilo2"><?php echo $row_consulta_cerradoSIA ['solicitante']; ?></div></td>

                    </tr>

					 <?php } while ($row_consulta_cerradoSIA = mysql_fetch_assoc($consulta_cerradoSIA)); ?>
					 
                  </table>
                  <input type="hidden" name="MM_update" value="form1">
                  <input type="hidden" name="id" value="<?php echo $row_consulta_cerradoSIA ['id']; ?>">
                </form>
              </div>
              <br/>
              <?php 
  }

?>

  		</div>
</div>




</body>
</html>
<?php
mysql_free_result($consulta_SIA);
mysql_free_result($consulta_cerradoSIA);

?>