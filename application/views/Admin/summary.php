<?php
	/**
	 * Vue affichant le tableau récapitulatif. Ce tableau affiche la liste des enseignant et l'état de la saisie de leur voeux. Permet aussi d'envoyer un mail de rappel et de 
	 * visionner le planning des professeurs ayant enregistré ou validé un planning.
	 *
	 * Cette vue nécessite les variables suivantes : 'teacherWishes' => Tableau de tableaux associatif ayant pour clé : 'state' => état du voeux, 'teacher_id' => id (initiales) du
	 * professeur, 'lastname' => nom du professeur, 'firstname' => prénom du professeur.
	 */
	
	echo div(array('class' => 'text-center center-block'));

		if(!isset($teacherWishes) OR empty($teacherWishes))
			echo 'Erreur !';
		else
		{
			$data = array();
			$headers = array('Nom et Prénom', 'État de la saisie du voeu');
			$whishState = array(-1 => '<p class="text text-danger">Pas encore saisi</p>',
								1 => '<p class="text text-warning">En cours de saisie</p>',
								2 => '<p class="text text-success">Validé</p>');

			echo '<table class="table table-bordered">';
				echo '<tr>';
					foreach ($headers as $header)
						echo "<th>$header</th>";
				echo '</tr>';

				foreach ($teacherWishes as $whish)
				{
					echo '<tr>';
						if($whish['state'] != -1)
							echo '<td>' .anchor('admin/getTeacherPlanning/'.$whish['teacher_id'], $whish['lastname'] .' '. $whish['firstname']);
						else
							echo '<td>' .$whish['lastname'] .' '. $whish['firstname']. '</td>';

						echo '<td>' .$whishState[$whish['state']]. '</td>';
					echo '</tr>';
				}
			echo '</table>';
			
			foreach($teacherWishes as $whish)
			{
				if($whish['state'] == -1 || $whish['state'] == 1)
					array_push($data, $whish['teacher_id']);
			}
		}
		$str = serialize($data);
		$strenc = urlencode($str);
		echo  anchor('admin/reminderMail/'.$strenc, 'Mail de rappel', array('class' => 'btn btn-info'));
		
	echo div_close();
?>
