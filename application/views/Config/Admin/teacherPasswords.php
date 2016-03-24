<?php
	/**
	 * Vues affichant le mot de passe de chaque professeur nouvellement créer.et permet de les imprimer
	 * Ces mot de passe ne sont affichés qu'une seul fois via cette vues.
	 * Cette vues nécessite les variables suivantes : $passwords => tableau ayant pour clé le nom du professeur et pour valeur sont mot de passe
	 */

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading("Mots de passe 1ère connexion des enseignants");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo '<p class="label label-warning">Attention ! Ces mots de passe ne seront affichés qu\'une seule fois, veillez à les imprimer !</p>';
			echo br(2);
			echo '<a href="#" onclick="print()" class="btn btn-info">Imprimer</a>';

			echo br(2);
				
			echo '<table id="myTable" class="table table-bordered table-striped">';
			echo '<tr><th>Nom Professeur</th><th>Mot de passe</th></tr>';
			
			
			foreach($passwords as $name => $password)
			{
				echo '<tr>';
					echo "<td>$name</td>";
					echo "<td><b>$password<b></td>";
				echo '</tr>';
			}
			
			echo '</table>';
			echo anchor('config/initSubject', 'Continuer', array('class' => 'btn btn-success','id'=>'continue_btn','onclick'=>'verif()'));
		echo div_close();
	echo div_close();
?>

<script>
	function verif(){
		confirm("Avez-vous sauvegardé les mots de passe ?");
	}
</script>
