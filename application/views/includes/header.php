<?php
	/**
	 * Vue affichant l'en-tête HTML de chaque page
	 * Cette vue affiche le doctype HTML5, affiche l'icone sur le naviguateur et inclut les fichiers CSS bootstrap
	 */
	echo doctype('html5'); 
?>
<html>
	<head>
		<title>Assist'Edt - À votre service !</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="icon" href="<?php echo base_url('res/img/logo.ico'); ?>" type="image/vnd.microsoft.ico" />

		<?php
			echo '<meta charset="UTF-8" />';
			echo link_tag('assets/css/bootstrap.min.css');
			echo link_tag('assets/css/bootstrap-theme.min.css');
            echo link_tag('assets/css/style.css');
		?>

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	
	</head>
	<body>
        <?php echo br(2) ?>
        <div class="page-header text-center">
	       <?php echo heading(img(base_url() . 'res/img/logo.png') . "  Assist'Edt"); echo br(1); ?>	
        </div>
        <div class="container">