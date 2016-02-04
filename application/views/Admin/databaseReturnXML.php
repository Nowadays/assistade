<?php
	/**
	 * Vues retournant un résultat et un message après avoir demandé une modification de la base de données côté administrateur (via AJAX)
	 *
	 * Cette vue nécessite les variables suivantes : 'state' => état de la requete, 'message' => message à afficher en réponse à l'utilisateur
	 */
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<all>';
		echo '<result state="'.$state.'" message="'.$message.'"/>';
	echo '</all>';
?>