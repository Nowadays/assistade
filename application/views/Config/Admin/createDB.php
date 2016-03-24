<?php
	/**
	 * Vue de la première page de configuration de l'application. Explique le pré-requis pour la base de données et permet de continuer la configuration
	 */    

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading('Configuration de la base de données');
		echo div_close();

		echo div(array('class' => 'panel-body text-left'));
			echo '<p class="text text-information"> Pour utiliser cette application, il nous faut un accès à une base de données. Pour nous permettre d\'y accèder, vous devez nous fournir les informations de connexion dans le fichier :</p>'; 
            echo '<pre>application/config/database.php</pre>';
            echo '<p>Vous devrez indiquer :</p>';
			echo ul(array("le nom d'utilisateur (username)", 
				"le mot de passe (password)", 
				'Éventuellement un nom de schéma <span class="label label-danger">Attention, le nom de schéma doit terminer par un point !</span> (dbprefix)'),
			array('class' => 'text-left'));
			echo '<p>Vous devez renseigner ces informations entre " \' " après les "="</p>';
            echo '<p>Exemple : </p>';
            echo '<pre> $db[\'default\'][\'hostname\'] = \'servbdd.iutlan.etu.univ-rennes1.fr\';</pre>';
            echo '</p>';
		echo div_close();

		echo div(array('class' => 'panel-body text-center'));
			echo '<p>Si vous avez correctement renseigné ces informations, nous pouvons continuer. Attention, cela peut prendre un certain temps !</p>';
			echo br();
            echo anchor('config/createDB', 'Continuer', array('class' => 'btn btn-success'));
            echo br();
	   echo div_close(); 
?>