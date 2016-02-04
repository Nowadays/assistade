<?php
	/**
	 * Vues affichant le formulaire pour les dates de fin de saisie de voeux pour chaque période.
	 * Cette vues nécessite les variables suivantes : $periodNumber => le nombre de période pour l'année courante
	 */
	display_validation_errors(validation_errors());
	
	echo div(array('class' => 'text-center'));
		echo heading("Configuration d'une nouvelle année", 1);
		echo heading("Quelques paramètres pour bien commencer...", 2);
	echo div_close();

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading('Date de fin de saisie de voeux');
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo form_open('config/initPeriods', array('class' => 'form-horizontal'));					
				for($i = 1; $i <= $periodNumber; $i++)
				{
					echo form_label("Date pour P$i", "p$i", array('class' => 'col-sm-3'));

					echo div(array('class' => 'col-sm-9'));
						echo form_input(array('type' => 'date', 'name' => "period[$i]", 'id' => "p$i", 'class' => 'form-control', 'required' => 'true', 'placeholder' => 'JJ/MM/AAAA', 'pattern' => '^[0-3][0-9]/[0|1][0-9]/[0-9]{4}$'));
					echo div_close();
				}

				echo br(6);

				echo form_submit(array('value' => 'Enregistrer', 'class' => 'btn btn-primary'));
			echo form_close();
		echo div_close();
	echo div_close();
?>