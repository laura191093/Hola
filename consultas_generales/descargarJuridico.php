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

$colname_Penal = "-1";
if (isset($_POST['supervisores'])) {
  $colname_Penal = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}

//Reporte para excel
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_penal = sprintf("SELECT p.id, p.semana, fecha,r.opcion as region, c.opcion as select2, e.opcion as select3,p.organizacion, p.delito, p.tipo,p.etapa_procesal, p.actividad_reportar,p.tiempo_invertido, p.narracion, p.seguimiento,p.estatus, p.supervisor FROM seguridad.proceso_penal p LEFT JOIN region r ON r.id_region = p.region LEFT JOIN select_2 c ON c.id = p.select2 LEFT JOIN select_3 e ON e.id = p.select3 WHERE supervisor = '%s'",  $colname_Penal);
		$consulta_penal = mysql_query ($query_penal,$conexion_usuarios)or die (mysql_error());
		$row_penal = mysql_num_rows ($consulta_penal);


		if($row_penal > 0){
						
		date_default_timezone_set('America/Mexico_City');

		//El archivo solo se va a mostrar si se accede desde un navegador web(HTTP).
		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once '../Classes/PHPExcel.php';

		// Se crea el objeto PHPExcel

		$objPHPExcel = new PHPExcel();

		$objPHPExcel-> createSheet (0);
		$objPHPExcel-> getSheet (0) -> setTitle ( 'Proceso Penal' );
		
		

		// Se asignan las propiedades del libro											 
		$objPHPExcel->getProperties()->setCreator("Centro de Mando")// Nombre del autor
		->setLastModifiedBy("Centro de Mando") //Ultimo usuario que lo modificó
		->setTitle("Etapa Procesal") // Titulo
		->setSubject("Consulta Web Seguridad")  //Asunto
		->setDescription("Documento Generado por Centro de Mando Seguridad Bimbo")  //Descripción
		->setKeywords("Etapa Procesal") //Etiquetas
		->setCategory("Etapa Procesal"); //Categorias
		
		$titulosColumnasO = array('FOLIO', 'SEMANA', 'FECHA', 'REGION','INSTALACION', 'ENTIDAD', 'ORGANIZACION','DELITO', 'TIPO','ETAPA PROCESAL','ACTIVIDAD A REPORTAR', 'TIEMPO INVERTIDO','NARRACION', 'SEGUIMIENTO', 'ESTATUS','SUPERVISOR');

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
					->setCellValue('P1',  $titulosColumnasO[15]);

		//Se agregan los datos 
		$io = 2; //Numero de fila donde se va a comenzar a rellenar
		while ($fila =mysql_fetch_array ($consulta_penal)){
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$io,  $fila['id'])
		            ->setCellValue('B'.$io,  $fila['semana'])
        		    ->setCellValue('C'.$io,  $fila['fecha'])
				    ->setCellValue('D'.$io, utf8_encode($fila['region']))
					->setCellValue('E'.$io, utf8_encode($fila['select2']))
					->setCellValue('F'.$io, utf8_encode($fila['select3']))			
					->setCellValue('G'.$io, utf8_encode($fila['organizacion']))
					->setCellValue('H'.$io, utf8_encode($fila['delito']))
					->setCellValue('I'.$io, utf8_encode($fila['tipo']))
					->setCellValue('J'.$io, utf8_encode($fila['etapa_procesal']))	
					->setCellValue('K'.$io, utf8_encode($fila['actividad_reportar']))
					->setCellValue('L'.$io, utf8_encode($fila['tiempo_invertido']))
					->setCellValue('M'.$io, utf8_encode($fila['narracion']))
					->setCellValue('N'.$io, utf8_encode($fila['seguimiento']))
					->setCellValue('O'.$io, utf8_encode($fila['estatus']))
					->setCellValue('P'.$io, utf8_encode($fila['supervisor']));

					$io++;
		}
	    // Ajustar texto a las celdas
		for($io = 'A'; $io <= 'P'; $io++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($io)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Proceso Penal');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:P1"); 
		
		$objPHPExcel->removeSheetByIndex(1); 
		
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('E4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(5,2);

		
	
		
		//ESTRUCTURA EXCEL 
		$f=date("d-m-y H.i");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$fecha01="Proceso Penal (Descargado) ".$f.".xlsx";
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