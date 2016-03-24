<?php
	/**
	 * Vue permettant à l'administrateur de changer son mot de passe
	 */

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading("Nouveau mot de passe");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo form_open('config/initAdminInfo', array('class' => 'form-horizontal'));
					echo div(array('class' => 'form-group'));
						echo form_label('Mot de passe', 'password', array('class' => 'col-sm-4'));
						echo div(array('class' => 'col-sm-8'));
							echo form_password(array('id' => 'password', 'name' => 'password', 'class' => 'form-control', 'required' => 'true'));
						echo div_close();
					echo div_close();

					echo div(array('class' => 'form-group'));
						echo form_label('Retapez le mot de passe', 'passwordBis', array('class' => 'col-sm-4'));
						echo div(array('class' => 'col-sm-8'));
							echo form_password(array('id' => 'passwordBis', 'name' => 'passwordBis', 'class' => 'form-control', 'required' => 'true'));
						echo div_close();

				echo br(2);

				echo form_submit(array('value' => 'Enregistrer', 'class' => 'btn btn-primary'));
			echo form_close();
		echo div_close();
	echo div_close();
?>