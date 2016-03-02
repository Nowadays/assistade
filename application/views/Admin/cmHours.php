<?php
	/**
	 * Vue affichant les créneaux réservés aux CM de la période actuelle pour une promo donnée
	 * Cette vues nécessite les variables suivantes : $hours => tableau des tranches horaires ayant pour clé l'id de latranche et pour valeur, 'status' => tableau contenant les status des créneaux horaires (0 si sélectionnable, 1 sinon)
	 * la tranche horaire sous la forme "HHhMM-HHhMM"
	 * Les données du tableau sont remplis via requêtes AJAX.
     * $cmHours => Heures de cm de la période pour la promo correspondant
     * $periodNumber => Numéro de la période courante
	 */
	echo div(array('class' => 'modal fade', 'id' => 'loading', 'tabindex' => -5, 'aria-hidden' => 'true'));
		echo div(array('class' => 'modal-dialog'));
			echo div(array('class' => 'modal-content'));
				echo div(array('class' => 'modal-body center-block text-center'));
					echo '<p>Chargement</p>';
					echo img('res/img/loading.gif');
				echo div_close();
			echo div_close();
		echo div_close();
	echo div_close();
	
	echo div(array('style' => 'max-width: 750px;', 'class' => "text-center center-block"));
		echo heading('Créneaux réservés aux CM de P'.$periodNumber);
        echo br(2);

		echo availabilityTable($hours);
		echo availabilityLevel();
	echo div_close(); 
?>