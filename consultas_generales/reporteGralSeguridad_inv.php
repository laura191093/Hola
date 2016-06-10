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
$query_consulta_usuarioTable = sprintf("SELECT Nombre FROM supervisores WHERE username = '%s'", $colname_consulta_usuarioTable);
$consulta_usuarioTable = mysql_query($query_consulta_usuarioTable, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuarioTable = mysql_fetch_assoc($consulta_usuarioTable);
$totalRows_consulta_usuarioTable = mysql_num_rows($consulta_usuarioTable);

$colname_RecordsetTable = "-1";
if (isset($_POST['supervisores'])) {
  $colname_RecordsetTable = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_Asaltos = sprintf("SELECT a. id, r.opcion as region, c.opcion as select2, e.opcion as select3, a.semana, a.organizacion, a.area, a.fecha, a.nombrev,
a.horario, a.calle, a.colonia, a.cliente, a.averiguacion, a.delegacion, a.jefe, a.afectacione, a.afectacion_02,
a.afectacionp, a.afectacionc, a.recuperacion, a.handheld_impresora, a.tinas_bandejas, a.ruta, a.canal, a.cumplio, a.medidas, a.supervisor, a.narracion,
a.seguimiento, a.estatus, a.fin_reporte, a.nombre_vendedor2, a.num_colaborador, a.num_colaborador2,
a.lesion, a.especifique_lesion, a.atendio, a.fisico, a.comentarios_fisico, a.motivo_borrado, a.nombre_actualizo,
a.robo_vhs, a.tipo_arma, a.turno, a.anio, s.estatura, s.complexion, s.vestimenta, s.color_piel, s.cabello,
s.cicatriz, s.tatuajes, s.o_caract_p, s.estatura2, s.complexion2,s.vestimenta2, s.color_piel2, s.cabello2, s.cicatriz2,
s.tatuajes2, s.o_caract_p2, s.num_sujetos, s.edad, s.edad2, dv.tipo, dv.marca, dv.color, dv.o_caract_v, dv.placas
FROM asaltos a
LEFT JOIN region r ON r.id_region = a.region
LEFT JOIN select_2 c ON c.id = a.select2
LEFT JOIN select_3 e ON e.id = a.select3
LEFT JOIN senas_particulares s ON s.id_fk = a.id LEFT JOIN datos_vehiculo dv ON dv .id_fkv = a.id WHERE supervisor = '%s' ORDER BY id DESC", $colname_RecordsetTable);
$Recordset_Asaltos = mysql_query($query_Asaltos, $conexion_usuarios) or die(mysql_error());
$row_Asaltos = mysql_fetch_assoc($Recordset_Asaltos);
$totalRows_Asaltos = mysql_num_rows($Recordset_Asaltos);


mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_detenidos_Table= sprintf("SELECT d.id, d.semana, r.opcion as region, c.opcion as select2, e.opcion as select3, d.organizacion,
d.fecha,d.monto_afectacion, d.nombre, d.lugar_de_nacimiento, d.edad, d.estado_civil,
d.profesion, d.estado, d.calle, d.colonia, d.municipio, d.especialidad_delictiva,
d.apodo, d.carpeta_de_investigacion, d.consignado_a, d.fecha_de_consignacion, d.juzgado_penal,
d.causa_penal, d.fecha_de_sentencia, d.condena, d.supervisor, d.narracion, d.seguimiento,
d.sentenciado, d.estatura, d.complexion, d.peso, d.color_de_piel, d.contorno_facial,
d.tipo_de_pelo, d.color_de_pelo, d.frente, d.cejas, d.ojos, d.color_de_ojos, d.tipo_de_nariz,
d.bigote, d.tipo_de_boca, d.labios, d.menton, d.cicatriz, d.tatuajes, d.deformacion_fisica,
d.Imagen, d.recuperacion, d.fin_reporte, d.atendio FROM detenido d
LEFT JOIN region r ON r.id_region = d.region
LEFT JOIN select_2 c ON c.id = d.select2
LEFT JOIN select_3 e ON e.id = d.select3 WHERE d.supervisor= '%s' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_detenidos_Table = mysql_query($query_consulta_detenidos_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_detenidos_Table = mysql_fetch_assoc($consulta_detenidos_Table);
$totalRows_consulta_detenidos_Table = mysql_num_rows($consulta_detenidos_Table);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_robos_Table = sprintf("SELECT r.id, r.semana, r.organizacion,  r.area, r.fecha, re.opcion as region, c.opcion as select2,
e.opcion as select3, r.nombre_del_conductor, r.puesto, r.horario, r.calle, r.cliente, r.colonia,
r.averiguacion_previa, r.municipio, r.jefe_inmediato, r.marca, r.placa, r.motor, r.ruta,
r.año, r.canal, r.precio, r.cumplio_medidas, r.medidas, r.economico, r.supervisor_de_seguridad,
r.recuperado, r.cancelacion, r.narracion, r.seguimiento, r.estatus, r.fin_reporte, r.num_empleado,
r.atendio, r.motivo_borrado, r.fisico, r.comentarios_fisico, r.nombre_actualizo
FROM robovhs r
LEFT JOIN region re ON re.id_region = r.region
LEFT JOIN select_2 c ON c.id = r.select2
LEFT JOIN select_3 e ON e.id = r.select3 WHERE r.supervisor_de_seguridad = '%s' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_robos_Table = mysql_query($query_consulta_robos_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_robos_Table = mysql_fetch_assoc($consulta_robos_Table);
$totalRows_consulta_robos_Table = mysql_num_rows($consulta_robos_Table);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_bajas_Table = sprintf("SELECT b.id, b.semana,  b.organizacion, r.opcion as region, c.opcion as select2,
e.opcion as select3, b.area,
b.fecha, b.ilicito, b.num_creditos, b.nombre_del_vendedor,
b.cliente, b.puesto, b.jefe_inmediato, b.ruta, b.canal,
b.afectacion_efectivo, b.afectacion02, b.afectacion_producto, b.recuperacion,
b.supervisores, b.narraciones, b.seguimiento, b.estatus, b.fin_reporte,
b.atendio, b.motivo_borrado, b.num_empleado FROM bajas b
LEFT JOIN region r ON r.id_region = b.region
LEFT JOIN select_2 c ON c.id = b.select2
LEFT JOIN select_3 e ON e.id = b.select3 WHERE b.supervisores = '%s' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_bajas_Table = mysql_query($query_consulta_bajas_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_bajas_Table = mysql_fetch_assoc($consulta_bajas_Table);
$totalRows_consulta_bajas_Table = mysql_num_rows($consulta_bajas_Table);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_re_Table = sprintf("SELECT r.id, r.semana, r.organizacion, r.area, re.opcion as region, c.opcion as select2,
e.opcion as select3,r.fecha, r.reporte, r.nombre_del_vendedor, r.horario, r.calle,
 r.colonia, r.cliente, r.averiguacion, r.delegacion, r.jefe, r.canal,
 r.ruta, r.bandejas, r.tinas, r.dollys, r.maquina_autovend, r.afectacione,
 r.valor_recuperacion, r.piezas_producto, r.cumplio, r.medidas, r.supervisor,
 r.narracion, r.seguimiento, r.estatus, r.fin_reporte, r.recuperacion_producto,
 r.recuperacion_equipo, r.atendio, r.nombre_actualizo, r.fisico, r.comentarios_fisico,
 r.turno FROM robo_equipo r
LEFT JOIN region re ON re.id_region = r.region
LEFT JOIN select_2 c ON c.id = r.select2
LEFT JOIN select_3 e ON e.id = r.select3 WHERE r.supervisor= '%s' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_re_Table = mysql_query($query_consulta_re_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_re_Table = mysql_fetch_assoc($consulta_re_Table);
$totalRows_consulta_re_Table = mysql_num_rows($consulta_re_Table);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_secciones_Table = sprintf( "SELECT s.id, r.opcion as region, c.opcion as select2, s.semana, s.organizacion, s.jefe_inmediato, s.fecha,
s.s_reportar,s.objetivo, s.supervisores, s.observaciones, s.estatus,
s.fin_seccion, s.atendio
FROM secciones s
LEFT JOIN region r ON r.id_region = s.region
LEFT JOIN select_2 c ON c.id = s.select2 WHERE s.supervisores= '%s' ORDER BY id DESC", $colname_RecordsetTable);
$consulta_secciones_Table = mysql_query($query_consulta_secciones_Table, $conexion_usuarios) or die(mysql_error());
$row_consulta_secciones_Table = mysql_fetch_assoc($consulta_secciones_Table);
$totalRows_consulta_secciones_Table = mysql_num_rows($consulta_secciones_Table);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_ExtendidaTable = sprintf("SELECT s.id, s.semana, s.organizacion, r.opcion as region, c.opcion as select2, e.opcion as select3,
	s.area, s.puesto, s.fecha, s.nombre, s.sexo, s.colonia, s.peligro, s.municipio,
	s.s_casa, s.s_ceve, s.t_cace, s.t_ceca, s.transporte, s.accidente,
	s.t_accidente, s.supervisor, s.recomendacion, s.fin_reporte FROM seguridad.seg_extendida s
	LEFT JOIN region r ON r.id_region = s.region
	LEFT JOIN select_2 c ON c.id = s.select2
	LEFT JOIN select_3 e ON e.id = s.select3 WHERE s.supervisor= '%s'' ORDER BY id DESC", $colname_RecordsetTable);
$Recordset_ExtendidaTable = mysql_query($query_ExtendidaTable, $conexion_usuarios) or die(mysql_error());
$row_ExtendidaTable = mysql_fetch_assoc($Recordset_ExtendidaTable);
$totalRows_ExtendidaTable = mysql_num_rows($Recordset_ExtendidaTable);

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_CETable = sprintf( "SELECT ce.id, ce.fecha, ce.semana, r.opcion as region, c.opcion as select2, e.opcion as select3, ce.marca, ce.nombre, ce.num_colaborador, ce.puesto,
ce.act_evaluada, ce.resultado, ce.comentarios, ce.fecha_captura,
ce.fechaAut, ce.afectacion, ce.recuperacion FROM carpeta_electronica ce
LEFT JOIN region r ON r.id_region = ce.region
LEFT JOIN select_2 c ON c.id = ce.select2
LEFT JOIN select_3 e ON e.id = ce.select3 WHERE ce.nombre= '%s' ORDER BY id DESC", $colname_RecordsetTable);
$Recordset_CETable = mysql_query ($query_CETable,$conexion_usuarios)or die (mysql_error());
$row_CETable = mysql_fetch_assoc($Recordset_CETable);
$totalRows_CarpetaTable = mysql_num_rows($Recordset_CETable);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<link type="text/css" href="../style/style_directorio_select.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../style/style_campos.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../style/estilo_Tabla.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte General</title>

<style type="text/css">
<!--
.Estilo10 {color: #003399; }
.Estilo11 {
	color: #990000;
	font-size: 14px;
}
.Estilo12 {font-size: 14px}
-->
</style>


<script type="text/javascript">
<!--
function mostrarReferencia(){
//Si la opcion con id resultado[1] (dentro del documento > formulario con name form1 >     y a la vez dentro del array de resultado) esta activada
if (document.form1.resultado[1].checked == true) {
//muestra (cambiando la propiedad display del estilo) el div con id 'mostrarDivComentarios'
document.getElementById('mostrarDivAsaltos').style.display='block';
document.getElementById('mostrarDivComentariosT').style.display='block';
//por el contrario, si no esta seleccionada
} else {
//oculta el div con id 'mostrarDivComentarios'
document.getElementById('mostrarDivComentarios').style.display='none';
document.getElementById('mostrarDivComentariosT').style.display='none';
}
}
-->
</script>

<script src="../jquery/jquery-1.3.2.min.js" type="text/javascript"></script>	
<script>
$(document).ready(function(){
	$("#ckb_asaltos").click(function(evento){
		if ($("#ckb_asaltos").attr("checked")){
			$("#div_Asaltos").css("display", "block");
			$("#div_Asaltos2").css("display", "block");

		}else{
			$("#div_Asaltos").css("display", "none");
			$("#div_Asaltos2").css("display", "none");
		}
	});
});

$(document).ready(function(){
	$("#ckb_bajas").click(function(evento){	
			if ($("#ckb_bajas").attr("checked")){
			$("#div_Bajas").css("display", "block");
			$("#div_Bajas2").css("display", "block");
		}else{
		$("#div_Bajas").css("display", "none");
		$("#div_Bajas2").css("display", "none");
		}
	});
});


$(document).ready(function(){
	$("#ckb_robovhs").click(function(evento){	
			if ($("#ckb_robovhs").attr("checked")){
			$("#div_RoboVHS").css("display", "block");
			$("#div_Robovhs2").css("display", "block");
		}else{
		$("#div_RoboVHS").css("display", "none");
		$("#div_Robovhs2").css("display", "none");
		}
	});
});

$(document).ready(function(){
	$("#ckb_detenidos").click(function(evento){	
			if ($("#ckb_detenidos").attr("checked")){
			$("#div_Detenidos").css("display", "block");
			$("#div_Detenidos2").css("display", "block");
		}else{
		$("#div_Detenidos").css("display", "none");
		$("#div_Detenidos2").css("display", "none");
		}
	});
});

$(document).ready(function(){
	$("#ckb_roboEquipo").click(function(evento){	
			if ($("#ckb_roboEquipo").attr("checked")){
			$("#div_RoboEquipo").css("display", "block");
			$("#div_RoboEquipo2").css("display", "block");
		}else{
		$("#div_RoboEquipo").css("display", "none");
		$("#div_RoboEquipo2").css("display", "none");
		}
	});
});

$(document).ready(function(){
	$("#ckb_secciones").click(function(evento){	
			if ($("#ckb_secciones").attr("checked")){
			$("#div_Secciones").css("display", "block");
			$("#div_secciones2").css("display", "block");
		}else{
		$("#div_Secciones").css("display", "none");
		$("#div_secciones2").css("display", "none");
		}
	});
});


$(document).ready(function(){
	$("#ckb_segExtendida").click(function(evento){	
			if ($("#ckb_segExtendida").attr("checked")){
			$("#div_SegExtendida").css("display", "block");
			$("#div_SegExtendida2").css("display", "block");
		}else{
		$("#div_SegExtendida").css("display", "none");
		$("#div_SegExtendida2").css("display", "none");
		}
	});
});

$(document).ready(function(){
	$("#ckb_carpeta").click(function(evento){	
			if ($("#ckb_carpeta").attr("checked")){
			$("#divCarpeta").css("display", "block");
			$("#divCarpeta2").css("display", "block");
		}else{
		$("#divCarpeta").css("display", "none");
		$("#divCarpeta2").css("display", "none");
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
					<h1>REPORTE GENERAL DE SEGURIDAD</h1>
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
	<img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="../principal_inv.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>
</br>

<form id="form2" name="form2" method="post" action="descargarReporteGral_inv.php">
  <label></label>
  <p align="left"><strong>-EXPORTAR REPORTE  </strong> 
    <select name="supervisores" id="supervisores" style="display: none;">
      
      <?php
do {  
?>
      <option value="<?php echo $row_consulta_usuario['Nombre']?>"<?php if (!(strcmp($row_consulta_usuario['Nombre'], $_POST['nombre']))) {echo "selected=\"selected\"";} ?>><?php echo $row_consulta_usuario['Nombre']?></option>
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
    <img src="../imagenes/export_to_excel.gif" alt="exportar_Excel" width="28" height="17"  />
    <input type="submit" name="Descargar" class="button themed" id="Descargar" value="DESCARGAR" />
  </p>
</form>
<br />
<form id="form1" name="form1" method="post" action="reporteGralSeguridad_inv.php">
  <label></label>
  <p><strong>-CLIC EN CONSULTAR, SELECCIONAR CATEGORIA A BUSCAR </strong>
    <select name="supervisores" id="supervisores" onchange="submit();mostrarDiv(this.value)">

      <?php
do {  
?>
      <option value="<?php echo $row_consulta_usuario['Nombre']?>"<?php if (!(strcmp($row_consulta_usuario['Nombre'], $_POST['nombre']))) {echo "selected=\"selected\"";} ?>><?php echo $row_consulta_usuario['Nombre']?></option>
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
    <input type="submit" name="Consultar" id="Consultar" class="button themed" value="CONSULTAR" />
    <br />
  </p>
  <div id="contenidoAjax" class="divMenú" >
</p>
    <input type="checkbox" name="ckb_asaltos" value="1" id="ckb_asaltos"> 
  ASALTOS  </p>
  <div class="Estilo10" id="div_Asaltos2" style="display: none;"><span class="Estilo10"> <?php echo $totalRows_Asaltos?> ASALTOS INGRESADOS AL PORTAL WEB</span></div>
  <p>
  <input type="checkbox" name="ckb_detenidos" value="4" id="ckb_detenidos"> 
  DETENIDOS</p>
  <div id="div_Detenidos2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_consulta_detenidos_Table?> DETENIDOS  INGRESADOS AL PORTAL WEB </p>
  </div>
   <input type="checkbox" name="ckb_robovhs" value="3" id="ckb_robovhs"> 
    ROBO DE VEHICULO</p>
  <div id="div_Robovhs2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_consulta_robos_Table ?> ROBOS DE VEHICULOS  INGRESADOS AL PORTAL WEB</p>
  </div>
  <p>
    <input type="checkbox" name="ckb_bajas" value="2" id="ckb_bajas">
    BAJAS</p>
  <div id="div_Bajas2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_consulta_bajas_Table ?> BAJAS  INGRESADAS AL PORTAL WEB  </p>
  </div>
  <p>
    <input type="checkbox" name="ckb_roboEquipo" value="5" id="ckb_roboEquipo"> 
    ROBO DE EQUIPO</p>
  <div id="div_RoboEquipo2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_consulta_re_Table?> ROBOS DE EQUIPOS  INGRESADOS AL PORTAL WEB</p>
  </div>
  <p>
    <input type="checkbox" name="ckb_secciones" value="6" id="ckb_secciones"> 
  SECCIONES</p>
  <div id="div_secciones2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_consulta_secciones_Table?> SECCIONES  INGRESADOS AL PORTAL WEB </p>
  </div>
  <p>
    <input type="checkbox" name="ckb_segExtendida" value="7" id="ckb_segExtendida"> 
    SEGURIDAD EXTENDIDA
</p>
  <div id="div_SegExtendida2" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_ExtendidaTable?> SEGURIDAD EXTENDIDA INGRESADOS AL PORTAL WEB </p>
  </div>
  <p>
    <input type="checkbox" name="ckb_carpeta" value="8" id="ckb_carpeta" /> 
    CARPETA ELECTR&Oacute;NICA
</p>
  <div id="divCarpeta" style="display: none;">
    <p class="Estilo10"><?php echo $totalRows_CarpetaTable?> CARPETA ELECTR&Oacute;NICA INGRESADOS AL PORTAL WEB </p>
  </div>
  </div>
</form>
<br/>
<p>

<div id="div_Asaltos" style="display: none;">
    <p align="center" class="Estilo4"><strong>ASALTOS</strong></p>
	<div class="CSSTableGenerator" >
    <table width="2628" border="1" align="center" cellspacing="1" id="Exportar_a_Excel">
      <tr>
        <td width="136"><div align="center">FOLIO ASALTO </div></td>
        <td width="134"><div align="center">SEMANA</div></td>
        <td width="134"><div align="center">FECHA</div></td>
        <td width="164"><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td width="127"><div align="center">REGI&Oacute;N</div></td>
        <td width="155"><div align="center">CENTRO DE VENTAS</div></td>
        <td width="164"><div align="center">ENTIDAD</div></td>
        <td width="165"><div align="center">NOMBRE DEL VENDEDOR #1</div></td>	
		<td width="165"><div align="center">NUM. EMPLEADO #1</div></td>
		
		<td width="165"><div align="center">NOMBRE DEL VENDEDOR #2</div></td>
		<td width="165"><div align="center">NUM. EMPLEADO #2</div></td>
        <td width="182"><div align="center">RUTA</div></td>
        <td width="182"><div align="center">CLIENTE</div></td>
        <td width="623"><div align="center">CALLE</div></td>
        <td width="171"><div align="center">COLONIA</div></td>
        <td width="182"><div align="center">DELEGACI&Oacute;N</div></td>
        <td width="199"><div align="center">AREA</div></td>
        <td width="192"><div align="center">HORARIO</div></td>
        <td width="182"><div align="center">AVERIGUACI&Oacute;N</div></td>
        <td width="182"><div align="center">JEFE</div></td>
        <td width="182"><div align="center">AFECTACI&Oacute;N EN EFECTIVO</div></td>
        <td width="182"><div align="center">RECARGAS ELECTRONICAS</div></td>
        <td width="182"><div align="center">AFECTACION PRODUCTO</div></td>
        <td width="182"><div align="center">AFECTACION APERTURA DE CAJA</div></td>
        <td width="182"><div align="center">RECUPERACION</div></td>
        <td width="182"><div align="center">AFECTACIÓN HAND HELD / IMPRESORA</div></td>
        <td width="182"><div align="center">EQUIPO</div></td>
        <td width="182"><div align="center">CANAL</div></td>
        <td width="182"><div align="center">CUMPLIO</div></td>
        <td width="182"><div align="center">MEDIDAS</div></td>
		
		<td width="182"><div align="center">LESIONADO</div></td>
		<td width="182"><div align="center">ESPECIFIQUE LESIÓN</div></td>
		
        <td width="182"><div align="center">NARRACI&Oacute;N</div></td>
        <td width="182"><div align="center">ESTATUS</div></td>
        <td width="182"><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  	 <td><a href="../detalle_a_sup.php?recordID=<?php echo $row_Asaltos['id']; ?>">A<?php echo $row_Asaltos['id']; ?></a></td>
    <td><?php echo $row_Asaltos['semana']; ?></td>
        <td><?php echo $row_Asaltos['fecha']; ?></td>
        <td><?php echo $row_Asaltos['organizacion']; ?></td>
        <td><?php echo $row_Asaltos['region']; ?></td>
        <td><?php echo $row_Asaltos['select2']; ?></td>
        <td><?php echo $row_Asaltos['select3']; ?></td>
        <td><?php echo $row_Asaltos['nombrev']; ?></td>
		<td><?php echo $row_Asaltos['num_colaborador']; ?></td>
		<td><?php echo $row_Asaltos['nombre_vendedor2']; ?></td>
		<td><?php echo $row_Asaltos['num_colaborador2']; ?></td>
        <td><?php echo $row_Asaltos['ruta']; ?></td>
        <td><?php echo $row_Asaltos['cliente']; ?></td>
        <td><?php echo $row_Asaltos['calle']; ?></td>
        <td><?php echo $row_Asaltos['colonia']; ?></td>
        <td><?php echo $row_Asaltos['delegacion']; ?></td>
        <td><?php echo $row_Asaltos['area']; ?></td>
        <td><?php echo $row_Asaltos['horario']; ?></td>
        <td><?php echo $row_Asaltos['averiguacion']; ?></td>
        <td><?php echo $row_Asaltos['jefe']; ?></td>
        <td><?php echo $row_Asaltos['afectacione']; ?></td>
        <td><?php echo $row_Asaltos['afectacion_02']; ?></td>
        <td><?php echo $row_Asaltos['afectacionp']; ?></td>
        <td><?php echo $row_Asaltos['afectacionc']; ?></td>
        <td><?php echo $row_Asaltos['recuperacion']; ?></td>
        <td><?php echo $row_Asaltos['handheld_impresora']; ?></td>
        <td><?php echo $row_Asaltos['tinas_bandejas']; ?></td>
        <td><?php echo $row_Asaltos['canal']; ?></td>
        <td><?php echo $row_Asaltos['cumplio']; ?></td>
        <td><?php echo $row_Asaltos['medidas']; ?></td>
		
		<td><?php echo $row_Asaltos['lesion']; ?></td>
		<td><?php echo $row_Asaltos['especifique_lesion']; ?></td>
		
        <td><?php echo $row_Asaltos['narracion']; ?></td>
        <td><?php echo $row_Asaltos['estatus']; ?></td>
        <td><?php echo $row_Asaltos['supervisor']; ?></td>
      </tr>
      <?php } while ($row_Asaltos = mysql_fetch_assoc($Recordset_Asaltos)); ?>
    </table>
</div>
</div>
  <div id="div_Bajas" style="display: none;">
    <p align="center" class="Estilo4"><strong>BAJAS</strong></p>
	<div class="CSSTableGenerator" >
    <table border="1" align="center" cellspacing="1"  id="Exportar_a_Excel_b">
      <tr>
        <td><div align="center">FOLIO BAJA</div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS </div></td>
        <td><div align="center">ENTIDAD</div></td>
        <td><div align="center">NOMBRE DEL VENDEDOR </div></td>
        <td><div align="center">RUTA</div></td>
        <td><div align="center">CLIENTE</div></td>
        <td><div align="center">AREA</div></td>
        <td><div align="center">ILICITO</div></td>
        <td><div align="center">NUMERO DE CREDITOS </div></td>
        <td><div align="center">PUESTO</div></td>
        <td><div align="center">JEFE INMEDIATO </div></td>
        <td><div align="center">CANAL</div></td>
        <td><div align="center">AFECTACI&Oacute;N EFECTIVO </div></td>
        <td><div align="center">RECARGAS ELECTRONICAS </div></td>
        <td><div align="center">AFECTACI&Oacute;N PRODUCTO </div></td>
        <td><div align="center">RECUPERACI&Oacute;N</div></td>
        <td><div align="center">NARRACI&Oacute;N</div></td>
        <td><div align="center">SEGUIMIENTO</div></td>
        <td><div align="center">ESTATUS</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  <td><a href="../detalle_b_sup.php?recordID=<?php echo $row_consulta_bajas_Table['id']; ?>">B<?php echo $row_consulta_bajas_Table['id']; ?></a></td>
        <td><?php echo $row_consulta_bajas_Table['semana']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['fecha']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['organizacion']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['region']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['select2']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['select3']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['nombre_del_vendedor']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['ruta']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['cliente']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['area']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['ilicito']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['num_creditos']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['puesto']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['jefe_inmediato']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['canal']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['afectacion_efectivo']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['afectacion02']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['afectacion_producto']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['recuperacion']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['narraciones']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['seguimiento']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['estatus']; ?></td>
        <td><?php echo $row_consulta_bajas_Table['supervisores']; ?></td>
      </tr>
      <?php } while ($row_consulta_bajas_Table = mysql_fetch_assoc($consulta_bajas_Table)); ?>
    </table>
  </div>
</div>
  <div id="div_RoboVHS" style="display: none;">
    <p align="center" class="Estilo4"><strong>ROBOS DE VEHICULOS</strong></p>
		<div class="CSSTableGenerator" >
    <table border="1" align="center">
      <tr>
        <td><div align="center">FOLIO VEHICULO</div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS</div></td>
        <td><div align="center">ENTIDAD</div></td>
        <td><div align="center">NOMBRE DEL CONDUCTOR </div></td>
        <td><div align="center">RUTA</div></td>
        <td><div align="center">PUESTO</div></td>
        <td><div align="center">AREA</div></td>
        <td><div align="center">CLIENTE</div></td>
        <td><div align="center">HORARIO</div></td>
        <td><div align="center">CALLE</div></td>
        <td><div align="center">COLONIA</div></td>
        <td><div align="center">MUNICIPIO</div></td>
        <td><div align="center">AVERIGUACI&Oacute;N PREVIA </div></td>
        <td><div align="center">JEFE INMEDIATO </div></td>
        <td><div align="center">MARCA</div></td>
        <td><div align="center">PLANTA</div></td>
        <td><div align="center">MOTOR</div></td>
        <td><div align="center">A&Ntilde;O</div></td>
        <td><div align="center">CANAL</div></td>
        <td><div align="center">PRECIO</div></td>
        <td><div align="center">CUMPLIO MEDIDAS </div></td>
        <td><div align="center">MEDIDAS</div></td>
        <td><div align="center">NUM. ECO.</div></td>
        <td><div align="center">RECUPERADO</div></td>
        <td><div align="center">CANCELACI&Oacute;N</div></td>
        <td><div align="center">NARRACI&Oacute;N</div></td>
        <td><div align="center">SEGUIMIENTO</div></td>
        <td><div align="center">ESTATUS</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  <td><a href="../detalle_v_sup.php?recordID=<?php echo $row_consulta_robos_Table['id']; ?>">VH<?php echo $row_consulta_robos_Table['id']; ?></a></td>
        <td><?php echo $row_consulta_robos_Table['semana']; ?></td>
        <td><?php echo $row_consulta_robos_Table['fecha']; ?></td>
        <td><?php echo $row_consulta_robos_Table['organizacion']; ?></td>
        <td><?php echo $row_consulta_robos_Table['region']; ?></td>
        <td><?php echo $row_consulta_robos_Table['select2']; ?></td>
        <td><?php echo $row_consulta_robos_Table['select3']; ?></td>
        <td><?php echo $row_consulta_robos_Table['nombre_del_conductor']; ?></td>
        <td><?php echo $row_consulta_robos_Table['ruta']; ?></td>
        <td><?php echo $row_consulta_robos_Table['puesto']; ?></td>
        <td><?php echo $row_consulta_robos_Table['area']; ?></td>
        <td><?php echo $row_consulta_robos_Table['cliente']; ?></td>
        <td><?php echo $row_consulta_robos_Table['horario']; ?></td>
        <td><?php echo $row_consulta_robos_Table['calle']; ?></td>
        <td><?php echo $row_consulta_robos_Table['colonia']; ?></td>
        <td><?php echo $row_consulta_robos_Table['municipio']; ?></td>
        <td><?php echo $row_consulta_robos_Table['averiguacion_previa']; ?></td>
        <td><?php echo $row_consulta_robos_Table['jefe_inmediato']; ?></td>
        <td><?php echo $row_consulta_robos_Table['marca']; ?></td>
        <td><?php echo $row_consulta_robos_Table['placa']; ?></td>
        <td><?php echo $row_consulta_robos_Table['motor']; ?></td>
        <td><?php echo $row_consulta_robos_Table['a&ntilde;o']; ?></td>
        <td><?php echo $row_consulta_robos_Table['canal']; ?></td>
        <td><?php echo $row_consulta_robos_Table['precio']; ?></td>
        <td><?php echo $row_consulta_robos_Table['cumplio_medidas']; ?></td>
        <td><?php echo $row_consulta_robos_Table['medidas']; ?></td>
        <td><?php echo $row_consulta_robos_Table['economico']; ?></td>
        <td><?php echo $row_consulta_robos_Table['recuperado']; ?></td>
        <td><?php echo $row_consulta_robos_Table['cancelacion']; ?></td>
        <td><?php echo $row_consulta_robos_Table['narracion']; ?></td>
        <td><?php echo $row_consulta_robos_Table['seguimiento']; ?></td>
        <td><?php echo $row_consulta_robos_Table['estatus']; ?></td>
        <td><?php echo $row_consulta_robos_Table['supervisor_de_seguridad']; ?></td>
      </tr>
      <?php } while ($row_consulta_robos_Table = mysql_fetch_assoc($consulta_robos_Table)); ?>
    </table>
  </div>
</div>
  <div id="div_Detenidos" style="display: none;">
    <p align="center" class="Estilo4"><strong>DETENIDOS</strong></p>
	<div class="CSSTableGenerator" >
    <table border="1" align="center">
      <tr>
        <td height="25"><div align="center">FOLIO DETENIDO </div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS</div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">NOMBRE</div></td>
        <td><div align="center">MONTO AFECTACI&Oacute;N </div></td>
        <td><div align="center">LUGAR NACIMIENTO </div></td>
        <td><div align="center">EDAD</div></td>
        <td><div align="center">ESTADO CIVIL </div></td>
        <td><div align="center">PROFESI&Oacute;N</div></td>
        <td><div align="center">ESTADO</div></td>
        <td><div align="center">CALLE</div></td>
        <td><div align="center">COLONIA</div></td>
        <td><div align="center">MUNICIPIO</div></td>
        <td><div align="center">ESPECIALIDAD DELICTIVA </div></td>
        <td><div align="center">APODO</div></td>
        <td><div align="center">CARPETA DE INVESTIGACI&Oacute;N </div></td>
        <td><div align="center">CONSIGNADO A </div></td>
        <td><div align="center">FECHA DE CONSIGNACI&Oacute;N </div></td>
        <td><div align="center">JUZGADO PENAL </div></td>
        <td><div align="center">CAUSA PENAL </div></td>
        <td><div align="center">FECHA SENTENCIA </div></td>
        <td><div align="center">CONDENA</div></td>
        <td><div align="center">NARRACI&Oacute;N</div></td>
        <td><div align="center">SEGUIMIENTO</div></td>
        <td><div align="center">SENTENCIADO</div></td>
        <td><div align="center">ESTATURA</div></td>
        <td><div align="center">COMPLEXI&Oacute;N</div></td>
        <td><div align="center">PESO</div></td>
        <td><div align="center">COLOR DE PIEL </div></td>
        <td><div align="center">CONTORNO FACIAL </div></td>
        <td><div align="center">TIPO DE PELO </div></td>
        <td><div align="center">COLOR DE PELO </div></td>
        <td><div align="center">FRENTE</div></td>
        <td><div align="center">CEJAS</div></td>
        <td><div align="center">OJOS</div></td>
        <td><div align="center">COLOR DE OJOS </div></td>
        <td><div align="center">TIPO DE NARIZ </div></td>
        <td><div align="center">BIGOTE</div></td>
        <td><div align="center">TIPO DE BOCA </div></td>
        <td><div align="center">LABIOS</div></td>
        <td><div align="center">MENT&Oacute;N</div></td>
        <td><div align="center">CICATRIZ</div></td>
        <td><div align="center">TATUAJES</div></td>
        <td><div align="center">DEFORMACI&Oacute;N FISICA </div></td>
        <td><div align="center">IMAGEN</div></td>
        <td><div align="center">RECUPERACI&Oacute;N</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  <td><a href="../detalle_d_sup.php?recordID=<?php echo $row_consulta_detenidos_Table['id']; ?>">D<?php echo $row_consulta_detenidos_Table['id']; ?></a></td>
        <td><?php echo $row_consulta_detenidos_Table['semana']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['fecha']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['region']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['select2']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['organizacion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['nombre']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['monto_afectacion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['lugar_de_nacimiento']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['edad']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['estado_civil']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['profesion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['estado']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['calle']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['colonia']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['municipio']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['especialidad_delictiva']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['apodo']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['carpeta_de_investigacion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['consignado_a']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['fecha_de_consignacion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['juzgado_penal']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['causa_penal']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['fecha_de_sentencia']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['condena']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['narracion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['seguimiento']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['sentenciado']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['estatura']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['complexion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['peso']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['color_de_piel']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['contorno_facial']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['tipo_de_pelo']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['color_de_pelo']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['frente']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['cejas']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['ojos']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['color_de_ojos']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['tipo_de_nariz']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['bigote']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['tipo_de_boca']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['labios']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['menton']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['cicatriz']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['tatuajes']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['deformacion_fisica']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['Imagen']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['recuperacion']; ?></td>
        <td><?php echo $row_consulta_detenidos_Table['supervisor']; ?></td>
      </tr>
      <?php } while ($row_consulta_detenidos_Table = mysql_fetch_assoc($consulta_detenidos_Table)); ?>
    </table>
  </div>
</div>
  <div id="div_RoboEquipo" style="display: none;">
    <p align="center" class="Estilo4"><strong>ROBO DE EQUIPO</strong></p>
    	<div class="CSSTableGenerator" >
	<table border="1" align="center">
      <tr>
        <td><div align="center">FOLIO ROBO </div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS</div></td>
        <td><div align="center">ENTIDAD</div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">NOMBRE DEL VENDEDOR </div></td>
        <td><div align="center">RUTA</div></td>
        <td><div align="center">CLIENTE</div></td>
        <td><div align="center">AREA</div></td>
        <td><div align="center">REPORTE</div></td>
        <td><div align="center">HORARIO</div></td>
        <td><div align="center">CALLE</div></td>
        <td><div align="center">COLONIA</div></td>
        <td><div align="center">DELEGACI&Oacute;N</div></td>
        <td><div align="center">AVERIGUACI&Oacute;N</div></td>
        <td><div align="center">JEFE</div></td>
        <td><div align="center">CANAL</div></td>
		<td><div align="center">TOTAL ROBADO </div></td>
        <td><div align="center">BANDEJAS</div></td>
        <td><div align="center">CHAROLAS / TINAS</div></td>
        <td><div align="center">DOLLYS</div></td>
        <td><div align="center">PASCUALINEROS </div></td>
		
		<td><div align="center">VALOR EQUIPOS RECUPERADOS</div></td>
		
        <td><div align="center">PIEZAS DE PRODUCTO </div></td>
		<td><div align="center">VALOR PRODUCTOS RECUPERADOS</div></td>


        <td><div align="center">TOTAL DE RECUPERACI&Oacute;N </div></td>
        <td><div align="center">CUMPLIO</div></td>
        <td><div align="center">MEDIDAS</div></td>
        <td><div align="center">NARRACI&Oacute;N</div></td>
        <td><div align="center">SEGIMIENTO</div></td>
        <td><div align="center">ESTATUS</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  <td><a href="../detalle_e_sup.php?recordID=<?php echo $row_consulta_re_Table['id']; ?>">RE<?php echo $row_consulta_re_Table['id']; ?></a></td>
        <td><?php echo $row_consulta_re_Table['semana']; ?></td>
        <td><?php echo $row_consulta_re_Table['fecha']; ?></td>
        <td><?php echo $row_consulta_re_Table['region']; ?></td>
        <td><?php echo $row_consulta_re_Table['select2']; ?></td>
        <td><?php echo $row_consulta_re_Table['select3']; ?></td>
        <td><?php echo $row_consulta_re_Table['organizacion']; ?></td>
        <td><?php echo $row_consulta_re_Table['nombre_del_vendedor']; ?></td>
        <td><?php echo $row_consulta_re_Table['ruta']; ?></td>
        <td><?php echo $row_consulta_re_Table['cliente']; ?></td>
        <td><?php echo $row_consulta_re_Table['area']; ?></td>
        <td><?php echo $row_consulta_re_Table['reporte']; ?></td>
        <td><?php echo $row_consulta_re_Table['horario']; ?></td>
        <td><?php echo $row_consulta_re_Table['calle']; ?></td>
        <td><?php echo $row_consulta_re_Table['colonia']; ?></td>
        <td><?php echo $row_consulta_re_Table['delegacion']; ?></td>
        <td><?php echo $row_consulta_re_Table['averiguacion']; ?></td>
        <td><?php echo $row_consulta_re_Table['jefe']; ?></td>
        <td><?php echo $row_consulta_re_Table['canal']; ?></td>
	    <td><?php echo $row_consulta_re_Table['afectacione']; ?></td>
        <td><?php echo $row_consulta_re_Table['bandejas']; ?></td>
        <td><?php echo $row_consulta_re_Table['tinas']; ?></td>
        <td><?php echo $row_consulta_re_Table['dollys']; ?></td>
        <td><?php echo $row_consulta_re_Table['maquina_autovend']; ?></td>
		
		<td><?php echo $row_consulta_re_Table['recuperacion_equipo']; ?></td>		
        <td><?php echo $row_consulta_re_Table['piezas_producto']; ?></td>		
		<td><?php echo $row_consulta_re_Table['recuperacion_producto']; ?></td>
        <td><?php echo $row_consulta_re_Table['valor_recuperacion']; ?></td>
        <td><?php echo $row_consulta_re_Table['cumplio']; ?></td>
        <td><?php echo $row_consulta_re_Table['medidas']; ?></td>
        <td><?php echo $row_consulta_re_Table['narracion']; ?></td>
        <td><?php echo $row_consulta_re_Table['seguimiento']; ?></td>
        <td><?php echo $row_consulta_re_Table['estatus']; ?></td>
        <td><?php echo $row_consulta_re_Table['supervisor']; ?></td>
      </tr>
      <?php } while ($row_consulta_re_Table = mysql_fetch_assoc($consulta_re_Table)); ?>
    </table>
  </div>
</div>
  <div id="div_Secciones" style="display: none;">
    <p align="center" class="Estilo4"><strong>SECCIONES</strong></p>	
	<div class="CSSTableGenerator" >
    <table border="1" align="center">
      <tr>
        <td><div align="center">FOLIO SECCI&Oacute;N </div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS </div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">JEFE INMEDIATO </div></td>
        <td><div align="center">OBJETIVO</div></td>
        <td><div align="center">OBSERVACIONES</div></td>
        <td><div align="center">ESTATUS</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
        <td><a href="../detalle_secciones_sup.php?recordID=<?php echo $row_consulta_secciones_Table['id']; ?>">S<?php echo $row_consulta_secciones_Table['id']; ?></a></td>
        <td><?php echo $row_consulta_secciones_Table['semana']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['fecha']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['region']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['select2']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['organizacion']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['jefe_inmediato']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['objetivo']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['observaciones']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['estatus']; ?></td>
        <td><?php echo $row_consulta_secciones_Table['supervisores']; ?></td>
      </tr>
      <?php } while ($row_consulta_secciones_Table = mysql_fetch_assoc($consulta_secciones_Table)); ?>
    </table>
	  </div>
</div>
<div class="CSSTableGenerator" ></div>
  <div id="div_SegExtendida" style="display: none;">
    <p align="center" class="Estilo4"><strong>SEGURIDAD EXTENDIDA</strong></p>
    <div class="CSSTableGenerator" >
	<table border="1" align="center">
      <tr>
        <td><div align="center">FOLIO SE </div></td>
        <td><div align="center">SEMANA</div></td>
        <td><div align="center">FECHA</div></td>
        <td><div align="center">REGI&Oacute;N</div></td>
        <td><div align="center">CENTRO DE VENTAS </div></td>
        <td><div align="center">ENTIDAD</div></td>
        <td><div align="center">NOMBRE</div></td>
        <td><div align="center">ORGANIZACI&Oacute;N</div></td>
        <td><div align="center">AREA</div></td>
        <td><div align="center">PUESTO</div></td>
        <td><div align="center">SEXO</div></td>
        <td><div align="center">COLONIA</div></td>
        <td><div align="center">MUNICIPIO</div></td>
        <td><div align="center">PELIGROSIDAD</div></td>
        <td><div align="center">SALIDA DE CASA </div></td>
        <td><div align="center">SALIDA DE CEVE </div></td>
        <td><div align="center">TRASLADO CASA- CEVE </div></td>
        <td><div align="center">TRASLADO CEVE-CASA </div></td>
        <td><div align="center">TRANSPORTE</div></td>
        <td><div align="center">ACCIDENTE</div></td>
        <td><div align="center">TIPO DE ACCIDENTE </div></td>
        <td><div align="center">RECOMENDACI&Oacute;N</div></td>
        <td><div align="center">SUPERVISOR</div></td>
      </tr>
      <?php do { ?>
      <tr>
	  <td><a href="../detalle_extendida_sup.php?recordID=<?php echo $row_ExtendidaTable['id']; ?>">EX<?php echo $row_ExtendidaTable['id']; ?></a></td>
        <td><?php echo $row_ExtendidaTable['semana']; ?></td>
        <td><?php echo $row_ExtendidaTable['fecha']; ?></td>
        <td><?php echo $row_ExtendidaTable['region']; ?></td>
        <td><?php echo $row_ExtendidaTable['select2']; ?></td>
        <td><?php echo $row_ExtendidaTable['select3']; ?></td>
        <td><?php echo $row_ExtendidaTable['nombre']; ?></td>
        <td><?php echo $row_ExtendidaTable['organizacion']; ?></td>
        <td><?php echo $row_ExtendidaTable['area']; ?></td>
        <td><?php echo $row_ExtendidaTable['puesto']; ?></td>
        <td><?php echo $row_ExtendidaTable['sexo']; ?></td>
        <td><?php echo $row_ExtendidaTable['colonia']; ?></td>
        <td><?php echo $row_ExtendidaTable['municipio']; ?></td>
        <td><?php echo $row_ExtendidaTable['peligro']; ?></td>
        <td><?php echo $row_ExtendidaTable['s_casa']; ?></td>
        <td><?php echo $row_ExtendidaTable['s_ceve']; ?></td>
        <td><?php echo $row_ExtendidaTable['t_cace']; ?></td>
        <td><?php echo $row_ExtendidaTable['t_ceca']; ?></td>
        <td><?php echo $row_ExtendidaTable['transporte']; ?></td>
        <td><?php echo $row_ExtendidaTable['accidente']; ?></td>
        <td><?php echo $row_ExtendidaTable['t_accidente']; ?></td>
        <td><?php echo $row_ExtendidaTable['recomendacion']; ?></td>
        <td><?php echo $row_ExtendidaTable['supervisor']; ?></td>
      </tr>
      <?php } while ($row_ExtendidaTable = mysql_fetch_assoc($Recordset_ExtendidaTable)); ?>
    </table>
  </div>
</div>
  
</p>
<div id="divCarpeta2" style="display: none;">
  <p align="center" class="Estilo4"><strong>CARPETA ELECTR&Oacute;NICA </strong></p>
      	<div class="CSSTableGenerator" >
  <table border="1" align="center">
    <tr>
      <td><div align="center">FOLIO CE </div></td>
      <td><div align="center">SEMANA</div></td>
      <td><div align="center">FECHA</div></td>
      <td><div align="center">REGI&Oacute;N</div></td>
      <td><div align="center">CENTRO DE VENTAS </div></td>
      <td><div align="center">ENTIDAD</div></td>
      <td><div align="center">ORGANIZACI&Oacute;N</div></td>
      <td><div align="center">FEHA DE REGISTRO</div></td>
      <td><div align="center">NUMERO DE COLABORADOR</div></td>
      <td><div align="center">PUESTO</div></td>
      <td><div align="center">ACTIVIDAD EVALUADA</div></td>
      <td><div align="center">RESULTADO</div></td>
      <td><div align="center">COMENTARIOS</div></td>
      <td><div align="center">SUPERVISOR</div></td>
    </tr>
    <?php do { ?>
    <tr>
	<td><a href="../detalle_carpetaE_sup.php?recordID=<?php echo $row_CETable['id']; ?>">CE<?php echo $row_CETable['id']; ?></a></td>
      <td><?php echo $row_CETable['semana']; ?></td>
      <td><?php echo $row_CETable['fecha']; ?></td>
      <td><?php echo $row_CETable['region']; ?></td>
      <td><?php echo $row_CETable['select2']; ?></td>
      <td><?php echo $row_CETable['select3']; ?></td>
      <td><?php echo $row_CETable['marca']; ?></td>
      <td><?php echo $row_CETable['fechaAut']; ?></td>
      <td><?php echo $row_CETable['num_colaborador']; ?></td>
      <td><?php echo $row_CETable['puesto']; ?></td>
      <td><?php echo $row_CETable['act_evaluada']; ?></td>
      <td><?php echo $row_CETable['resultado']; ?></td>
      <td><?php echo $row_CETable['comentarios']; ?></td>
      <td><?php echo $row_CETable['nombre']; ?></td>
      </tr>
    <?php } while ($row_CETable = mysql_fetch_assoc($Recordset_CETable)); ?>
  </table>
</div>
</div>
<p>&nbsp;</p>
</body>
</html>

<?php
mysql_free_result($consulta_usuarioTable);
mysql_free_result($Recordset_Asaltos);
mysql_free_result($consulta_bajas_Table);
mysql_free_result($consulta_robos_Table);
mysql_free_result($consulta_detenidos_Table);
mysql_free_result($Recordset_ExtendidaTable);
mysql_free_result($Recordset_CETable);
?>