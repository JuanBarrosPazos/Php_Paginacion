
<?php session_start(); ?>

<html>
    <body>

<?php

require 'config.php';

/* **************************************************** */

/*  */
    if(isset($_POST['oculto'])){    global $pag;
                                    $pag =1;
                                    sform();
                                    process();
                                    }
    elseif (!isset($_GET['pag'])){  global $pag;
                                      $pag = 1;
                                      sform();
                                      process();
                                    }
                                    else{ global $pag;
                                          $pag = $_GET['pag'];
                                          global $defaults;
                                          sform();
                                          process();
                                                }
    

/* **************************************************** */

function sform(){

    if(isset($_POST['oculto'])){$_SESSION['idioma'] = $_POST['idioma'];
                                $_SESSION['npg'] = $_POST['npg'];
                                $defaults = $_POST;}
    else{if(!isset($_SESSION['idioma'])){$_SESSION['idioma'] = "English";
                                         $_SESSION['npg'] = 10;
                                            }
                                         $defaults['idioma'] = $_SESSION['idioma'];
                                         $defaults['npg'] = $_SESSION['npg'];
                                            }

    $npg = array ('10' => 'ENTRADAS X PAGINA',
				 '10' => '10 ENTRADAS',
				 '15' => '15 ENTRADAS',
				 '20' => '20 ENTRADAS',
				 '25' => '25 ENTRADAS',
                 '30' => '30 ENTRADAS');
                
	print("<table align='center' style='border:1; margin-top:2px' width='auto'>
				
			<form name='idioma' method='post' action='$_SERVER[PHP_SELF]'>
				<tr>
					<td align='center'>
							FILTRE LOS PAISES POR IDIOMA
					</td>
				</tr>		
				<tr>
					<td>
					<div style='float:left; margin-right:6px'>
						<input type='submit' value='SELECCIONE UN LENGUAJE' />
						<input type='hidden' name='oculto' value=1 />
					</div>
					<div style='float:left'>

                        <select name='idioma'>
                        ");

	global $link;

    $sqlu =  "SELECT DISTINCT Language  FROM countrylanguage ORDER BY Language ASC ";
	$qu = mysqli_query($link, $sqlu);
	if(!$qu){
			print("Modifique la entrada L.51 ".mysqli_error($link)."<br/>");
	} else {
					
		while($rowu = mysqli_fetch_assoc($qu)){
					
            print ("<option value='".$rowu['Language']."' ");
            
            if($rowu['Language'] == $defaults['idioma']){
                                print ("selected = 'selected'");
                                                                }
                print (">".$rowu['Language']."</option>");
        }
    }  

	print ("	</select>
					</div>
            
            <div style='float:left'>
				<select name='npg'>");
				foreach($npg as $optionnpg => $labelnpg){
					print ("<option value='".$optionnpg."' ");
					if($optionnpg == $defaults['npg']){
													print ("selected = 'selected'");
													}
													print ("> $labelnpg </option>");
													}	
            print ("	</select>
                            </div>

                </form>	
                </td>
                </tr>
    
                    </table>"); 

        }

/* **************************************************** */

function process(){

    global $link;
    global $pag;

    if(strlen(trim(isset($_SESSION['idioma']))) == 0){$_SESSION['idioma'] = "English";} 
    if(strlen(trim(isset($_SESSION['npg']))) == 0){$_SESSION['npg'] = 10;} 

    global $lg;
    $lg = "'".trim($_SESSION['idioma'])."'";
    
    $result2 = mysqli_query($link, "SELECT COUNT(*) FROM country INNER JOIN countrylanguage ON country.Code = countrylanguage.CountryCode WHERE countrylanguage.Language = $lg ");
    list($total) = mysqli_fetch_row($result2);
   

    global $tampag;
    //$tampag = 12;
    $tampag = trim($_SESSION['npg']);
    global $reg1;
    $reg1 = ($pag-1) * $tampag;

    global $i;
    $i = $pag * $tampag;
    if ($i >= $total){$i = $total;}
    else{}
    echo "<b>* ".strtoupper($_SESSION['idioma'])." => Resultados: ".$i." de ".$total.".</b><br>";

   global $result;
    /*
    $result = mysqli_query($link, "SELECT `Name`, `Continent`, `Region` FROM `country` WHERE `Continent`= '$continent' LIMIT $reg1, $tampag" );
    */
    global $lg2;
    $lg2 = "'".trim($_SESSION['idioma'])."'";
    $result = mysqli_query($link, "SELECT * FROM country INNER JOIN countrylanguage ON country.Code = countrylanguage.CountryCode WHERE countrylanguage.Language = $lg2 ORDER BY country.Code ASC LIMIT $reg1, $tampag");

    if (mysqli_num_rows($result)){
        echo "<table border = '1'>
        ";
        echo "<tr><td> C. CODE </td><td> LANGUAGE </td><td> CODE </td><td> NOMBRE </td><td> CONTINENTE </td><td>REGION</td></tr>
        ";
        while ($row = @mysqli_fetch_array($result)) {
                echo "<tr>
                <td> ".$row['CountryCode']." </td>
                <td> ".$row['Language']." </td>
                <td> ".$row['Code']." </td>
                <td> ".$row['Name']." </td>
                <td> ".$row['Region']." </td>
                <td> ".$row['Continent']." </td>
                </tr>";
                }
        echo "</table>";
        }
        else
        echo "¡ No se ha encontrado ningún registro !";

        echo paginar($pag, $total, $tampag, "index.php?pag=");
    }

/* **************************************************** */

/* Funcion paginar
* actual: Pagina actual
* total: Total de registros
* por_pagina: Registros por pagina
* enlace: Texto del enlace
* maxpags: El máximo de páginas a presentar simultáneamente (opcional)
* Devuelve un texto que representa la paginacion
*/
function paginar($actual, $total, $por_pagina, $enlace, $maxpags=0) {
    $total_paginas = ceil($total/$por_pagina);
    $anterior = $actual - 1;
    $posterior = $actual + 1;
    $minimo = $maxpags ? max(1, $actual-ceil($maxpags/2)): 1;
    $maximo = $maxpags ? min($total_paginas, $actual+floor($maxpags/2)): $total_paginas;

    if ($actual>1)
    $texto = "<a href=".$enlace.$anterior.">&laquo;</a> ";
    else
    $texto = "<b>&laquo;</b> ";
    if ($minimo!=1) $texto.= "... ";
    for ($i=$minimo; $i<$actual; $i++)
    $texto .= "<a href=".$enlace.$i.">$i</a> ";
    $texto .= "<b>$actual</b> ";
    for ($i=$actual+1; $i<=$maximo; $i++)
    $texto .= "<a href=".$enlace.$i.">$i</a> ";
    if ($maximo!=$total_paginas) $texto.= "... ";
    if ($actual<$total_paginas)
    $texto .= "<a href=".$enlace.$posterior.">&raquo;</a>";
    else
    $texto .= "<b>&raquo;</b>";
    
    return $texto;

    }

    /* **************************************************** */

?>

    </body>
</html>