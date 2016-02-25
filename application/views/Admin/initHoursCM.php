<?php
	/**
	 * Vue affichant le tableau permettant de saisir les heures de CM à bloquer dan sle planning pour la période
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
     * $hours => Tableau avec les créneaux horaires
	 */
	echo div(array('style' => 'max-width: 800px;', 'class' => 'text-center center-block'));
		echo heading('Saisie des heures de CM pour P'.$periodNumber, 2);
		echo br();
		
		echo form_button(array('content' => 'Remettre à blanc', 'class' => 'btn btn-xs active', 'id' => 'white'));
		echo nbs(2);
		echo form_button(array('content' => 'Indisponible', 'class' => 'btn btn-xs btn-inverse', 'id' => 'black'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire à éviter', 'class' => 'btn btn-xs btn-danger', 'id' => 'red'));
		echo nbs(2);
		echo form_button(array('content' => 'Horaire de disponible', 'class' => 'btn btn-xs btn-success', 'id' => 'green'));

		echo br(2);
		echo form_open('admin/openWishInput');

			echo availabilityTable($hours, null, true);
			echo form_submit(array('name' => 'validate', 'value' => 'Ouvrir la saisie des voeux', 'class' => 'btn btn-success'));
		echo form_close();
	echo div_close();
?>