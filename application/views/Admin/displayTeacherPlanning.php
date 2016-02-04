<?php
	/**
	 * Vue affichant le plnanning d'un professeur.
	 *
	 * Cette vue affiche le planning d'un professeur et si le planning a été validé, active le bouton 'Modifier le planning'
	 * Cette vue nécessite les variables suivantes : 'teacherName' => nom du professeur, 'infos' => tableau associatif ayant pour clé :
	 * 'phone' => numéro de téléphone du professeur, 'email' => adresse email du professeur. La variable 'hours' => tableau contenant les crénaux horaires,
	 * 'teacherTimeSlot' => tableau contenant les disponibilités du professeur et 'whishState' => état du voeux du professeur.
	 */
	
	echo div(array('style' => 'max-width: 750px;', 'class' => 'text-center center-block'));
		
		echo heading('Informations concernant '.$teacherName, 2);

		$data = array('<strong>N° de téléphone mobile : </strong>'.$infos['phone'],
					  '<strong>Adresse e-mail : </strong>'.$infos['email']);

		echo ul($data, array('class' => 'text-left'));
		

		echo heading('Son planning', 2);
		
		echo availabilityTable($hours, $TeacherTimeSlot);		
		
		echo availabilityLevel();

		echo br(2);

		if($wishState == 2)
			echo anchor("admin/modifyTeacherPlanning/$teacherInitials", 'Modifier le planning', array('class' => 'btn btn-primary'));
		else
		{
			echo div(array('style' => 'display: inline-block', 'data-toogle' => 'tooltip', 'data-placement' => 'bottom', 'data-original-title' => 'Impossible de modifier le planning tant qu\'il n\'a pas été validé par le prof'));
				echo anchor('#', 'Modifier le planning', array('class' => 'btn btn-primary disabled'));
			echo div_close();
		}
echo div_close();
?>