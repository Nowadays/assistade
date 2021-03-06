<?php
	/**
	 * Vue permettant la gestion des responsables de matière.
	 *
	 * Cette vue nécessite les variables suivantes : 'teachers' => tableau contenant des tableaux associatif représentant chacun un enseignant ('initials', 'firstname', 'lastname'),
	 * 'subjects' => tableau contenant des tableaux associatif représentant chacun une matière ('id', 'short_name', 'subject_name'), 'responsible' => tableau associatif ayant pour 
	 * clé l'id d'une matière et pour valeur l'id du responsable de matière
	 */
?>
<div style="margin: auto;" class="text-center">
	<?php
		$data = array("default" => "Sélectionnez un professeur");
		
		foreach ($teachers as $t)
			$data[$t['initials']] = $t['firstname'] . ' ' . $t['lastname'];

		echo form_open('admin/manageResponsibles', array('class' => 'form-group'));
			
			echo '<table id="myTable" class="table table-bordered table-striped">';
			echo '<tr><th class="text-center">Matière</th><th class="text-center">Responsable</th></tr>';
			
			
			foreach($subjects as $s){
				echo '<tr><td>';
				echo form_label($s['id']." - ".$s['short_name']);
				echo '</td><td>';
				echo form_dropdown('manageResp['.$s['id'].']', $data, (isset($responsibles[$s['id']]))? $responsibles[$s['id']] : 'default', 'id="resp" class="form-control"');
				echo '</td></tr>';
			}
			
			echo '</table>';
			
			echo form_submit(array('value' => 'Valider', 'class' => 'btn btn-info'));
		echo form_close();
	?>
</div>

