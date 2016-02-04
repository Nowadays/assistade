<?php
	/**
	 * Vue permettant la gestion des enseignant (ajout, modification et suppression).
	 *
	 * Cette vue nécessite les variables suivantes : 'teachers' => tableau contenant des tableaux associatif représentant chacun un enseignant ('initials', 'lastname', 'firstname').
	 */

	echo div(array('style' => 'max-width: 700px;', 'class' => 'text-center center-block'));
		$headers = array('Initiales', 'Nom','Prénom', 'Options');

		echo '<table id="myTable" class="table table-bordered">';
			echo '<tr>';
				foreach ($headers as $header)
					echo "<th>$header</th>";
			echo '</tr>';

			foreach ($teachers as $teacher)
			{
				echo '<tr id="'. $teacher['initials'] .'">';
				echo '<td>'. $teacher['initials'] .'</td><td>'. $teacher['lastname'] .'</td><td>'. $teacher['firstname'] .'</td>';
				echo '<td><span style="cursor: pointer;" class="glyphicon glyphicon-pencil" data-original-title="Modifier" data-placement="top" data-toogle="tooltip" onclick="editRow(\''. $teacher['initials'] .'\')"></span>'.nbs(4).'<span style="cursor: pointer;" class="glyphicon glyphicon-trash" data-original-title="Supprimer" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\''. $teacher['initials'] .'\')"></span></td>';
				echo '</tr>';
			}
		echo '</table>';
		echo '<button id="addButton" class="btn btn-info" onclick="addRow()">Ajouter une ligne</button>';
	echo div_close();

	echo div(array('class' => 'modal fade', 'id' => 'loading', 'tabindex' => -1, 'aria-hidden' => 'true'));
		echo div(array('class' => 'modal-dialog'));
			echo div(array('class' => 'modal-content'));
				echo div(array('id' => 'modal-content' ,'class' => 'modal-body center-block text-center'));
					echo '<p>Chargement</p>';
					echo nbs(4);
					echo img('res/img/loading.gif');
				echo div_close();
			echo div_close();
		echo div_close();
	echo div_close();
?>
