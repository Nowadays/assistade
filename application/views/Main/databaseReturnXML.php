<?php
	/**
	 * Vue renvoyant un fichier XML pour répondre à une requête AJAX.
	 * Cette vue nécessite les variables suivantes : $state => état du résultat (success ou failed)
	 * $message => message à afficher en retour
	 */
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<all>';
		echo '<result state="'.$state.'" message="'.$message.'"/>';
	echo '</all>';
?>