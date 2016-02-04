<?php
	/**
	 * Vue affichant une liste de périodes
	 * Cette vue nécessite les variables suivantes : $periods => tableau associatif ayant pour clé la valeur retourné par la dropdown
	 * et pour valeur le texte affiché par la dropdown. Voir doc codeigniter 2.2 > helper > form helper
	 */
	echo div(array('style' => 'max-width: 300px', 'class' => 'text-center center-block'));
		echo 'Période : ';
		echo form_dropdown('period', $periods, '', 'id="period" class="form-control"');
	echo div_close();
?>