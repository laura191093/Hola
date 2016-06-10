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
$query_consulta_usuario = sprintf("SELECT Nombre FROM supervisores", $colname_consulta_usuario);
$consulta_usuario = mysql_query($query_consulta_usuario, $conexion_usuarios) or die(mysql_error());
$row_consulta_usuario = mysql_fetch_assoc($consulta_usuario);
$totalRows_consulta_usuario = mysql_num_rows($consulta_usuario);

$colname_Recordset1 = "-1";
if (isset($_POST['supervisores'])) {
  $colname_Recordset1 = (get_magic_quotes_gpc()) ? $_POST['supervisores'] : addslashes($_POST['supervisores']);
}


//Reporte para excel
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
	LEFT JOIN senas_particulares s ON s.id_fk = a.id LEFT JOIN datos_vehiculo dv ON dv .id_fkv = a.id WHERE supervisor = '%s'", $colname_Recordset1);
		$Recordset_Asaltos = mysql_query ($query_Asaltos,$conexion_usuarios)or die (mysql_error());
		$row_Asaltos = mysql_num_rows ($Recordset_Asaltos);

		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);	
		$query_consulta_bajas_t= sprintf("SELECT b.id, b.semana,  b.organizacion, r.opcion as region, c.opcion as select2,
		e.opcion as select3, b.area,
		b.fecha, b.ilicito, b.num_creditos, b.nombre_del_vendedor,
		b.cliente, b.puesto, b.jefe_inmediato, b.ruta, b.canal,
		b.afectacion_efectivo, b.afectacion02, b.afectacion_producto, b.recuperacion,
		b.supervisores, b.narraciones, b.seguimiento, b.estatus, b.fin_reporte,
		b.atendio, b.motivo_borrado, b.num_empleado FROM bajas b
		LEFT JOIN region r ON r.id_region = b.region
		LEFT JOIN select_2 c ON c.id = b.select2
		LEFT JOIN select_3 e ON e.id = b.select3 WHERE b.supervisores = '%s'", $colname_Recordset1);
		$consulta_bajas_t = mysql_query ($query_consulta_bajas_t,$conexion_usuarios)or die (mysql_error());
		$row_consulta_bajas_t= mysql_num_rows ($consulta_bajas_t);

			
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
				$query_consulta_detenidos_t = sprintf("SELECT d.id, d.semana, r.opcion as region, c.opcion as select2, e.opcion as select3, d.organizacion,
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
		LEFT JOIN select_3 e ON e.id = d.select3 WHERE d.supervisor= '%s'", $colname_Recordset1);
		$consulta_detenidos_t = mysql_query ($query_consulta_detenidos_t,$conexion_usuarios)or die (mysql_error());
		$row_consulta_detenidos_t= mysql_num_rows ($consulta_detenidos_t);
		
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_consulta_robos_T = sprintf("SELECT r.id, r.semana, r.organizacion,  r.area, r.fecha, re.opcion as region, c.opcion as select2,
e.opcion as select3, r.nombre_del_conductor, r.puesto, r.horario, r.calle, r.cliente, r.colonia,
r.averiguacion_previa, r.municipio, r.jefe_inmediato, r.marca, r.placa, r.motor, r.ruta,
r.año, r.canal, r.precio, r.cumplio_medidas, r.medidas, r.economico, r.supervisor_de_seguridad,
r.recuperado, r.cancelacion, r.narracion, r.seguimiento, r.estatus, r.fin_reporte, r.num_empleado,
r.atendio, r.motivo_borrado, r.fisico, r.comentarios_fisico, r.nombre_actualizo
FROM robovhs r
LEFT JOIN region re ON re.id_region = r.region
LEFT JOIN select_2 c ON c.id = r.select2
LEFT JOIN select_3 e ON e.id = r.select3 WHERE r.supervisor_de_seguridad = '%s'", $colname_Recordset1);
		$consulta_robos_T = mysql_query ($query_consulta_robos_T,$conexion_usuarios)or die (mysql_error());
		$row_consulta_robos_T = mysql_num_rows ($consulta_robos_T);
		
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_consulta_re_t = sprintf("SELECT r.id, r.semana, r.organizacion, r.area, re.opcion as region, c.opcion as select2,
e.opcion as select3,r.fecha, r.reporte, r.nombre_del_vendedor, r.horario, r.calle,
 r.colonia, r.cliente, r.averiguacion, r.delegacion, r.jefe, r.canal,
 r.ruta, r.bandejas, r.tinas, r.dollys, r.maquina_autovend, r.afectacione,
 r.valor_recuperacion, r.piezas_producto, r.cumplio, r.medidas, r.supervisor,
 r.narracion, r.seguimiento, r.estatus, r.fin_reporte, r.recuperacion_producto,
 r.recuperacion_equipo, r.atendio, r.nombre_actualizo, r.fisico, r.comentarios_fisico,
 r.turno FROM robo_equipo r
LEFT JOIN region re ON re.id_region = r.region
LEFT JOIN select_2 c ON c.id = r.select2
LEFT JOIN select_3 e ON e.id = r.select3 WHERE r.supervisor= '%s'", $colname_Recordset1);
		$consulta_re_t = mysql_query ($query_consulta_re_t,$conexion_usuarios)or die (mysql_error());	
		$row_consulta_re_t = mysql_num_rows ($consulta_re_t);
		
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_consulta_secciones_t = sprintf( "SELECT s.id, r.opcion as region, c.opcion as select2, s.semana, s.organizacion, s.jefe_inmediato, s.fecha,
s.s_reportar,s.objetivo, s.supervisores, s.observaciones, s.estatus,
s.fin_seccion, s.atendio
FROM secciones s
LEFT JOIN region r ON r.id_region = s.region
LEFT JOIN select_2 c ON c.id = s.select2 WHERE s.supervisores= '%s'", $colname_Recordset1);
		$consulta_secciones_t = mysql_query ($query_consulta_secciones_t,$conexion_usuarios)or die (mysql_error());
		$row_consulta_secciones_t = mysql_num_rows ($consulta_secciones_t);
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_Extendida = sprintf("SELECT s.id, s.semana, s.organizacion, r.opcion as region, c.opcion as select2, e.opcion as select3,
	s.area, s.puesto, s.fecha, s.nombre, s.sexo, s.colonia, s.peligro, s.municipio,
	s.s_casa, s.s_ceve, s.t_cace, s.t_ceca, s.transporte, s.accidente,
	s.t_accidente, s.supervisor, s.recomendacion, s.fin_reporte FROM seguridad.seg_extendida s
	LEFT JOIN region r ON r.id_region = s.region
	LEFT JOIN select_2 c ON c.id = s.select2
	LEFT JOIN select_3 e ON e.id = s.select3 WHERE s.supervisor= '%s'", $colname_Recordset1);
		$Recordset_Extendida  = mysql_query ($query_Extendida,$conexion_usuarios)or die (mysql_error());
		$row_Extendida= mysql_num_rows ($Recordset_Extendida);

		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_CE = sprintf( "SELECT ce.id, ce.fecha, ce.semana, r.opcion as region, c.opcion as select2, e.opcion as select3, ce.marca, ce.nombre, ce.num_colaborador, ce.puesto,
ce.act_evaluada, ce.resultado, ce.comentarios, ce.fecha_captura,
ce.fechaAut, ce.afectacion, ce.recuperacion FROM carpeta_electronica ce
LEFT JOIN region r ON r.id_region = ce.region
LEFT JOIN select_2 c ON c.id = ce.select2
LEFT JOIN select_3 e ON e.id = ce.select3 WHERE ce.nombre= '%s'", $colname_Recordset1);
		$Recordset_CE = mysql_query ($query_CE,$conexion_usuarios)or die (mysql_error());
		$row_CE= mysql_num_rows ($Recordset_CE);
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_Investigaciones =  sprintf("SELECT *, CONCAT(con_marca, '/', con_region, '/', con_asunto, '/', con_ceve , '', con_fecha, '', con_sup)  As codigo FROM investigaciones WHERE responsable_caso = '%s'", $colname_Recordset1);
		$consulta_investigaciones = mysql_query ($query_Investigaciones,$conexion_usuarios)or die (mysql_error());
		$row_investigaciones = mysql_num_rows ($consulta_investigaciones);
		
		
		mysql_select_db($database_conexion_usuarios, $conexion_usuarios);
		$query_Tecnicos= sprintf("SELECT * FROM tecnicos_seguridad  WHERE supervisor = '%s'",  $colname_Recordset1);
		$consulta_tecnicos = mysql_query ($query_Tecnicos,$conexion_usuarios)or die (mysql_error());
		$row_tecnicos = mysql_num_rows ($consulta_tecnicos);
		
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
LEFT JOIN select_3 e ON e.id = o.select3 WHERE o.supervisor = '%s'",  $colname_Recordset1);
		$consulta_Operativos = mysql_query ($query_Operativos,$conexion_usuarios)or die (mysql_error());
		$row_Operativos = mysql_num_rows ($consulta_Operativos);
		

		if($row_Asaltos > 0 || $row_consulta_bajas_t> 0 || $row_consulta_detenidos_t> 0 || $row_consulta_robos_T  > 0 ||  $row_consulta_re_t > 0 || $row_consulta_secciones_t > 0 || $row_Extendida> 0 || $row_CE> 0 || $row_investigaciones>0 || $row_tecnicos>0 || $row_Operativos > 0)
		{
						
		date_default_timezone_set('America/Mexico_City');

		//El archivo solo se va a mostrar si se accede desde un navegador web(HTTP).
		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once '../Classes/PHPExcel.php';

		// Se crea el objeto PHPExcel
		$objPHPExcel = new PHPExcel();

		$objPHPExcel-> createSheet (0);
		$objPHPExcel-> getSheet (0) -> setTitle ( 'Asaltos' );
		
		$objPHPExcel-> createSheet (1);
		$objPHPExcel-> getSheet (1) -> setTitle ( 'Detenidos' );
		
		$objPHPExcel-> createSheet (2);
		$objPHPExcel-> getSheet (2) -> setTitle ( 'Robo VHS' );
		
		$objPHPExcel-> createSheet (3);
		$objPHPExcel-> getSheet (3) -> setTitle ( 'Bajas' );
		
		$objPHPExcel-> createSheet (4);
		$objPHPExcel-> getSheet (4) -> setTitle ( 'Robo Equipo' );
		
		$objPHPExcel-> createSheet (5);
		$objPHPExcel-> getSheet (5) -> setTitle ( 'Secciones' );
		
		$objPHPExcel-> createSheet (6);
		$objPHPExcel-> getSheet (6) -> setTitle ( 'Seguridad Extendida' );
		
		$objPHPExcel-> createSheet (7);
		$objPHPExcel-> getSheet (7) -> setTitle ( 'Carpeta Electronica' );
		
		$objPHPExcel-> createSheet (8);
		$objPHPExcel-> getSheet (8) -> setTitle ( 'Investigaciones' );
		
		$objPHPExcel-> createSheet (9);
		$objPHPExcel-> getSheet (9) -> setTitle ( 'Actividades Tecnicos' );
		
		$objPHPExcel-> createSheet (10);
		$objPHPExcel-> getSheet (10) -> setTitle ( 'Operativos' );
		

		// Se asignan las propiedades del libro											 
		$objPHPExcel->getProperties()->setCreator("Centro de Mando")// Nombre del autor
		->setLastModifiedBy("Centro de Mando") //Ultimo usuario que lo modificó
		->setTitle("Reporte General de Seguridad") // Titulo
		->setSubject("Consulta Web Seguridad")  //Asunto
		->setDescription("Documento Generado por Centro de Mando Seguridad Bimbo")  //Descripción
		->setKeywords("Reporte General CM") //Etiquetas
		->setCategory("Reporte General"); //Categorias
		
		$titulosColumnas = array('FOLIO', 'SEMANA','FECHA','REGION','CENTRO DE VENTAS', 'ENTIDAD','NOMBRE DEL VENDEDOR 1', 'NUM DE EMPLEADO 1',
		'NOMBRE DEL VENDEDOR 2', 'NUM. DE EMPLEADO 2', 'RUTA','ORGANIZACION','CLIENTE','CALLE','COLONIA','DELEGACION','AREA','HORARIO','AVERIGUACION','JEFE','AFECTACION EN EFECTIVO','RECARGAS ELECTRONICAS','AFECTACION PRODUCTO','AFECTACION APERTURA DE CAJA','RECUPERACION','AFECTACIÓN HAND HELD / IMPRESORA','EQUIPO','CANAL','CUMPLIO','MEDIDAS', 'LESIONADO', 'ESPECIFIQUE LESION', 'SUPERVISOR','NARRACION','SEGUIMIENTO','ESTATUS');
		
		// Se combinan las celdas A1 hasta D1, para colocar ahí el titulo del reporte
		//$objPHPExcel->setActiveSheetIndex(0)
        		    //->mergeCells('A1:E1');
						
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
					->setCellValue('I1',  $titulosColumnas[8])
					->setCellValue('J1',  $titulosColumnas[9])
					->setCellValue('K1',  $titulosColumnas[10])
					->setCellValue('L1',  $titulosColumnas[11])
					->setCellValue('M1',  $titulosColumnas[12])
					->setCellValue('N1',  $titulosColumnas[13])
					->setCellValue('O1',  $titulosColumnas[14])
					->setCellValue('P1',  $titulosColumnas[15])
					->setCellValue('Q1',  $titulosColumnas[16])
					->setCellValue('R1',  $titulosColumnas[17])
					->setCellValue('S1',  $titulosColumnas[18])
					->setCellValue('T1',  $titulosColumnas[19])
					->setCellValue('U1',  $titulosColumnas[20])
					->setCellValue('V1',  $titulosColumnas[21])
					->setCellValue('W1',  $titulosColumnas[22])
					->setCellValue('X1',  $titulosColumnas[23])
					->setCellValue('Y1',  $titulosColumnas[24])
					->setCellValue('Z1',  $titulosColumnas[25])
					->setCellValue('AA1', $titulosColumnas[26])
					->setCellValue('AB1', $titulosColumnas[27])
					->setCellValue('AC1', $titulosColumnas[28])
					->setCellValue('AD1', $titulosColumnas[29])
					->setCellValue('AE1', $titulosColumnas[30])
					
					->setCellValue('AF1', $titulosColumnas[31])
					->setCellValue('AG1', $titulosColumnas[32])
					->setCellValue('AH1', $titulosColumnas[33])
					->setCellValue('AI1', $titulosColumnas[34])
					->setCellValue('AJ1', $titulosColumnas[35]);

		//Se agregan los datos 
		$i = 2; //Numero de fila donde se va a comenzar a rellenar
		while ($fila =mysql_fetch_array ($Recordset_Asaltos)){
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$i,  $fila['id'])
		            ->setCellValue('B'.$i,  $fila['semana'])
					->setCellValue('C'.$i,  $fila['fecha'])
        		    ->setCellValue('D'.$i,  $fila['region'])
					->setCellValue('E'.$i, utf8_encode($fila['select2']))					
					->setCellValue('F'.$i, utf8_encode($fila['select3']))
					->setCellValue('G'.$i, utf8_encode($fila['nombrev']))
					
					->setCellValue('H'.$i, utf8_encode($fila['num_colaborador']))
					->setCellValue('I'.$i, utf8_encode($fila['nombre_vendedor2']))
					->setCellValue('J'.$i, utf8_encode($fila['num_colaborador2']))	
										
					->setCellValue('K'.$i, utf8_encode($fila['ruta']))
					->setCellValue('L'.$i, utf8_encode($fila['organizacion']))
					->setCellValue('M'.$i, utf8_encode($fila['cliente']))
					->setCellValue('N'.$i, utf8_encode($fila['calle']))
					->setCellValue('O'.$i, utf8_encode($fila['colonia']))
					->setCellValue('P'.$i, utf8_encode($fila['delegacion']))
					->setCellValue('Q'.$i, utf8_encode($fila['area']))
					->setCellValue('R'.$i, utf8_encode($fila['horario']))
					->setCellValue('S'.$i, utf8_encode($fila['averiguacion']))
					->setCellValue('T'.$i, utf8_encode($fila['jefe']))
					->setCellValue('U'.$i, utf8_encode($fila['afectacione']))
					->setCellValue('V'.$i, utf8_encode($fila['afectacion_02']))
					->setCellValue('W'.$i, utf8_encode($fila['afectacionp']))
					->setCellValue('X'.$i, utf8_encode($fila['afectacionc']))
					->setCellValue('Y'.$i, utf8_encode($fila['recuperacion']))
					->setCellValue('Z'.$i, utf8_encode($fila['handheld_impresora']))
					->setCellValue('AA'.$i, utf8_encode($fila['tinas_bandejas']))
					->setCellValue('AB'.$i, utf8_encode($fila['canal']))
					->setCellValue('AC'.$i, utf8_encode($fila['cumplio']))
					->setCellValue('AD'.$i, utf8_encode($fila['medidas']))
					
					->setCellValue('AE'.$i, utf8_encode($fila['lesion']))
					->setCellValue('AF'.$i, utf8_encode($fila['especifique_lesion']))
					
					->setCellValue('AG'.$i, utf8_encode($fila['supervisor']))
					->setCellValue('AH'.$i, utf8_encode($fila['narracion']))
					->setCellValue('AI'.$i, utf8_encode($fila['seguimiento']))
					->setCellValue('AJ'.$i, utf8_encode($fila['estatus']));
					$i++;
		}
	    // Ajustar texto a las celdas
		for($i = 'A'; $i <= 'Z'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($i)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Asaltos');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AJ1"); 
		
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('E4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(5,2);
		
		
		//REPORTE BAJAS
		$titulosColumnasB = array('FOLIO', 'SEMANA', 'FECHA', 'ORGANIZACION','REGION','CENTRO DE VENTAS', 'ENTIDAD','NOMBRE DEL VENDEDOR','RUTA','CLIENTE','AREA','ILICITO','NUMERO DE CREDITOS','PUESTO','JEFE INMEDIATO','CANAL','AFECTACION EFECTIVO','RECARGAS ELECTRONICAS','AFECTACION PRODUCTO','RECUPERACION','SUPERVISOR','NARRACION','SEGUIMIENTO','ESTATUS');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(3)
        		    ->setCellValue('A1',  $titulosColumnasB[0])
		            ->setCellValue('B1',  $titulosColumnasB[1])
        		    ->setCellValue('C1',  $titulosColumnasB[2])
            		->setCellValue('D1',  $titulosColumnasB[3])
					->setCellValue('E1',  $titulosColumnasB[4])
					->setCellValue('F1',  $titulosColumnasB[5])
					->setCellValue('G1',  $titulosColumnasB[6])
					->setCellValue('H1',  $titulosColumnasB[7])
					->setCellValue('I1',  $titulosColumnasB[8])
					->setCellValue('J1',  $titulosColumnasB[9])
					->setCellValue('K1',  $titulosColumnasB[10])
					->setCellValue('L1',  $titulosColumnasB[11])
					->setCellValue('M1',  $titulosColumnasB[12])
					->setCellValue('N1',  $titulosColumnasB[13])
					->setCellValue('O1',  $titulosColumnasB[14])
					->setCellValue('P1',  $titulosColumnasB[15])
					->setCellValue('Q1',  $titulosColumnasB[16])
					->setCellValue('R1',  $titulosColumnasB[17])
					->setCellValue('S1',  $titulosColumnasB[18])
					->setCellValue('T1',  $titulosColumnasB[19])
					->setCellValue('U1',  $titulosColumnasB[20])
					->setCellValue('V1',  $titulosColumnasB[21])
					->setCellValue('W1',  $titulosColumnasB[22])
					->setCellValue('X1',  $titulosColumnasB[23]);

		//Se agregan los datos 
		$ib = 2;
		while ($filaB =mysql_fetch_array ($consulta_bajas_t)){
			$objPHPExcel->setActiveSheetIndex(3)
        		    ->setCellValue('A'.$ib,  $filaB['id'])
		            ->setCellValue('B'.$ib,  $filaB['semana'])
        		    ->setCellValue('C'.$ib,  $filaB['fecha'])
            		->setCellValue('D'.$ib, utf8_encode($filaB['organizacion']))
					->setCellValue('E'.$ib, utf8_encode($filaB['region']))
					->setCellValue('F'.$ib, utf8_encode($filaB['select2']))	
					->setCellValue('G'.$ib, utf8_encode($filaB['select3']))
					->setCellValue('H'.$ib, utf8_encode($filaB['nombre_del_vendedor']))
					->setCellValue('I'.$ib, utf8_encode($filaB['ruta']))
					->setCellValue('J'.$ib, utf8_encode($filaB['cliente']))
					->setCellValue('K'.$ib, utf8_encode($filaB['area']))
					->setCellValue('L'.$ib, utf8_encode($filaB['ilicito']))
					->setCellValue('M'.$ib, utf8_encode($filaB['num_creditos']))
					->setCellValue('N'.$ib, utf8_encode($filaB['puesto']))
					->setCellValue('O'.$ib, utf8_encode($filaB['jefe_inmediato']))
					->setCellValue('P'.$ib, utf8_encode($filaB['canal']))
					->setCellValue('Q'.$ib, utf8_encode($filaB['afectacion_efectivo']))
					->setCellValue('R'.$ib, utf8_encode($filaB['afectacion02']))
					->setCellValue('S'.$ib, utf8_encode($filaB['afectacion_producto']))
					->setCellValue('T'.$ib, utf8_encode($filaB['recuperacion']))
					->setCellValue('U'.$ib, utf8_encode($filaB['supervisores']))
					->setCellValue('V'.$ib, utf8_encode($filaB['narraciones']))
					->setCellValue('W'.$ib, utf8_encode($filaB['seguimiento']))
					->setCellValue('X'.$ib, utf8_encode($filaB['estatus']));

					$ib++;
		}
		

		
		
	    // Ajustar texto a las celdas
		for($ib = 'A'; $ib <= 'V'; $ib++){
			$objPHPExcel->setActiveSheetIndex(3)			
				->getColumnDimension($ib)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Bajas');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:X1"); 
		
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(1)->freezePaneByColumnAndRow(7,2);	
		
		
			//REPORTE DETENIDOS
		$titulosColumnasD = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ORGANIZACION','NOMBRE','MONTO AFECTACION','LUGAR DE NACIMIENTO','EDAD','ESTADO CIVIL','PROFESION','ESTADO','CALLE','COLONIA','MUNICIPIO','ESPECIALIDAD DELICTIVA','APODO','CARPETA DE INVESTIGACION','CONSIGNADO A','FECHA DE CONSIGNACION','JUZGADO PENAL','CAUSA PENAL','FECHA SENTENCIA','CONDENA','SEGUIMIENTO','SENTENCIADO','ESTATURA','COMPLEXION','PESO','COLOR DE PIEL','CONTORNO FACIAL','TIPO DE PELO','COLOR DE PELO','FRENTE','CEJAS','OJOS','COLOR DE OJOS','TIPO DE NARIZ','BIGOTE','TIPO DE BOCA','LABIOS','MENTÓN','CICATRIZ','TATUAJES','DEFORMACION FISICA','IMAGEN','RECUPERACION','NARRACION','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(1)
        		    ->setCellValue('A1',  $titulosColumnasD[0])
		            ->setCellValue('B1',  $titulosColumnasD[1])
        		    ->setCellValue('C1',  $titulosColumnasD[2])
            		->setCellValue('D1',  $titulosColumnasD[3])
					->setCellValue('E1',  $titulosColumnasD[4])
					->setCellValue('F1',  $titulosColumnasD[5])
					->setCellValue('G1',  $titulosColumnasD[6])
					->setCellValue('H1',  $titulosColumnasD[7])
					->setCellValue('I1',  $titulosColumnasD[8])
					->setCellValue('J1',  $titulosColumnasD[9])
					->setCellValue('K1',  $titulosColumnasD[10])
					->setCellValue('L1',  $titulosColumnasD[11])
					->setCellValue('M1',  $titulosColumnasD[12])
					->setCellValue('N1',  $titulosColumnasD[13])
					->setCellValue('O1',  $titulosColumnasD[14])
					->setCellValue('P1',  $titulosColumnasD[15])
					->setCellValue('Q1',  $titulosColumnasD[16])
					->setCellValue('R1',  $titulosColumnasD[17])
					->setCellValue('S1',  $titulosColumnasD[18])
					->setCellValue('T1',  $titulosColumnasD[19])
					->setCellValue('U1',  $titulosColumnasD[20])
					->setCellValue('V1',  $titulosColumnasD[21])
					->setCellValue('W1',  $titulosColumnasD[22])
					->setCellValue('X1',  $titulosColumnasD[23])
					->setCellValue('Y1',  $titulosColumnasD[24])
					->setCellValue('Z1',  $titulosColumnasD[25])
					->setCellValue('AA1',  $titulosColumnasD[26])
					->setCellValue('AB1',  $titulosColumnasD[27])
					->setCellValue('AC1',  $titulosColumnasD[28])
					->setCellValue('AD1',  $titulosColumnasD[29])
					->setCellValue('AE1',  $titulosColumnasD[30])
					->setCellValue('AF1',  $titulosColumnasD[31])				
					->setCellValue('AG1',  $titulosColumnasD[32])
					->setCellValue('AH1',  $titulosColumnasD[33])
					->setCellValue('AI1',  $titulosColumnasD[34])
					->setCellValue('AJ1',  $titulosColumnasD[35])
					->setCellValue('AK1',  $titulosColumnasD[36])
					->setCellValue('AL1',  $titulosColumnasD[37])
					->setCellValue('AM1',  $titulosColumnasD[38])
					->setCellValue('AN1',  $titulosColumnasD[39])
					->setCellValue('AO1',  $titulosColumnasD[40])
					->setCellValue('AP1',  $titulosColumnasD[41])
					->setCellValue('AQ1',  $titulosColumnasD[42])
					->setCellValue('AR1',  $titulosColumnasD[43])
					->setCellValue('AS1',  $titulosColumnasD[44])
					->setCellValue('AT1',  $titulosColumnasD[45])
					->setCellValue('AU1',  $titulosColumnasD[46])
					->setCellValue('AV1',  $titulosColumnasD[47])
					->setCellValue('AW1',  $titulosColumnasD[48])
					->setCellValue('AX1',  $titulosColumnasD[49]);
					

		//Se agregan los datos 
		$id = 2;
		while ($filaD =mysql_fetch_array ($consulta_detenidos_t)){
			$objPHPExcel->setActiveSheetIndex(1)
        		    ->setCellValue('A'.$id,  $filaD['id'])
		            ->setCellValue('B'.$id,  $filaD['semana'])
        		    ->setCellValue('C'.$id,  $filaD['fecha'])
            		->setCellValue('D'.$id, utf8_encode($filaD['region']))
					->setCellValue('E'.$id, utf8_encode($filaD['select2']))
					->setCellValue('F'.$id, utf8_encode($filaD['organizacion']))	
					->setCellValue('G'.$id, utf8_encode($filaD['nombre']))
					->setCellValue('H'.$id, utf8_encode($filaD['monto_afectacion']))
					->setCellValue('I'.$id, utf8_encode($filaD['lugar_de_nacimiento']))
					->setCellValue('J'.$id, utf8_encode($filaD['edad']))
					->setCellValue('K'.$id, utf8_encode($filaD['estado_civil']))
					->setCellValue('L'.$id, utf8_encode($filaD['profesion']))

					->setCellValue('M'.$id, utf8_encode($filaD['estado']))
					->setCellValue('N'.$id, utf8_encode($filaD['calle']))
					->setCellValue('O'.$id, utf8_encode($filaD['colonia']))
					->setCellValue('P'.$id, utf8_encode($filaD['municipio']))
					->setCellValue('Q'.$id, utf8_encode($filaD['especialidad_delictiva']))
					->setCellValue('R'.$id, utf8_encode($filaD['apodo']))
					->setCellValue('S'.$id, utf8_encode($filaD['carpeta_de_investigacion']))
					->setCellValue('T'.$id, utf8_encode($filaD['consignado_a']))
					->setCellValue('U'.$id, utf8_encode($filaD['fecha_de_consignacion']))
					->setCellValue('V'.$id, utf8_encode($filaD['juzgado_penal']))
					
					->setCellValue('W'.$id, utf8_encode($filaD['causa_penal']))
					->setCellValue('X'.$id, utf8_encode($filaD['fecha_de_sentencia']))
					->setCellValue('Y'.$id, utf8_encode($filaD['condena']))
					->setCellValue('Z'.$id, utf8_encode($filaD['seguimiento']))
					
					->setCellValue('AA'.$id, utf8_encode($filaD['sentenciado']))
					->setCellValue('AB'.$id, utf8_encode($filaD['estatura']))
					->setCellValue('AC'.$id, utf8_encode($filaD['complexion']))
					->setCellValue('AD'.$id, utf8_encode($filaD['peso']))
					->setCellValue('AE'.$id, utf8_encode($filaD['color_de_piel']))
					->setCellValue('AF'.$id, utf8_encode($filaD['contorno_facial']))
					->setCellValue('AG'.$id, utf8_encode($filaD['tipo_de_pelo']))
					->setCellValue('AH'.$id, utf8_encode($filaD['color_de_pelo']))
					->setCellValue('AI'.$id, utf8_encode($filaD['frente']))
					->setCellValue('AJ'.$id, utf8_encode($filaD['cejas']))
					->setCellValue('AK'.$id, utf8_encode($filaD['ojos']))
					->setCellValue('AL'.$id, utf8_encode($filaD['color_de_ojos']))
					->setCellValue('AM'.$id, utf8_encode($filaD['tipo_de_nariz']))
					->setCellValue('AN'.$id, utf8_encode($filaD['bigote']))
					->setCellValue('AO'.$id, utf8_encode($filaD['tipo_de_boca']))
					->setCellValue('AP'.$id, utf8_encode($filaD['labios']))
					->setCellValue('AQ'.$id, utf8_encode($filaD['menton']))
					->setCellValue('AR'.$id, utf8_encode($filaD['cicatriz']))
					->setCellValue('AS'.$id, utf8_encode($filaD['tatuajes']))
					->setCellValue('AT'.$id, utf8_encode($filaD['deformacion_fisica']))
					->setCellValue('AU'.$id, utf8_encode($filaD['Imagen']))			
					->setCellValue('AV'.$id, utf8_encode($filaD['recuperacion']))
					->setCellValue('AW'.$id, utf8_encode($filaD['narracion']))
					->setCellValue('AX'.$id, utf8_encode($filaD['supervisor']));
		
					$id++;
		}
	    // Ajustar texto a las celdas
		for($id = 'A'; $id <= 'Z'; $id++){
			$objPHPExcel->setActiveSheetIndex(1)			
				->getColumnDimension($id)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Detenidos');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
			//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AW1"); 
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(2)->freezePaneByColumnAndRow(5,2);	
		
		//REPORTE ROBO DE VEHICULOS
		$titulosColumnasRV = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ENTIDAD','ORGANIZACION','NOMBRE DEL CONDUCTOR','RUTA','PUESTO','AREA','CLIENTE','HORARIO','CALLE','COLONIA','MUNICIPIO','AVERIGUACION PREVIA','JEFE INMEDIATO','MARCA','PLACA','MOTOR','AÑO','CANAL','PRECIO', 'CUMPLIO MEDIDAS','MEDIDAS','NUM. ECO.','RECUPERADO','CANCELACIÓN','NARRACIÓN','SEGUIMIENTO','ESTATUS','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(2)
        		    ->setCellValue('A1',  $titulosColumnasRV[0])
		            ->setCellValue('B1',  $titulosColumnasRV[1])
        		    ->setCellValue('C1',  $titulosColumnasRV[2])
            		->setCellValue('D1',  $titulosColumnasRV[3])
					->setCellValue('E1',  $titulosColumnasRV[4])
					->setCellValue('F1',  $titulosColumnasRV[5])
					->setCellValue('G1',  $titulosColumnasRV[6])
					->setCellValue('H1',  $titulosColumnasRV[7])
					->setCellValue('I1',  $titulosColumnasRV[8])
					->setCellValue('J1',  $titulosColumnasRV[9])
					->setCellValue('K1',  $titulosColumnasRV[10])
					->setCellValue('L1',  $titulosColumnasRV[11])
					->setCellValue('M1',  $titulosColumnasRV[12])
					->setCellValue('N1',  $titulosColumnasRV[13])
					->setCellValue('O1',  $titulosColumnasRV[14])
					->setCellValue('P1',  $titulosColumnasRV[15])
					->setCellValue('Q1',  $titulosColumnasRV[16])
					->setCellValue('R1',  $titulosColumnasRV[17])
					->setCellValue('S1',  $titulosColumnasRV[18])
					->setCellValue('T1',  $titulosColumnasRV[19])
					->setCellValue('U1',  $titulosColumnasRV[20])
					->setCellValue('V1', utf8_encode($titulosColumnasRV[21]))
					->setCellValue('W1',  $titulosColumnasRV[22])
					->setCellValue('X1',  $titulosColumnasRV[23])
					->setCellValue('Y1',  $titulosColumnasRV[24])
					->setCellValue('Z1',  $titulosColumnasRV[25])
					->setCellValue('AA1',  $titulosColumnasRV[26])
					->setCellValue('AB1',  $titulosColumnasRV[27])
					->setCellValue('AC1',  $titulosColumnasRV[28])
					->setCellValue('AD1',  $titulosColumnasRV[29])
					->setCellValue('AE1',  $titulosColumnasRV[30])
					->setCellValue('AF1',  $titulosColumnasRV[31])			
					->setCellValue('AG1',  $titulosColumnasRV[32]);		
					

		//Se agregan los datos 
		$irv = 2;
		while ($filaRV =mysql_fetch_array ($consulta_robos_T)){
			$objPHPExcel->setActiveSheetIndex(2)
        		    ->setCellValue('A'.$irv,  $filaRV['id'])
		            ->setCellValue('B'.$irv,  $filaRV['semana'])
        		    ->setCellValue('C'.$irv,  $filaRV['fecha'])
            		->setCellValue('D'.$irv, utf8_encode($filaRV['region']))
					->setCellValue('E'.$irv, utf8_encode($filaRV['select2']))
					->setCellValue('F'.$irv, utf8_encode($filaRV['select3']))	
					->setCellValue('G'.$irv, utf8_encode($filaRV['organizacion']))	
					->setCellValue('H'.$irv, utf8_encode($filaRV['nombre_del_conductor']))
					->setCellValue('I'.$irv, utf8_encode($filaRV['ruta']))
					->setCellValue('J'.$irv, utf8_encode($filaRV['puesto']))
					->setCellValue('K'.$irv, utf8_encode($filaRV['area']))
					->setCellValue('L'.$irv, utf8_encode($filaRV['cliente']))
					->setCellValue('M'.$irv, utf8_encode($filaRV['horario']))
					->setCellValue('N'.$irv, utf8_encode($filaRV['calle']))
					->setCellValue('O'.$irv, utf8_encode($filaRV['colonia']))
					->setCellValue('P'.$irv, utf8_encode($filaRV['municipio']))
					->setCellValue('Q'.$irv, utf8_encode($filaRV['averiguacion_previa']))
					->setCellValue('R'.$irv, utf8_encode($filaRV['jefe_inmediato']))
					->setCellValue('S'.$irv, utf8_encode($filaRV['marca']))
					->setCellValue('T'.$irv, utf8_encode($filaRV['placa']))
					->setCellValue('U'.$irv, utf8_encode($filaRV['motor']))
					->setCellValue('V'.$irv, utf8_encode($filaRV['año']))
					->setCellValue('W'.$irv, utf8_encode($filaRV['canal']))					
					->setCellValue('X'.$irv, utf8_encode($filaRV['precio']))
					->setCellValue('Y'.$irv, utf8_encode($filaRV['cumplio_medidas']))
					->setCellValue('Z'.$irv, utf8_encode($filaRV['medidas']))
					->setCellValue('AA'.$irv, utf8_encode($filaRV['economico']))				
					->setCellValue('AB'.$irv, utf8_encode($filaRV['recuperado']))
					->setCellValue('AC'.$irv, utf8_encode($filaRV['cancelacion']))
					->setCellValue('AD'.$irv, utf8_encode($filaRV['narracion']))
					->setCellValue('AE'.$irv, utf8_encode($filaRV['seguimiento']))
					->setCellValue('AF'.$irv, utf8_encode($filaRV['estatus']))
					->setCellValue('AG'.$irv, utf8_encode($filaRV['supervisor_de_seguridad']));

					$irv++;
		}
	    // Ajustar texto a las celdas
		for($irv = 'A'; $irv <= 'Z'; $irv++){
			$objPHPExcel->setActiveSheetIndex(2)			
				->getColumnDimension($irv)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Robo VHS');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
			//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AG1"); 
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(3)->freezePaneByColumnAndRow(6,2);	
		
				//REPORTE ROBO DE EQUIPO
		$titulosColumnasRE = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ENTIDAD','ORGANIZACION','NOMBRE DEL VENDEDOR','RUTA','CLIENTE','AREA','REPORTE','HORARIO','CALLE','COLONIA','MUNICIPIO-DELEGACION','AVERIGUACION PREVIA','JEFE INMEDIATO','CANAL','BAJDEJAS','CHAROLAS / TINAS','DOLLYS','PASCUALINEROS', 'VALOR EQUIPOS RECUPERADOS','PIEZAS DE PRODUCTO', 'VALOR PRODUCTOS RECUPERADOS' ,'TOTAL ROBADO','TOTAL DE RECUPERACION','CUMPLIO','MEDIDAS','NARRACION','SEGUIMIENTO','ESTATUS','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(4)
        		    ->setCellValue('A1',  $titulosColumnasRE[0])
		            ->setCellValue('B1',  $titulosColumnasRE[1])
        		    ->setCellValue('C1',  $titulosColumnasRE[2])
            		->setCellValue('D1',  $titulosColumnasRE[3])
					->setCellValue('E1',  $titulosColumnasRE[4])
					->setCellValue('F1',  $titulosColumnasRE[5])
					->setCellValue('G1',  $titulosColumnasRE[6])
					->setCellValue('H1',  $titulosColumnasRE[7])
					->setCellValue('I1',  $titulosColumnasRE[8])
					->setCellValue('J1',  $titulosColumnasRE[9])
					->setCellValue('K1',  $titulosColumnasRE[10])
					->setCellValue('L1',  $titulosColumnasRE[11])
					->setCellValue('M1',  $titulosColumnasRE[12])
					->setCellValue('N1',  $titulosColumnasRE[13])
					->setCellValue('O1',  $titulosColumnasRE[14])
					->setCellValue('P1',  $titulosColumnasRE[15])
					->setCellValue('Q1',  $titulosColumnasRE[16])
					->setCellValue('R1',  $titulosColumnasRE[17])
					->setCellValue('S1',  $titulosColumnasRE[18])
					->setCellValue('T1',  $titulosColumnasRE[19])
					->setCellValue('U1',  $titulosColumnasRE[20])
					->setCellValue('V1',  $titulosColumnasRE[21])
					->setCellValue('W1',  $titulosColumnasRE[22])
					->setCellValue('X1',  $titulosColumnasRE[23])
					->setCellValue('Y1',  $titulosColumnasRE[24])
					->setCellValue('Z1',  $titulosColumnasRE[25])
					->setCellValue('AA1',  $titulosColumnasRE[26])
					->setCellValue('AB1',  $titulosColumnasRE[27])
					->setCellValue('AC1',  $titulosColumnasRE[28])
					->setCellValue('AD1',  $titulosColumnasRE[29])
					->setCellValue('AE1',  $titulosColumnasRE[30])
					->setCellValue('AF1',  $titulosColumnasRE[31])
					->setCellValue('AG1',  $titulosColumnasRE[32])
					->setCellValue('AH1',  $titulosColumnasRE[33]);	
					

		//Se agregan los datos 
		$ire = 2;
		while ($filaRE =mysql_fetch_array ($consulta_re_t)){
			$objPHPExcel->setActiveSheetIndex(4)
        		    ->setCellValue('A'.$ire,  $filaRE['id'])
		            ->setCellValue('B'.$ire,  $filaRE['semana'])
        		    ->setCellValue('C'.$ire,  $filaRE['fecha'])
            		->setCellValue('D'.$ire, utf8_encode($filaRE['region']))
					->setCellValue('E'.$ire, utf8_encode($filaRE['select2']))
					->setCellValue('F'.$ire, utf8_encode($filaRE['select3']))	
					->setCellValue('G'.$ire, utf8_encode($filaRE['organizacion']))
					->setCellValue('H'.$ire, utf8_encode($filaRE['nombre_del_vendedor']))
					->setCellValue('I'.$ire, utf8_encode($filaRE['ruta']))
					->setCellValue('J'.$ire, utf8_encode($filaRE['cliente']))
					->setCellValue('K'.$ire, utf8_encode($filaRE['area']))
					->setCellValue('L'.$ire, utf8_encode($filaRE['reporte']))
					->setCellValue('M'.$ire, utf8_encode($filaRE['horario']))
					->setCellValue('N'.$ire, utf8_encode($filaRE['calle']))
					->setCellValue('O'.$ire, utf8_encode($filaRE['colonia']))
					->setCellValue('P'.$ire, utf8_encode($filaRE['delegacion']))
					->setCellValue('Q'.$ire, utf8_encode($filaRE['averiguacion']))
					->setCellValue('R'.$ire, utf8_encode($filaRE['jefe']))
					->setCellValue('S'.$ire, utf8_encode($filaRE['canal']))
					->setCellValue('T'.$ire, utf8_encode($filaRE['bandejas']))
					->setCellValue('U'.$ire, utf8_encode($filaRE['tinas']))
					->setCellValue('V'.$ire, utf8_encode($filaRE['dollys']))				
					->setCellValue('W'.$ire, utf8_encode($filaRE['maquina_autovend']))			
					->setCellValue('X'.$ire, utf8_encode($filaRE['recuperacion_equipo']))		
					->setCellValue('Y'.$ire, utf8_encode($filaRE['piezas_producto']))		
					->setCellValue('Z'.$ire, utf8_encode($filaRE['recuperacion_producto']))		
					->setCellValue('AA'.$ire, utf8_encode($filaRE['afectacione']))		
					->setCellValue('AC'.$ire, utf8_encode($filaRE['cumplio']))
					->setCellValue('AD'.$ire, utf8_encode($filaRE['medidas']))
					->setCellValue('AE'.$ire, utf8_encode($filaRE['narracion']))
					->setCellValue('AF'.$ire, utf8_encode($filaRE['seguimiento']))
					->setCellValue('AG'.$ire, utf8_encode($filaRE['estatus']))
					->setCellValue('AH'.$ire, utf8_encode($filaRE['supervisor']));
					$ire++;
		}
	    // Ajustar texto a las celdas
		for($ire = 'A'; $ire <= 'Z'; $ire++){
			$objPHPExcel->setActiveSheetIndex(4)			
				->getColumnDimension($ire)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Robo Equipo');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AF1");
		
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(4)->freezePaneByColumnAndRow(6,2);	
		
		//REPORTE SECCIONES
		$titulosColumnasSC = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ORGANIZACION','JEFE INMEDIATO','SECCION A REPORTAR','OBJETIVO','OBSERVACIONES','ESTATUS','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(5)
        		    ->setCellValue('A1',  $titulosColumnasSC[0])
		            ->setCellValue('B1',  $titulosColumnasSC[1])
        		    ->setCellValue('C1',  $titulosColumnasSC[2])
            		->setCellValue('D1',  $titulosColumnasSC[3])
					->setCellValue('E1',  $titulosColumnasSC[4])
					->setCellValue('F1',  $titulosColumnasSC[5])
					->setCellValue('G1',  $titulosColumnasSC[6])
					->setCellValue('H1',  $titulosColumnasSC[7])
					->setCellValue('I1',  $titulosColumnasSC[8])
					->setCellValue('J1',  $titulosColumnasSC[9])
					->setCellValue('K1',  $titulosColumnasSC[10])
					->setCellValue('L1',  $titulosColumnasSC[11]);				
					
		//Se agregan los datos 
		$isc = 2;
		while ($filaSC =mysql_fetch_array ($consulta_secciones_t)){
			$objPHPExcel->setActiveSheetIndex(5)
        		    ->setCellValue('A'.$isc,  $filaSC['id'])
		            ->setCellValue('B'.$isc,  $filaSC['semana'])
        		    ->setCellValue('C'.$isc,  $filaSC['fecha'])
            		->setCellValue('D'.$isc, utf8_encode($filaSC['region']))
					->setCellValue('E'.$isc, utf8_encode($filaSC['select2']))
					->setCellValue('F'.$isc, utf8_encode($filaSC['organizacion']))	
					->setCellValue('G'.$isc, utf8_encode($filaSC['jefe_inmediato']))
					->setCellValue('H'.$isc, utf8_encode($filaSC['s_reportar']))
					->setCellValue('I'.$isc, utf8_encode($filaSC['objetivo']))
					->setCellValue('J'.$isc, utf8_encode($filaSC['observaciones']))
					->setCellValue('K'.$isc, utf8_encode($filaSC['estatus']))
					->setCellValue('L'.$isc, utf8_encode($filaSC['supervisores']));

					$isc++;
		}
	    // Ajustar texto a las celdas
		for($isc = 'A'; $isc <= 'V'; $isc++){
			$objPHPExcel->setActiveSheetIndex(5)			
				->getColumnDimension($isc)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Secciones');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:M1"); 
		
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(5)->freezePaneByColumnAndRow(5,2);	
		
		
		//REPORTE EXTENDIDA
		$titulosColumnasSE = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ENTIDAD','ORGANIZACION','NOMBRE','AREA','PUESTO','SEXO','COLONIA','MUNICIPIO','PELIGROSIDAD','SALIDA DE CASA','SALIDA DE CEVE','TRASLADO CASA-CEVE','TRASLADO CEVE-CASA','TRANSPORTE','ACCIDENTE','TIPO DE ACCIDENTE','RECOMENDACION','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(6)
        		    ->setCellValue('A1',  $titulosColumnasSE[0])
		            ->setCellValue('B1',  $titulosColumnasSE[1])
        		    ->setCellValue('C1',  $titulosColumnasSE[2])
            		->setCellValue('D1',  $titulosColumnasSE[3])
					->setCellValue('E1',  $titulosColumnasSE[4])
					->setCellValue('F1',  $titulosColumnasSE[5])
					->setCellValue('G1',  $titulosColumnasSE[6])
					->setCellValue('H1',  $titulosColumnasSE[7])
					->setCellValue('I1',  $titulosColumnasSE[8])
					->setCellValue('J1',  $titulosColumnasSE[9])
					->setCellValue('K1',  $titulosColumnasSE[10])
					->setCellValue('L1',  $titulosColumnasSE[11])
					->setCellValue('M1',  $titulosColumnasSE[12])
					->setCellValue('N1',  $titulosColumnasSE[13])
					->setCellValue('O1',  $titulosColumnasSE[14])
					->setCellValue('P1',  $titulosColumnasSE[15])
					->setCellValue('Q1',  $titulosColumnasSE[16])
					->setCellValue('R1',  $titulosColumnasSE[17])
					->setCellValue('S1',  $titulosColumnasSE[18])
					->setCellValue('T1',  $titulosColumnasSE[19])
					->setCellValue('U1',  $titulosColumnasSE[20])
					->setCellValue('V1',  $titulosColumnasSE[21])
					->setCellValue('W1',  $titulosColumnasSE[22]);
					
					
		//Se agregan los datos 
		$ise = 2;
		while ($filaSE =mysql_fetch_array ($Recordset_Extendida)){
			$objPHPExcel->setActiveSheetIndex(6)
        		    ->setCellValue('A'.$ise,  $filaSE['id'])
		            ->setCellValue('B'.$ise,  $filaSE['semana'])
        		    ->setCellValue('C'.$ise,  $filaSE['fecha'])
            		->setCellValue('D'.$ise, utf8_encode($filaSE['region']))
					->setCellValue('E'.$ise, utf8_encode($filaSE['select2']))
					->setCellValue('F'.$ise, utf8_encode($filaSE['select3']))	
					->setCellValue('G'.$ise, utf8_encode($filaSE['organizacion']))
					->setCellValue('H'.$ise, utf8_encode($filaSE['nombre']))
					->setCellValue('I'.$ise, utf8_encode($filaSE['area']))
					->setCellValue('J'.$ise, utf8_encode($filaSE['puesto']))
					->setCellValue('K'.$ise, utf8_encode($filaSE['sexo']))
					->setCellValue('L'.$ise, utf8_encode($filaSE['colonia']))
					->setCellValue('M'.$ise, utf8_encode($filaSE['municipio']))
					->setCellValue('N'.$ise, utf8_encode($filaSE['peligro']))
					->setCellValue('O'.$ise, utf8_encode($filaSE['s_casa']))
					->setCellValue('P'.$ise, utf8_encode($filaSE['s_ceve']))
					->setCellValue('Q'.$ise, utf8_encode($filaSE['t_cace']))
					->setCellValue('R'.$ise, utf8_encode($filaSE['t_ceca']))
					->setCellValue('S'.$ise, utf8_encode($filaSE['transporte']))
					->setCellValue('T'.$ise, utf8_encode($filaSE['accidente']))
					->setCellValue('U'.$ise, utf8_encode($filaSE['t_accidente']))
					->setCellValue('V'.$ise, utf8_encode($filaSE['recomendacion']))
					->setCellValue('W'.$ise, utf8_encode($filaSE['supervisor']));

					$ise++;
		}
	    // Ajustar texto a las celdas
		for($ise = 'A'; $ise <= 'W'; $ise++){
			$objPHPExcel->setActiveSheetIndex(6)			
				->getColumnDimension($ise)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Seguridad Extendida');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
				//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:W1"); 
		
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(6)->freezePaneByColumnAndRow(6,2);
		

		// CARPETA ELECTRÓNICA
		$titulosColumnasCE = array('FOLIO', 'SEMANA', 'FECHA','REGION','CENTRO DE VENTAS', 'ENTIDAD','ORGANIZACION','FEHA DE REGISTRO','NUMERO DE COLABORADOR','PUESTO','ACTIVIDAD EVALUADA', 'AFECTACION','RECUPERACION','RESULTADO','COMENTARIOS','SUPERVISOR');	
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(7)
        		    ->setCellValue('A1',  $titulosColumnasCE[0])
		            ->setCellValue('B1',  $titulosColumnasCE[1])
        		    ->setCellValue('C1',  $titulosColumnasCE[2])
            		->setCellValue('D1',  $titulosColumnasCE[3])
					->setCellValue('E1',  $titulosColumnasCE[4])
					->setCellValue('F1',  $titulosColumnasCE[5])
					->setCellValue('G1',  $titulosColumnasCE[6])
					->setCellValue('H1',  $titulosColumnasCE[7])
					->setCellValue('I1',  $titulosColumnasCE[8])
					->setCellValue('J1',  $titulosColumnasCE[9])
					->setCellValue('K1',  $titulosColumnasCE[10])
					->setCellValue('L1',  $titulosColumnasCE[11])
					->setCellValue('M1',  $titulosColumnasCE[12])
					->setCellValue('N1',  $titulosColumnasCE[13])
					->setCellValue('O1',  $titulosColumnasSE[14])
					->setCellValue('P1',  $titulosColumnasSE[15]);
					
					
		//Se agregan los datos 
		$ica = 2;
		while ($filaCE =mysql_fetch_array ($Recordset_CE)){
			$objPHPExcel->setActiveSheetIndex(7)
        		    ->setCellValue('A'.$ica,  $filaCE['id'])
		            ->setCellValue('B'.$ica,  $filaCE['semana'])
        		    ->setCellValue('C'.$ica,  $filaCE['fecha'])
            		->setCellValue('D'.$ica, utf8_encode($filaCE['region']))
					->setCellValue('E'.$ica, utf8_encode($filaCE['select2']))
					->setCellValue('F'.$ica, utf8_encode($filaCE['select3']))	
					->setCellValue('G'.$ica, utf8_encode($filaCE['marca']))
					->setCellValue('H'.$ica, utf8_encode($filaCE['fechaAut']))
					->setCellValue('I'.$ica, utf8_encode($filaCE['num_colaborador']))
					->setCellValue('J'.$ica, utf8_encode($filaCE['puesto']))
					->setCellValue('K'.$ica, utf8_encode($filaCE['act_evaluada']))
					->setCellValue('L'.$ica, utf8_encode($filaCE['resultado']))
					->setCellValue('M'.$ica, utf8_encode($filaCE['comentarios']))
					->setCellValue('N'.$ica, utf8_encode($filaCE['nombre']))
				    ->setCellValue('O'.$ica, utf8_encode($filaCE['afectacion']))
					->setCellValue('P'.$ica, utf8_encode($filaCE['recuperacion']));
					
					$ica++;
		}
	    // Ajustar texto a las celdas
		for($ica = 'A'; $ica <= 'M'; $ica++){
			$objPHPExcel->setActiveSheetIndex(7)			
				->getColumnDimension($ica)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Carpeta Electronica');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		//$objPHPExcel->setActiveSheetIndex(3);
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(7)->freezePaneByColumnAndRow(6,2);
		
				//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:N1"); 
		
		
		//INVESTIGACIONES
		
		$titulosColumnasInv = array('FOLIO', 'SEMANA', 'REGION', 'CENTRO DE VENTAS', 'ENTIDAD','MARCA', 'RESPONSABLE CASO','CODIGO INVESTIGACION', 'ESTATUS','FECHA INICIO', 'FECHA DE CIERRE', 'ASUNTO','TIPO DE INVESTIGACION', 'DESCRIPCION DEL ASUNTO', 'SEGUIMIENTO','APRENDIO','MEJORA IMPLEMENTAR');
		
		// Se combinan las celdas A1 hasta D1, para colocar ahí el titulo del reporte
						
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(8)
					//->setCellValue('A1',$tituloReporte) // Titulo del reporte
        		    ->setCellValue('A1',  $titulosColumnasInv[0]) //Titulo de las columnas
		            ->setCellValue('B1',  $titulosColumnasInv[1])
        		    ->setCellValue('C1',  $titulosColumnasInv[2])
            		->setCellValue('D1',  $titulosColumnasInv[3])
					->setCellValue('E1',  $titulosColumnasInv[4])
					->setCellValue('F1',  $titulosColumnasInv[5])
					->setCellValue('G1',  $titulosColumnasInv[6])
					->setCellValue('H1',  $titulosColumnasInv[7])
					->setCellValue('H1',  $titulosColumnasInv[7])
					->setCellValue('I1',  $titulosColumnasInv[8])
					->setCellValue('J1',  $titulosColumnasInv[9])
					->setCellValue('K1',  $titulosColumnasInv[10])
					->setCellValue('L1',  $titulosColumnasInv[11])
					->setCellValue('M1',  $titulosColumnasInv[12])
					->setCellValue('N1',  $titulosColumnasInv[13])
					->setCellValue('O1',  $titulosColumnasInv[14])
					->setCellValue('P1',  $titulosColumnasInv[15])
					->setCellValue('Q1',  $titulosColumnasInv[16]);

		//Se agregan los datos 
		$inv = 2; //Numero de fila donde se va a comenzar a rellenar
		while ($fila =mysql_fetch_array ($consulta_investigaciones)){
			$objPHPExcel->setActiveSheetIndex(8)
        		    ->setCellValue('A'.$inv,  $fila['id'])
		            ->setCellValue('B'.$inv,  $fila['semana'])
        		    ->setCellValue('C'.$inv,  $fila['region'])
					->setCellValue('D'.$inv, utf8_encode($fila['select2']))					
					->setCellValue('E'.$inv, utf8_encode($fila['select3']))
				    ->setCellValue('F'.$inv, utf8_encode($fila['marca']))
					->setCellValue('G'.$inv, utf8_encode($fila['responsable_caso']))
					->setCellValue('H'.$inv, $fila['codigo'])				
					->setCellValue('I'.$inv, $fila['estatus'])
					->setCellValue('J'.$inv, utf8_encode($fila['fechaInicio']))
					->setCellValue('K'.$inv, utf8_encode($fila['fecha_cierre']))
					->setCellValue('L'.$inv, utf8_encode($fila['asunto']))	
					->setCellValue('M'.$inv, utf8_encode($fila['tipo_investigacion']))
					->setCellValue('N'.$inv, utf8_encode($fila['descripcion_asunto']))					
					->setCellValue('O'.$inv, utf8_encode($fila['seguimiento']))
					->setCellValue('P'.$inv, utf8_encode($fila['aprendio']))
					->setCellValue('Q'.$inv, utf8_encode($fila['mejora_implementar']));

					$inv++;
		}
	    // Ajustar texto a las celdas
		for($inv = 'A'; $inv <= 'Q'; $inv++){
			$objPHPExcel->setActiveSheetIndex(8)			
				->getColumnDimension($inv)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Investigaciones');

		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:Q1"); 
		
		
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('E4');
		$objPHPExcel->getActiveSheet(8)->freezePaneByColumnAndRow(5,2);
		
		//ACTIVIDADES TECNICOS DE SEGURIDAD
		
$titulosColumnasTec = array('FOLIO', 'SEMANA', 'FECHA', 'REGION', 'CENTRO DE VENTAS','ENTIDAD', 'MARCA','TECNICO DE SEGURIDAD', 'REVISION','FECHA DE CAPTURA', 'SUPERVISOR', 'ESTATUS','NOVEDADES / COMENTARIOS', 'COMENTARIOS DE CIERRE');

		// Se combinan las celdas A1 hasta D1, para colocar ahí el titulo del reporte
						
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(9)
					//->setCellValue('A1',$tituloReporte) // Titulo del reporte
        		    ->setCellValue('A1',  $titulosColumnasTec[0]) //Titulo de las columnas
		            ->setCellValue('B1',  $titulosColumnasTec[1])
        		    ->setCellValue('C1',  $titulosColumnasTec[2])
            		->setCellValue('D1',  $titulosColumnasTec[3])
					->setCellValue('E1',  $titulosColumnasTec[4])
					->setCellValue('F1',  $titulosColumnasTec[5])
					->setCellValue('G1',  $titulosColumnasTec[6])
					->setCellValue('H1',  $titulosColumnasTec[7])
					->setCellValue('H1',  $titulosColumnasTec[7])
					->setCellValue('I1',  $titulosColumnasTec[8])
					->setCellValue('J1',  $titulosColumnasTec[9])
					->setCellValue('K1',  $titulosColumnasTec[10])
					->setCellValue('L1',  $titulosColumnasTec[11])
					->setCellValue('M1',  $titulosColumnasTec[12])
					->setCellValue('N1',  $titulosColumnasTec[13]);

		//Se agregan los datos 
		$inv = 2; //Numero de filaTecnico donde se va a comenzar a rellenar
		while ($filaTecnico =mysql_fetch_array ($consulta_tecnicos)){
			$objPHPExcel->setActiveSheetIndex(9)
        		    ->setCellValue('A'.$inv,  $filaTecnico['id'])
		            ->setCellValue('B'.$inv,  $filaTecnico['fecha'])
        		    ->setCellValue('C'.$inv,  $filaTecnico['semana'])
					->setCellValue('D'.$inv, utf8_encode($filaTecnico['region']))					
					->setCellValue('E'.$inv, utf8_encode($filaTecnico['select2']))
				    ->setCellValue('F'.$inv, utf8_encode($filaTecnico['select3']))
					->setCellValue('G'.$inv, utf8_encode($filaTecnico['marca']))
					->setCellValue('H'.$inv, utf8_encode($filaTecnico['Nombre']))			
					->setCellValue('I'.$inv, utf8_encode($filaTecnico['revision']))
					->setCellValue('J'.$inv, utf8_encode($filaTecnico['fecha_captura']))
					->setCellValue('K'.$inv, utf8_encode($filaTecnico['supervisor']))
					->setCellValue('L'.$inv, utf8_encode($filaTecnico['estatus']))	
					->setCellValue('M'.$inv, utf8_encode($filaTecnico['comentarios']))
					->setCellValue('N'.$inv, utf8_encode($filaTecnico['comentarios_cierre']));
					
					$inv++;
		}
	    // Ajustar texto a las celdas
		for($inv = 'A'; $inv <= 'L'; $inv++){
			$objPHPExcel->setActiveSheetIndex(9)			
				->getColumnDimension($inv)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Actividades Tecnicos');
		
		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:N1"); 
		
			

		
		//OPERATIVOS
		
				$titulosColumnasO = array('FOLIO', 'SEMANA', 'FECHA INFORMO','FECHA INICIO', 'FECHA TERMINO', 'REGION','CENTRO DE VENTAS','ENTIDAD', 'ORGANIZACIÓN','TIPO OPERATIVO','ALTO IMPACTO','FALTATES EQUIPO', 'SOBRANTES EQUIPO','FALTANTE PRODUCTO', 'SOBRANTES PRODUCTO', 'AFECTACION', 'RECUPERACION', 'ANTECEDENTES', 'OBJETIVO','RESULTADOS','SUPERVISOR LIDER', 'INTEGRANTE 1','INTEGRANTE 2','INTEGRANTE 3','INTEGRANTE 4','INTEGRANTE 5','INTEGRANTE 6','INTEGRANTE 7','INTEGRANTE 8','INTEGRANTE 9','INTEGRANTE 10','ARCHIVO/INFORME','FECHA DE CAPTURA');
				
				
				$objPHPExcel->setActiveSheetIndex(10)
			
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
			$objPHPExcel->setActiveSheetIndex(10)
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
			$objPHPExcel->setActiveSheetIndex(10)			
				->getColumnDimension($io)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Operativos');

		//Crear Filtros en Hojas
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AG1"); 
		

		
		// Inmovilizar paneles 
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(5,2);
				
				
		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);	
		$objPHPExcel->removeSheetByIndex(11); 	
				
		
		
		//ESTRUCTURA EXCEL 
		$f=date("d-m-y H.i");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$fecha01="Reporte General de Seguridad (Descargado) ".$f.".xlsx";
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

<title>Reporte General</title>

<script>
$(document).ready(function(){
    
    $("input[type=submit]").click(function() {
        var accion = $(this).attr('dir');
        $('form2').attr('action', accion);
        $('form2').submit();
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
					<h1>DESCARGAR REPORTE GENERAL DE SEGURIDAD</h1>
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
	<img src="../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="../principal.php"><img src="../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>    </p>
  </div> </td>		

</br>
</br>
</br>


<form id="form2" name="form2" class= "reporte" method="post" action="">
  <label></label>
  <p><strong>-EXPORTAR REPORTE GENERAL</strong>
    <select name="supervisores" id="supervisores">
      <option value="- ELIGE -" <?php if (!(strcmp("", $_POST['nombre']))) {echo "selected=\"selected\"";} ?>> - ELIGE -</option>
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
    <img src="../imagenes/export_to_excel.gif" alt="excel" class="botonExcel" />
    <input type="submit" name="Descargar" id="Descargar" class="button themed" value="DESCARGAR" dir="../consulta_asaltos_sup.php" />
	

    </label>
  </p>
</form>

</body>
</html>

