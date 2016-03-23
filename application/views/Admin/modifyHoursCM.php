<?php
	/**
	 * Vue affichant le tableau permettant de saisir les heures de CM à bloquer dans le planning pour la période
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
     * $hours => Tableau avec les créneaux horaires
	 */
	echo div(array('class' => 'text-center center-block'));
		
        echo '<h3>Nombre d\'heures à saisir : '.$nbHours.'</h3>';
        echo br();

		echo form_button(array('content' => 'Retirer', 'class' => 'btn btn-xs active', 'id' => 'white'));
		echo nbs(2);
		echo form_button(array('content' => 'Sélectionner', 'class' => 'btn btn-xs btn-success', 'id' => 'green'));

        echo br(2);

		echo form_open('admin/modifyHoursCM/'.$promo);

			echo availabilityTable($hours, $status, $cmHours);
			echo form_submit(array('name' => 'validate', 'value' => 'Sauvegarder', 'class' => 'btn btn-success'));
		echo form_close();
	echo div_close();
?>