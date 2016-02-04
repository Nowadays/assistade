<?php
	/**
	 * Affiche l'espace privé d'un utilisateur.
	 * Cette vue nécessite les variables suivantes : $infos => tableau associatif ayant pour clé 'phone' et 'email'. Les valeurs sont le numéro de téléphone et l'adresse email
	 */
	
	$tab = array("N° de téléphone mobile : " => $infos['phone'],
				"Adresse e-mail : " => $infos['email']
				);
	
	echo div(array('style' => 'max-width: 800px;', 'class' => 'panel panel-info center-block'));
		echo div(array('class' => 'panel-heading text-center'));
		echo "<h2>Bonjour $username</h2>";
		echo "<i>Vous pouvez retrouver ici vos informations personnelles</i>";
		echo div_close();
		echo br();
		
		foreach($tab as $key => $value){
			echo div(array('class' => 'row'));
				echo div(array('class' => 'col-md-4 col-md-offset-3'));
					echo "<strong>".$key."</strong>";
				echo div_close();
				echo div(array('class' => 'col-md-1'));
					echo $value;
				echo div_close();
			echo div_close();
		}
		echo br();
		echo div(array('class' => 'text-center'));
			echo anchor('main/modifyInfo', 'Modifier', array('class' => 'btn btn-info'));
			echo br(2);
		echo div_close();
	echo div_close();
?>
