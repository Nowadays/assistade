<?php
	/**
	 * Vues affichant le mot de passe de chaque professeur nouvellement créer.et permet de les imprimer
	 * Ces mot de passe ne sont affichés qu'une seul fois via cette vues.
	 * Cette vues nécessite les variables suivantes : $passwords => tableau ayant pour clé le nom du professeur et pour valeur sont mot de passe
	 */
	echo div(array('class' => 'text-center'));
		echo heading("Configuration d'une nouvelle année", 1);
		echo heading("Quelques paramètres pour bien commencer...", 2);
	echo div_close();

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading("Mot de passe 1ère connexion des professeurs");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo '<p class="label label-warning">Attention ! Ces mots de passe ne seront affiché qu\'une seule fois ! Veuillez à les imprimés</p>';
			echo br(2);
			echo '<a href="#" onclick="print()" class="btn btn-success">Imprimer</a>';

			echo br(2);
				
			echo '<table class="table table-bordered">';
			echo '<tr><th>Nom Professeur</th><th>Mot de passe</th></tr>';
			
			
			foreach($passwords as $name => $password)
			{
				echo '<tr>';
					echo "<td>$name</td>";
					echo "<td>$password</td>";
				echo '</tr>';
			}
			
			echo '</table>';
			
			echo anchor('config/initSubject', 'Continuer', array('class' => 'btn btn-primary'));
		echo div_close();
	echo div_close();
?>