<?php
	/**
	 * Vue affichant le planning définitifs d'un enseignant.
	 * Cette vues nécessite les variables suivantes : $hours => tableau des tranches horaires ayant pour clé l'id de latranche et pour valeur, 'status' => tableau contenant les status des créneaux horaires (0 si sélectionnable, 1 sinon)
	 * la tranche horaire sous la forme "HHhMM-HHhMM"
	 * Les données du tableau sont remplis via requêtes AJAX.
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
	
	echo div(array('class' => "text-center center-block"));
		echo '<p class="lead">Ces disponibilités sont définitives ! Pour tout problème, veuillez contacter l\'administrateur.</p>';

		echo availabilityTable($hours);
		echo availabilityLevel();
	echo div_close(); 
?>