<html>
	<head>
		<title>Algo</title>
		<?php
			$this->load->helper('html');
		?>
	</head>

	<body>
		<header>
            <h1>Algo</h1>
        </header>
        <div>
            <a href="index.php/init/createdb">Reset</a>
            <a href="init/repartition">Go !</a>
        </div>
        <pre>
        <?php
            print_r($_SESSION['result']);
        ?>
        </pre>
    </body>
</html>
                
				
			
