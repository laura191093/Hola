<?php

include '../conexion.php';


$q=$_POST['q'];


$con=conexion();

$sql="SELECT d. id, d.region,d.select2,d.select3,d.instalacion,d.marca,d.tel_ceve,d.direccion,d.tel_policia,d.tel_bomberos,d.administrador,
d.correo_admin, s.nombre as supervisor,  s.celular as tel_supervisor, t.Nombre as tecnico_seguridad, t. celular as telefono_tecnico, d.tiene_cctv, d.num_camaras, d.tiene_dialler, d. latitud, d. longitud FROM directorio_instalaciones d
LEFT JOIN directorio s ON d.id_usuario=s.Id
LEFT JOIN datos_tecnicos t ON id_tecnico1=t.Id where d.select2 LIKE '".$q."%'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{


 while($fila=mysql_fetch_array($res)){

  echo '<div class="sugerencias" onclick="myFunction2
  (
  '.$fila["id"].'
  )">
  '.utf8_encode($fila['select2']).' - '.utf8_encode($fila['marca']).'
  </div>';
  


 }


}

?>