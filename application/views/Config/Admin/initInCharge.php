<?php
	/**
	 * Vues affichant le tableau permettant d'associer à une matière un responsable.
	 * Cette Vues nécessite les variables suivantes : $teachers => tableau des professeurs (contenant les initiales, le nom et le prénom)
	 * $subjects => tableau des matières contenant l'id et le nom court (short_name)
	 */
	echo div(array('class' => 'text-center'));
		echo heading("Configuration d'une nouvelle année", 1);
		echo heading("Quelques paramètres pour bien commencer...", 2);
	echo div_close();

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading("Initialisation Responsable/Matière");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			$data = array("default" => "Sélectionnez un professeur");
		
			foreach ($teachers as $t)
				$data[$t['initials']] = $t['firstname'] . ' ' . $t['lastname'];

			echo form_open('config/initInCharge', array('class' => 'form-group'));
			
				echo form_label('Sélectionnez les responsables de matière :', 'resp');
				echo br(2);
				
				echo '<table class="table table-bordered">';
				echo '<tr><th>Matière</th><th>Responsable</th></tr>';
				
				
				foreach($subjects as $s)
				{
					echo '<tr><td>';
					echo form_label($s['id']." - ".$s['short_name']);
					echo '</td><td>';
					echo form_dropdown('manageResp['.$s['id'].']', $data, (isset($responsibles[$s['id']]))? $responsibles[$s['id']] : 'default', 'id="resp" class="form-control"');
					echo '</td></tr>';
				}
				
				echo '</table>';
				
				echo form_submit(array('value' => 'Valider', 'class' => 'btn btn-info'));
			echo form_close();
		echo div_close();
	echo div_close();
?>