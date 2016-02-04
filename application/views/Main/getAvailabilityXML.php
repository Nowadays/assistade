<?php
	/**
	 * Vue retournant un fichier XML en réponse à une requête AJAX.
	 * Elle renvoie les disponibilités d'un professeurs pour être affiché dans un planning
	 * Cette vue nécessite les variables suivantes : $period => tableau ayant pour clé l'id d'un créneau horaire et pour valeur un tableau contenant 
	 * une ligne "availability_level"
	 */
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<all>';
		foreach ($period as $timeSlotId => $timeSlot)
			echo '<timeslot id="'.$timeSlotId.'" state="'.$timeSlot['availability_level'].'" />';	
	echo '</all>';
?>