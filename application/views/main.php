<html>
	<head>
		<title>Algo</title>
		<?php
			$this->load->helper('html');
            echo link_tag('assets/css/bootstrap.min.css');
		?>
	</head>

	<body>
		<header>
            <h1>Algo</h1>
        </header>
        <div>
            <a href="index.php/init/createdb">Reset</a>
            <a href="init/gettables">Start</a>
            <a href="init/repartition">Go !</a>
        </div>
        <div>
            <?php
                if(!isset($_SESSION['tables'])){
                }else{
                    for($i=0;$i<3;$i++){
                        echo "<div class='col-md-4'><div class='col-md-6'><table class='table''><thead><tr>";
                        $head=$_SESSION['tables'][$i][0];
                        foreach($head as $key=>$value){
                            echo "<th>";
                            echo $key;
                            echo "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        foreach($_SESSION['tables'][$i] as $ligne){
                            echo "<tr>";
                            foreach($ligne as $key=>$value){
                                echo "<td>";
                                echo $value;
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table></div><div class='col-md-6'><table class='table''><thead><tr>";
                        $head=$_SESSION['tables'][$i+3][0];
                        foreach($head as $key=>$value){
                            echo "<th>";
                            echo $key;
                            echo "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        foreach($_SESSION['tables'][$i+3] as $ligne){
                            echo "<tr>";
                            foreach($ligne as $key=>$value){
                                echo "<td>";
                                echo $value;
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table></div></div>";
                    }
                }
            ?>
        </div>
        <div class="col-md-3">
            <table class="table table-res table-hover">
                <?php
                if(!isset($_SESSION['result'])){
                }else{
                    $head=$_SESSION['result'][0];
                    echo "<thead><tr>";
                    foreach($head as $key=>$value){
                        echo "<th>";
                        echo $key;
                        echo "</th>";
                    }
                    echo "</tr></thead><tbody>";
                    foreach($_SESSION['result'] as $ligne){
                        echo "<tr>";
                        foreach($ligne as $key=>$value){
                            echo "<td>";
                            echo $value;
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody>";
                }
                ?>
            </table>
        </div>
    </body>
</html>
                
				
			
