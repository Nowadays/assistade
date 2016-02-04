<?php
	/**
	 * Vue ajoutant les fichiers javascript nécessaire à bootstrap et les fichiers javascripts nécessaire à la page en cours
	 * Cette vue nécessite les variables suivantes : (peut être vide) $js => tableau contenant le nom du fichier javascript (finissant par .js)
	 * se trouvant le dossier (racine du site)/res/js/
	 */
?>		
		<script src="<?php echo base_url('/assets/js/jquery.min.js'); ?>"></script>
		<script src="<?php echo base_url('/assets/js/bootstrap.min.js'); ?>"></script>
		<?php
			if(!empty($js))
				foreach ($js as $file)
					echo '<script src="' .base_url('/res/js/'. $file). '"></script>';
		?>
	</body>
</html>