<?php
	/**
	 * Vue affichant le tableau permettant de saisir son planning
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
	 * (optionnel) $TeacherTimeSlot => tableau contenant les clé, l'id d'un crénaux horaire et pour valeur un tableau contenant "availability_level"
     * (ajout) $minHour => Le nombre minimal d'heures à rentrer par l'enseignant,
     *'status' => tableau contenant les status des créneaux horaires (0 si sélectionnable, 1 sinon)
	 */
	echo div(array('class' => 'text-center center-block'));
		
		echo form_button(array('content' => 'Remettre à blanc', 'class' => 'btn btn-xs active', 'id' => 'white'));
		echo nbs(2);
		echo form_button(array('content' => 'Indisponible', 'class' => 'btn btn-xs btn-inverse', 'id' => 'black'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire à éviter', 'class' => 'btn btn-xs btn-danger', 'id' => 'red'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire de disponible', 'class' => 'btn btn-xs btn-success', 'id' => 'green'));

		echo br(2);
		echo form_open('main/setAvailability');

			$TeacherTimeSlot = (empty($TeacherTimeSlot)) ? TRUE : $TeacherTimeSlot;

            echo '<p>Nombre d\'heures disponnibles : '.$effectivHours.'</p>';
            echo '<p>Nombre d\'heures minimal de disponnibilité : '.$miniHours.'</p>';

			echo availabilityTable($hours, $status, $TeacherTimeSlot);

			echo form_button(array('content' => 'Remettre à zéro', 'class' => 'btn btn-danger', 'id' => 'reset'));
			echo nbs(2);
			echo form_submit(array('name' => 'save', 'value' => 'Enregistrer', 'class' => 'btn btn-info'));
			echo nbs(2);
			echo form_submit(array('name' => 'validate', 'value' => 'Valider définitivement', 'class' => 'btn btn-success'));
		echo form_close(); 
	echo div_close();
?>