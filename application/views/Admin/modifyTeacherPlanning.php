<?php
	/**
	 * Vue permettant de modifier le planning d'un professeur.
	 *
	 * Cette vue nécessite les variables suivantes : 'teacherName' => nom de l'enseignant, 'infos' => tableau associatif ayant pour clé : 'phone' => numéro de téléphone de
	 * l'enseignant, 'email' => l'adresse email du professeur. 'teacherInitials' => initials (id) du professeur, 'TeacherTimeSlot' => tableau contenant les disponibilités du
	 * professeur, 'hours' => tableau contenant les crénaux horaires.
	 */
	echo div(array('style' => 'max-width: 750px;', 'class' => 'text-center center-block'));
		
		echo heading('Informations concernant '.$teacherName, 2);

		$data = array('<strong>N° de téléphone mobile : </strong>'.$infos['phone'],
					  '<strong>Adresse e-mail : </strong>'.$infos['email']);

		echo ul($data, array('class' => 'text-left'));
		

		echo heading('Son planning', 2);


		echo form_button(array('content' => 'Remettre à blanc', 'class' => 'btn btn-xs active', 'id' => 'white'));
		echo nbs(2);
		echo form_button(array('content' => 'Indisponible', 'class' => 'btn btn-xs btn-inverse', 'id' => 'black'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire à éviter', 'class' => 'btn btn-xs btn-danger', 'id' => 'red'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire de disponible', 'class' => 'btn btn-xs btn-success', 'id' => 'green'));

		echo br(2);
		echo form_open('admin/modifyTeacherPlanning/'.$teacherInitials);

			$TeacherTimeSlot = (empty($TeacherTimeSlot)) ? TRUE : $TeacherTimeSlot;

			echo availabilityTable($hours, $TeacherTimeSlot);

			echo availabilityLevel();

			echo br(2);
			echo form_submit(array('class' => 'btn btn-success', 'value' => 'Sauvegarder'));
			
		echo form_close(); 
echo div_close();
?>
