<?php
	/**
	 * Vue permettant le gestion des matières (Ajout, modification et suppression).
	 *
	 * Cette vue nécessite les variables suivantes : 'subjects' => tableau de tableaux associatif représentant chacun une matière ('id', 'short_name', 'subject_name')
	 */
	
	echo div(array('class' => 'modal fade', 'id' => 'loading', 'tabindex' => -1, 'aria-hidden' => 'true'));
		echo div(array('class' => 'modal-dialog'));
			echo div(array('class' => 'modal-content'));
				echo div(array('id' => 'modal-content', 'class' => 'modal-body center-block text-center'));
					echo '<p>Chargement</p>';
					echo nbs(4);
					echo img('res/img/loading.gif');
				echo div_close();
			echo div_close();
		echo div_close();
	echo div_close();

	echo div(array('class' => 'text-center center-block'));
		$headers = array('Identifiants', 'Nom court','Nom de la matière', 'Promo', 'Heures CM/Semaine', 'Heures TD/Semaine', 'Heures TP/Semaine', 'Options');

		echo '<table id="myTable" class="table table-bordered table-striped">';
			echo '<tr>';
				foreach ($headers as $header)
					echo "<th class='text-center'>$header</th>";
			echo '</tr>';

            foreach ($subjects as $subject)
			{
				echo '<tr id="'. $subject['id'] .'">';
				echo '<td>'. $subject['id'] .'</td><td>'. $subject['short_name'] .'</td><td>'. $subject['subject_name'] .'</td><td>'. $subject['promo_id'] .'</td><td>'. $subject['hours_cm'] .'</td><td>'. $subject['hours_td'] .'</td><td>'. $subject['hours_tp'] .'</td>';
				echo '<td><span style="cursor: pointer;" class="glyphicon glyphicon-pencil" data-original-title="Modifier" data-placement="top" data-toogle="tooltip" class="btn btn-info" onclick="editRow(\''. $subject['id'] .'\')"></span>'.nbs(4).'<span style="cursor: pointer;" class="glyphicon glyphicon-trash" data-original-title="Supprimer" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\''. $subject['id'] .'\')"></span></td>';
				echo '</tr>';
			}
		echo '</table>';
		echo '<button id="addButton" class="btn btn-info" onclick="addRow()">Ajouter une ligne</button>';
	echo div_close();
?>



