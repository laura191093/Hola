<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0055)http://www.jose-aguilar.com/scripts/jquery/slide-popup/ -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>jQuery Slide Popup</title>
<link rel="stylesheet" type="text/css" href="style.css">	
<script type="text/javascript" src="../s_popup/jquery.js"></script>
<script type="text/javascript">
function openDialog() {
	$('#overlay').fadeIn('fast', function() {
		$('#popup').css('display','block');
        $('#popup').animate({'left':'30%'},500);
    });
}

function closeDialog(id) {
	$('#'+id).css('position','absolute');
	$('#'+id).animate({'left':'-100%'}, 500, function() {
		$('#'+id).css('position','fixed');
		$('#'+id).css('left','100%');
		$('#overlay').fadeOut('fast');
	});
}
</script>
</head>

<body onload="openDialog();">
<div id="content">
<div id="overlay" class="overlay"></div>
<!--<a onclick="openDialog();">Mostrar publicidad</a>-->

<div id="popup" class="popup" style="display: block; left: 30%;">
	 <a onclick="closeDialog('popup');" class="close"></a>
	<div>
    	<h2></h2>
        <div style="float:left; width:120px;">
        	<div align="center"><img src="../../imagenes/user.png" width="70" height="70"></div>
        </div>

        <div style="float:left; width:300px;">
          <div class="mensaje">Para poder procesar la ficha es necesario adjuntar la imagen del detenido.<br>
	        <br>
          </div>

            	
      </div>
    </div>
  </div>
</div>
</div>


</body></html>
