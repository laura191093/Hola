<?php

include '../conexion.php';



$q=$_POST['q'];


$con=conexion();

$sql="select * from directorio where nombre LIKE '".$q."%' AND estatus='ACTIVO'";
$res=mysql_query($sql,$con);

if(mysql_num_rows($res)==0){

 echo '<b>NO HAY SUGERENCIAS</b>';

}else{


 while($fila=mysql_fetch_array($res)){

  echo '<div class="sugerencias" onclick="myFunction2('.$fila["Id"].')">'.utf8_encode($fila['nombre']).'</div>';
  


 }


}

?>