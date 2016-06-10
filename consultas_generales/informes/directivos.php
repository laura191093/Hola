<?php require_once('../../connections/conexion_usuarios.php'); ?>

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
	
  $logoutGoTo = "cm_ingreso.php";
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

$MM_restrictGoTo = "cm_ingreso.php";
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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}




?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" href="../../style/style_principal.css">
<link rel="stylesheet" type="text/css" href="../../style/style_button.css">
<link rel="stylesheet" type="text/css" href="../../style/style_camposBitacora.css">
<link rel="stylesheet" type="text/css" href="../../style/menu/css/baseReportes.css">
<link rel="stylesheet" type="text/css" href="../../style/menu/css/zonas.css">
<title>Seguimientos a Bitacoras</title>

<link rel="stylesheet" type="text/css" href="../../style/bitacoras/notificacion_Bitacoras.css">

<script type="text/javascript" src="../../style/bitacoras/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../style/bitacoras/jquery-ui-1.8.6.min.js"></script>


<script language="javascript">
function copiarFolio()
{
document.getElementById("id").value = document.getElementById("folio").value;
}
</script>


<script type="text/javascript">
$(document).ready(function() {

    $("#enviar-btn").click(function() {

        var name = $("input#name").val();
        var comment = $("textarea#comment").val();
		var id = $("input#id").val();
        var now = new Date();
        var date_show = now.getDate() + '-' + now.getMonth() + '-' + now.getFullYear() + ' ' + now.getHours() + ':' + + now.getMinutes() + ':' + + now.getSeconds();

       
        
        if (comment == '') {
            alert('Añade comentarios respecto al seguimiento');
            return false;
        }


        var dataString = 'nombre=' + name + '&comentarios=' + comment + '&id=' + id ;

        $.ajax({
                type: "POST",
                url: "controller/addcomment.php",
                data: dataString,
                success: function() {
                    $('#newmessage').append('<div class="comment"><div class="comment-avatar"><img width="38" height="38" src="style/bitacoras/user.png" /></div><div class="comment-time">'+date_show+'</div><div class="comment-autor">'+name+'</div><div class="comment-text">'+comment+'</div></div>');
				
					
                }
        });
        return false;
    });
});
</script>


<script type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' Debe de contener numero.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' - campo requerido.\n'; }
  } if (errors) alert('Validar los datos:\n'+errors);
  document.MM_returnValue = (errors == '');

}
//-->
</script>



</head>

<body onload="document.getElementById('folio').focus();">

<div id="body1">
  <div id="pagina">
    <div id="cabezal">
      <div class="ancho940">
        <div id="logo" class="col4 primeracol"><img src="../../style/menu/img/content/oso.png" width="141" height="130" alt="osoSeguridad" /> <img src="../../style/menu/img/content/escudo.jpg" width="125" height="130" alt="escudo" /></div>
        <div id="contenidocab" class="col8 ">
          <table width="623" border="0" cellpadding="0" cellspacing="0" style="width: 780px;">
            <tbody>
              <tr>
                <td><h1>INFORMES</h1>
                    <br />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
	
	<br/>
	<br/>
	
    <div id="cajacontenidos">
      <div id="zonacontenidos" class="ancho940">
        <td width="341"><div class="sesion"> BIENVENIDO: <?php echo $row_consulta_usuario['Nombre']; ?> <img src="../../imagenes/profile.png" alt="perfil" width="35" height="32" border="0" title="PERFIL"/> | <a href="cm_principal.php"><img src="../../imagenes/principal.png" alt="sesion" width="35" height="37" border="0" title="MENÚ PRINCIPAL" /></a> | <a href="<?php echo $logoutAction ?>"><img src="../../imagenes/logout.png" alt="sesion" width="25" height="30" border="0" title="CERRAR SESI&Oacute;N" /></a>
                <p></p>
        </div></td>
			<br/>
        <div id="div-principal">
          <form method="post" name="form1" class= "reporte" action="<?php echo $editFormAction; ?>">
            <table width="1096" align="center">
              <tr valign="baseline">
                <td width="179">SEMANA </td>
                <td width="117">&nbsp;</td>
                <td width="136">&nbsp;</td>
                <td width="95">&nbsp;</td>
                <td width="127">&nbsp;</td>
                <td width="117">&nbsp;</td>
                <td width="293">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>MES</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>SUPERVISOR</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>CENTRO DE VENTAS </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>ENTIDAD</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>% AFECTACION </td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>RECUPERACI&Oacute;N DE EQUIPO </td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>RECUPERACI&Oacute;N PRODUCTO</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>EFECTIVO</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>RECUPERACION</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td>VENDEDORES MAS ASALTADOS </td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr valign="baseline">
                <td align="right" nowrap><div align="left"></div></td>
                <td colspan="6" align="right" nowrap></td>
              </tr>
            </table>
            <input type="hidden" name="MM_update" value="form1" />
            <input type="hidden" name="id" value="<?php echo $row_Recordset1['id']; ?>" />
          </form>
        </div>
      </div>
	  <br />
	  
      <form method="post" class= "reporte" action="">
	  
	  <div class="register_form">
        <table width="818" border="1">
          <tr>
            <td colspan="3"><div class="tituloComentarios"></div></td>
            </tr>
          <tr>
            <td width="17">&nbsp;</td>
            <td width="623"><input type="text" id="name" name="name" size="5" style="display:none" value="<?php echo $row_consulta_usuario['Nombre']?>"/><input type="text" id="id" name="id" size="5" value="" style="display:none" /></td>
            <td width="156">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
	</div>
      </form>
    </div>
  </div>
</div>
</div> 
</body>
</html>
<?php
mysql_free_result($Recordset1);
mysql_free_result($Recordset2);
mysql_free_result($consulta_usuario);
mysql_free_result($supervisor);
?>