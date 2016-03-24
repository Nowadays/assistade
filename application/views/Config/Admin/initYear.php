<?php
	/**
	 * Cette vues affiche le fomulaire pour indiquer l'année universitaire en cours et le nombre de période la composant
	 * Cette vues nécessite les variables suivantes : $year => année en actuelle
	 */
	echo validation_errors();

	echo div(array('class' => 'panel panel-info text-center center-block'));

		echo div(array('class' => 'panel-heading'));
			echo heading("Information sur l'année scolaire");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo form_open('config/initYear', array('class' => 'form-horizontal'));
				echo div(array('class' => 'form-group'));
					echo form_label('Année en cours', 'currentYear', array('class' => 'col-sm-3'));
					echo div(array('class' => 'col-sm-7'));
						echo form_input(array('type' => 'number', 'value' => $year, 'id' => 'currentYear', 'name' => 'currentYear', 'class' => 'form-control', 'required' => 'true'));
					echo div_close();
					echo div(array('class' => 'col-sm-2'));
						echo '<p id="nextYear" class="form-control-static text-left"> </p>';
					echo div_close();
				echo div_close();

				echo div(array('class' => 'form-group'));
					echo form_label('Nombre de périodes', 'periodNumber', array('class' => 'col-sm-3'));
					echo div(array('class' => 'col-sm-9'));
						echo form_input(array('type' => 'number', 'min' => 1, 'value' => 1, 'id' => 'periodNumber', 'name' => 'periodNumber', 'class' => 'form-control', 'required' => 'true'));
					echo div_close();

				echo br(3);

				echo form_submit(array('value' => 'Enregistrer', 'class' => 'btn btn-success'));
			echo form_close();
		echo div_close();
	echo div_close();
?>