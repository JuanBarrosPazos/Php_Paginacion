<?php

$db_host = '127.0.0.1'; 
$db_user = 'JuanBarros'; 
$db_pass = 'MariaPazos'; 
$db_name = 'world'; 

global $link;
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if (!$link){ die ("Es imposible conectar con la bbdd ".$db_name."<br/>".mysqli_connect_error());
            }else {/*echo "OK BBDD<br><br>";*/}


?>