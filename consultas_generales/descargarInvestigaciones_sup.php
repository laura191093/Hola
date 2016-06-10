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

$colname_Recordset1 = "-1";
if (isset($_POST['supervisores'])) {
  $colname_Recordset1 = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}


//Reporte para excel
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_Investigaciones =  sprintf("SELECT *, CONCAT(con_marca, '/', con_region, '/', con_asunto, '/', con_ceve , '', con_fecha, '', con_sup)  As codigo FROM investigaciones WHERE responsable_caso = '%s'", $colname_Recordset1);
	
		
		$consulta_investigaciones = mysql_query ($query_Investigaciones,$conexion_usuarios)or die (mysql_error());
		$row_investigaciones = mysql_num_rows ($consulta_investigaciones);

		if($row_investigaciones > 0){
						
		date_default_timezone_set('America/Mexico_City');

		//El archivo solo se va a mostrar si se accede desde un navegador web(HTTP).
		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once '../Classes/PHPExcel.php';

		// Se crea el objeto PHPExcel
		$objPHPExcel = new PHPExcel();

		$objPHPExcel-> createSheet (0);
		$objPHPExcel-> getSheet (0) -> setTitle ( 'Investigaciones' );
		

		// Se asignan las propiedades del libro											 
		$objPHPExcel->getProperties()->setCreator("Centro de Mando")// Nombre del autor
		->setLastModifiedBy("Centro de Mando") //Ultimo usuario que lo modificó
		->setTitle("Reporte General de Seguridad") // Titulo
		->setSubject("Consulta Web Seguridad")  //Asunto
		->setDescription("Documento Generado por Centro de Mando Seguridad Bimbo")  //Descripción
		->setKeywords("Reporte General CM") //Etiquetas
		->setCategory("Reporte General"); //Categorias
		
		$titulosColumnas = array('FOLIO', 'SEMANA', 'REGION', 'CENTRO DE VENTAS', 'ENTIDAD','MARCA', 'RESPONSABLE CASO','CODIGO INVESTIGACION', 'ESTATUS','FECHA INICIO', 'FECHA DE CIERRE', 'ASUNTO','TIPO DE INVESTIGACION', 'DESCRIPCION DEL ASUNTO', 'SEGUIMIENTO','APRENDIO','MEJORA IMPLEMENTAR');
		
		// Se combinan las celdas A1 hasta D1, para colocar ahí el titulo del reporte
						
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(0)
					//->setCellValue('A1',$tituloReporte) // Titulo del reporte
        		    ->setCellValue('A1',  $titulosColumnas[0]) //Titulo de las columnas
		            ->setCellValue('B1',  $titulosColumnas[1])
        		    ->setCellValue('C1',  $titulosColumnas[2])
            		->setCellValue('D1',  $titulosColumnas[3])
					->setCellValue('E1',  $titulosColumnas[4])
					->setCellValue('F1',  $titulosColumnas[5])
					->setCellValue('G1',  $titulosColumnas[6])
					->setCellValue('H1',  $titulosColumnas[7])
					->setCellValue('H1',  $titulosColumnas[7])
					->setCellValue('I1',  $titulosColumnas[8])
					->setCellValue('J1',  $titulosColumnas[9])
					->setCellValue('K1',  $titulosColumnas[10])
					->setCellValue('L1',  $titulosColumnas[11])
					->setCellValue('M1',  $titulosColumnas[12])
					->setCellValue('N1',  $titulosColumnas[13])
					->setCellValue('O1',  $titulosColumnas[14])
					->setCellValue('P1',  $titulosColumnas[15])
					->setCellValue('Q1',  $titulosColumnas[16]);

		//Se agregan los datos 
		$i = 2; //Numero de fila donde se va a comenzar a rellenar
		while ($fila =mysql_fetch_array ($consulta_investigaciones)){
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$i,  $fila['id'])
		            ->setCellValue('B'.$i,  $fila['semana'])
        		    ->setCellValue('C'.$i,  $fila['region'])
					->setCellValue('D'.$i, utf8_encode($fila['select2']))					
					->setCellValue('E'.$i, utf8_encode($fila['select3']))
				    ->setCellValue('F'.$i, utf8_encode($fila['marca']))
					->setCellValue('G'.$i, utf8_encode($fila['responsable_caso']))
					->setCellValue('H'.$i, $fila['codigo'])				
					->setCellValue('I'.$i, $fila['estatus'])
					->setCellValue('J'.$i, utf8_encode($fila['fechaInicio']))
					->setCellValue('K'.$i, utf8_encode($fila['fecha_cierre']))
					->setCellValue('L'.$i, utf8_encode($fila['asunto']))	
					->setCellValue('M'.$i, utf8_encode($fila['tipo_investigacion']))
					->setCellValue('N'.$i, utf8_encode($fila['descripcion_asunto']))					
					->setCellValue('O'.$i, utf8_encode($fila['seguimiento']))
					->setCellValue('P'.$i, utf8_encode($fila['aprendio']))
					->setCellValue('Q'.$i, utf8_encode($fila['mejora_implementar']));

					$i++;
		}
	    // Ajustar texto a las celdas
		for($i = 'A'; $i <= 'Q'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($i)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Investigaciones');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:Q1"); 
		
		$objPHPExcel->removeSheetByIndex(1); 
		
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('E4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(5,2);
		
	
		
		//ESTRUCTURA EXCEL 
		$f=date("d-m-y H.i");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$fecha01="Investigaciones (Descargado) ".$f.".xlsx";
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="Shortcut Icon" href="../imagenes/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="../style/style_principal.css">
<link rel="stylesheet" type="text/css" href="../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../style/style_campos.css">

<link rel="stylesheet" type="text/css" href="../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../style/menu/css/zonas.css">


<title>Investigaciones</title>

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
						<h1>DESCARGAR INVESTIGACIONES</h1>
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


<form id="form2" name="form2" class= "reporte" method="post" action="descargarInvestigaciones_sup.php">
  <p><strong>-EXPORTAR INVESTIGACIONES </strong>
    <select name="supervisores" id="supervisores">

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
    <input type="submit" name="Descargar" id="Descargar" class="button themed" value="DESCARGAR" dir="../consulta_asaltos_sup.php" />
	

    </label>
  </p>
</form>

</body>
</html>