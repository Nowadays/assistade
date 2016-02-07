<?php
	/**
	 * Espace privé de l'administrateur. Affiche l'année en cours, la préiode en cours, la date de fin de saisie des voeux pour cette période et l'état de la saisie des voeux.
	 * Et permet de créer une nouvelle année.
	 *
	 * Cette vue nécessite les varaibles suivantes : 'period' => tableau associatif ayant pour clé : 'period_number' => numéro de la période courante, 'end_time' => date de fin
	 * de saisie de voeux pour la période courante, 'state' => état de la saisie des voeux (si c'est ouvert ou fermé). 
	 * 
	 */
	
	$periodNumber = $period['period_number'];
	$date = formatDate($period['end_time']);
	$isPeriodOpen = boolval($period['state']);
	$periodState = ($isPeriodOpen && $period['state'] != -1) ? 'Saisie des voeux <strong>ouverte</strong>' : 'Saisi des voeux <strong>fermée</strong>';

	echo div(array('style' => 'max-width: 500px;', 'class' => 'alert alert-info center-block text-center'));
		echo "<p>Année en cours : <strong>$beginYear - $endYear</strong></p>";
		
		if($period['state'] != -1)
			echo "<p>Période en cours : <strong>P$periodNumber</strong> Date de fin de saisie : <strong>$date</strong></p>";
		else
			echo "<p><strong>Année terminée</strong></p>";
		
		echo "<p>$periodState</p>";

		if(!$isPeriodOpen)
		{
			echo br();
			echo anchor('admin/openWishInput', 'Ouvrir la saisie des voeux', array('class' => "btn btn-primary"));
		}else{
            //Si tous les profs ont validé leurs voeux (fonction à définir)
            //Sinon le bouton est inactif (grisé)
            echo br();
			echo anchor('#', 'Valider la saisie des voeux et lancer la répartition des groupes', array('class' => "btn btn-primary"));
        }

		echo br(2);
		echo anchor('admin/newYear', 'Nouvelle année (reset total)', array('class' => 'btn btn-danger'));

	echo div_close();
?>