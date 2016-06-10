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
$query_consulta_region = "SELECT * FROM supervisores";
$consulta_region = mysql_query($query_consulta_region, $conexion_usuarios) or die(mysql_error());
$row_consulta_region = mysql_fetch_assoc($consulta_region);
$totalRows_consulta_region = mysql_num_rows($consulta_region);

$colname_consulta_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_consulta_usuario = (get_magic_quotes_gpc()) ? $_SESSION['MM_Username'] : addslashes($_SESSION['MM_Username']);
}

mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
$query_consulta_usuario = sprintf("SELECT Nombre FROM supervisores WHERE username = '%s'", $colname_consulta_usuario);
$consulta_usuario = mysql_query($query_consulta_usuario, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuario = mysql_fetch_assoc($consulta_usuario);
$totalRows_consulta_usuario = mysql_num_rows($consulta_usuario);

$colname_Operativos = "-1";
if (isset($_POST['supervisores'])) {
  $colname_Operativos = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}

//Reporte para excel
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_Operativos = sprintf("SELECT o.id, o.semana, o.fecha, o.periodoFecha1, o.periodoFecha2,
r.opcion as region, c.opcion as select2, e.opcion as select3, o.tipo_operativo, o.alto_impacto,
o.faltantes_equipo, o.sobrantes_equipo, o.faltantes_producto, o.sobrantes_producto,
o.afectacion, o.recuperacion, o.antecedentes, o.objetivo, o.resultados, o.supervisor,
o.integrante1, o.integrante2, o.integrante3, o.integrante4, o.integrante5, o.integrante6,
o.integrante7, o.integrante8, o.integrante9, o.integrante10,
o.archivo, o.fecha_captura, o.organizacion FROM seguridad.operativos o
LEFT JOIN region r ON r.id_region = o.region
LEFT JOIN select_2 c ON c.id = o.select2
LEFT JOIN select_3 e ON e.id = o.select3 WHERE o.supervisor = '%s'",  $colname_Operativos);
		$consulta_Operativos = mysql_query ($query_Operativos,$conexion_usuarios)or die (mysql_error());
		$row_Operativos = mysql_num_rows ($consulta_Operativos);


		if($row_Operativos > 0){
						
		date_default_timezone_set('America/Mexico_City');

		//El archivo solo se va a mostrar si se accede desde un navegador web(HTTP).
		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once '../Classes/PHPExcel.php';

		// Se crea el objeto PHPExcel

		$objPHPExcel = new PHPExcel();

		$objPHPExcel-> createSheet (0);
		$objPHPExcel-> getSheet (0) -> setTitle ( 'Operativos' );
		
		

		// Se asignan las propiedades del libro											 
		$objPHPExcel->getProperties()->setCreator("Centro de Mando")// Nombre del autor
		->setLastModifiedBy("Centro de Mando") //Ultimo usuario que lo modificó
		->setTitle("Reporte General de Seguridad") // Titulo
		->setSubject("Consulta Web Seguridad")  //Asunto
		->setDescription("Documento Generado por Centro de Mando Seguridad Bimbo")  //Descripción
		->setKeywords("Reporte General CM") //Etiquetas
		->setCategory("Reporte General"); //Categorias
		
		$titulosColumnasO = array('FOLIO', 'SEMANA', 'FECHA INFORMO','FECHA INICIO', 'FECHA TERMINO', 'REGION','CENTRO DE VENTAS','ENTIDAD', 'ORGANIZACIÓN','TIPO OPERATIVO','ALTO IMPACTO','FALTATES EQUIPO', 'SOBRANTES EQUIPO','FALTANTE PRODUCTO', 'SOBRANTES PRODUCTO', 'AFECTACION', 'RECUPERACION', 'ANTECEDENTES', 'OBJETIVO','RESULTADOS','SUPERVISOR LIDER', 'INTEGRANTE 1','INTEGRANTE 2','INTEGRANTE 3','INTEGRANTE 4','INTEGRANTE 5','INTEGRANTE 6','INTEGRANTE 7','INTEGRANTE 8','INTEGRANTE 9','INTEGRANTE 10','ARCHIVO/INFORME','FECHA DE CAPTURA');

		// Se combinan las celdas A1 hasta D1, para colocar ahí el titulo del reporte
						
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(0)
			
        		    ->setCellValue('A1',  $titulosColumnasO[0]) 
		            ->setCellValue('B1',  $titulosColumnasO[1])
        		    ->setCellValue('C1',  $titulosColumnasO[2])
            		->setCellValue('D1',  $titulosColumnasO[3])
					->setCellValue('E1',  $titulosColumnasO[4])
					->setCellValue('F1',  $titulosColumnasO[5])
					->setCellValue('G1',  $titulosColumnasO[6])
					->setCellValue('H1',  $titulosColumnasO[7])
					->setCellValue('I1',  $titulosColumnasO[8])
					->setCellValue('J1',  $titulosColumnasO[9])
					->setCellValue('K1',  $titulosColumnasO[10])
					->setCellValue('L1',  $titulosColumnasO[11])
					->setCellValue('M1',  $titulosColumnasO[12])
					->setCellValue('N1',  $titulosColumnasO[13])
					->setCellValue('O1',  $titulosColumnasO[14])
					->setCellValue('P1',  $titulosColumnasO[15])
					->setCellValue('Q1',  $titulosColumnasO[16])
					->setCellValue('R1',  $titulosColumnasO[17])
					->setCellValue('S1',  $titulosColumnasO[18])
					->setCellValue('T1',  $titulosColumnasO[19])
					->setCellValue('U1',  $titulosColumnasO[20])
					->setCellValue('V1',  $titulosColumnasO[21])
					->setCellValue('W1',  $titulosColumnasO[22])
					->setCellValue('X1',  $titulosColumnasO[23])
					->setCellValue('Y1',  $titulosColumnasO[24])
					->setCellValue('Z1',  $titulosColumnasO[25])
					->setCellValue('AA1',  $titulosColumnasO[26])
					->setCellValue('AB1',  $titulosColumnasO[27])
					->setCellValue('AC1',  $titulosColumnasO[28])
					->setCellValue('AD1',  $titulosColumnasO[29])
					->setCellValue('AE1',  $titulosColumnasO[30])
					->setCellValue('AF1',  $titulosColumnasO[31])
					->setCellValue('AG1',  $titulosColumnasO[32]);

		//Se agregan los datos 
		$io = 2; //Numero de fila donde se va a comenzar a rellenar
		while ($fila =mysql_fetch_array ($consulta_Operativos)){
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$io,  $fila['id'])
		            ->setCellValue('B'.$io,  $fila['semana'])
        		    ->setCellValue('C'.$io,  $fila['fecha'])
					->setCellValue('D'.$io, utf8_encode($fila['periodoFecha1']))					
					->setCellValue('E'.$io, utf8_encode($fila['periodoFecha2']))
				    ->setCellValue('F'.$io, utf8_encode($fila['region']))
					->setCellValue('G'.$io, utf8_encode($fila['select2']))
					->setCellValue('H'.$io, utf8_encode($fila['select3']))			
					->setCellValue('I'.$io, utf8_encode($fila['organizacion']))
					->setCellValue('J'.$io, utf8_encode($fila['tipo_operativo']))
					->setCellValue('K'.$io, utf8_encode($fila['alto_impacto']))
					->setCellValue('L'.$io, utf8_encode($fila['faltantes_equipo']))	
					->setCellValue('M'.$io, utf8_encode($fila['sobrantes_equipo']))
					->setCellValue('N'.$io, utf8_encode($fila['faltantes_producto']))
					->setCellValue('O'.$io, utf8_encode($fila['sobrantes_producto']))
					->setCellValue('P'.$io, utf8_encode($fila['afectacion']))
					->setCellValue('Q'.$io, utf8_encode($fila['recuperacion']))
					->setCellValue('R'.$io, utf8_encode($fila['antecedentes']))
					->setCellValue('S'.$io, utf8_encode($fila['objetivo']))
					->setCellValue('T'.$io, utf8_encode($fila['resultados']))
					->setCellValue('U'.$io, utf8_encode($fila['supervisor']))
					->setCellValue('V'.$io, utf8_encode($fila['integrante1']))
					->setCellValue('W'.$io, utf8_encode($fila['integrante2']))
					->setCellValue('X'.$io, utf8_encode($fila['integrante3']))
					->setCellValue('Y'.$io, utf8_encode($fila['integrante4']))
					->setCellValue('Z'.$io, utf8_encode($fila['integrante5']))
					->setCellValue('AA'.$io, utf8_encode($fila['integrante6']))
					->setCellValue('AB'.$io, utf8_encode($fila['integrante7']))
					->setCellValue('AC'.$io, utf8_encode($fila['integrante8']))
					->setCellValue('AD'.$io, utf8_encode($fila['integrante9']))
					->setCellValue('AE'.$io, utf8_encode($fila['integrante10']))
					->setCellValue('AF'.$io, utf8_encode($fila['archivo']))
				    ->setCellValue('AG'.$io, $fila['fecha_captura']);;

					$io++;
		}
	    // Ajustar texto a las celdas
		for($io = 'A'; $io <= 'Z'; $io++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($io)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Operativos');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AG1"); 
		
		$objPHPExcel->removeSheetByIndex(1); 
		
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('E4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(5,2);

		
	
		
		//ESTRUCTURA EXCEL 
		$f=date("d-m-y H.i");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$fecha01="Operativos (Descargado) ".$f.".xlsx";
		header('Content-Disposition: attachment;filename='.$fecha01.'');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
		
	}
	else{
		//print_r('NO HAY RESULTADOS PARA MOSTRAR');
	}	
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" href="../style/style_principal.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../style/style_campos.css">

<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">

<title>Operativos</title>

<style type="text/css">
.botonExcel{cursor:pointer;}
</style>

<style type="text/css">
<!--
.Estilo2 {font-size: xx-large}
.Estilo4 {font-size: large}
.Estilo5 {color: #FFFFFF}
-->
</style>

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
						<h1>OPERATIVOS</h1>
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
    </br>
   BIENVENIDO: <?php echo $row_consulta_usuario['Nombre']; ?>
	<img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="../principal_sup.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>
</br>

<form id="form2" name="form2" method="post" action="descargarOperativos_sup.php">
  <label></label>
  <p align="left"><strong>-EXPORTAR OPERATIVOS </strong> 
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
    <img src="../imagenes/export_to_excel.gif" alt="exportar_Excel" width="28" height="17"  />
    <input type="submit" name="Descargar" class="button themed" id="Descargar" value="DESCARGAR" />
	

    </label>
  </p>
</form>
</body>
</html>