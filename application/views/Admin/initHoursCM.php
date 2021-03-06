<?php
	/**
	 * Vue affichant le tableau permettant de saisir les heures de CM à bloquer dans le planning pour la période
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
     * $hours => Tableau avec les créneaux horaires
	 */
	echo div(array('class' => 'text-center center-block'));
		echo heading('Saisie des heures de CM de P'.$periodNumber.' pour les '.$promo, 2);
		echo br();
        echo '<p>Nombre d\'heures à saisir : '.$nbHours.'</p>';
		
		echo form_button(array('content' => 'Retirer', 'class' => 'btn btn-xs active', 'id' => 'white'));
		echo nbs(2);
		echo form_button(array('content' => 'Sélectionner', 'class' => 'btn btn-xs btn-success', 'id' => 'green'));

		echo br(2);
		echo form_open('admin/openWishInput/'.$promo);

			echo availabilityTable($hours, $status, $cmHours);
			echo form_submit(array('name' => 'validate', 'value' => 'Valider', 'class' => 'btn btn-success'));
		echo form_close();
	echo div_close();
?>