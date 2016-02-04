<?php
	/**
	 * Vue permettant à un responsable de voir la liste des vacataires, de se connecter en leur nom ou de créer un vacataire
	 * Cette vue nécessite les varaibles suivantes : (optionnel) $temporaryWorkers => tableau contenant des tableau associatifs décrivant un 'teacher'. Les clés sont :
	 * 'initials' , 'firstnale' , 'lastname'
	 * $fieldNames => tableau contenant les différents champs (texte) à remplir pour la création d'un vacataire
	 */

	$fieldNames = array('Initiales','Nom','Prénom');
	
	echo div(array('style' => 'max-width: 500px;', 'class' => 'text-center center-block'));
	
		echo '<p class="lead">Pour continuer :</p>';
		
		if(isset($temporaryWorkers) && !empty($temporaryWorkers))
		{
			$data = array();
			foreach ($temporaryWorkers as $t)
				$data[$t['initials']] = $t['firstname'] . ' ' . $t['lastname'];

			echo form_open('main/selectTemporaryWorkers', array('class' => 'form-group'));
				echo form_label('Sélectionnez le nom/prénom du vacataire :', 'login');
				echo nbs(2);
				echo form_dropdown('id', $data, '', 'id="login" class="form-control"');
				echo br(1);
				echo form_submit(array('value' => 'Connexion', 'class' => 'btn btn-info'));
				echo br(2);
				echo '<p class="lead">ou</p>';
			echo form_close();
		}
		
		echo form_label('créez un nouveau vacataire :');
		echo nbs(2);
		echo '<table id="myTable" class="table">';
			echo '<tr id="new">';
			foreach($fieldNames as $field)
				echo '<td><input type="text" class="form-control" placeholder="'.$field.'" style="padding : 0px;"/></td>';
			
			echo '<td><span style="cursor: pointer;" class="glyphicon glyphicon-floppy-disk" data-original-title="Enregistrer" data-placement="top" data-toogle="tooltip" onclick="saveNewRow()"></span></td>';
			echo '</tr>';
		echo '</table>';
		
	echo div_close();		
?>
