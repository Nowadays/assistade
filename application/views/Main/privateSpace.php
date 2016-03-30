<?php
	/**
	 * Affiche l'espace privé d'un utilisateur.
	 * Cette vue nécessite les variables suivantes : $infos => tableau associatif ayant pour clé 'phone' et 'email'. Les valeurs sont le numéro de téléphone et l'adresse email
	 */
	
	$tab = array("N° de téléphone mobile : " => $infos['phone'],
				"Adresse e-mail : " => $infos['email']
				);
	
	echo div(array('class' => 'panel panel-info center-block'));
		echo div(array('class' => 'panel-heading text-center'));
		echo "<h2>Bonjour $username</h2>";
        echo br();
		echo div_close();
		echo br();
		
		foreach($tab as $key => $value){
			echo div(array('class' => 'row'));
				echo div(array('class' => 'col-md-offset-4 col-lg-offset-4 col-sm-offset-4'));
					echo "<strong>".$key."</strong> ".$value;
                    echo br(2);
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
