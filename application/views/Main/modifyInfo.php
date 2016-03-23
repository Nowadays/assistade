<?php
	/**
	 * Vue présentant le formulaire pour modifier les informations personnelles
	 *
	 * Cette vue nécessite les variables suivantes : $infos => tableau ayant pour clé et valeur 'phone' (numéro de téléphone renseigné au début) et 'email' (email renseigné au début)
	 */
	echo div(array('class' => 'alert alert-info text-center center-block'));
	
		echo form_open('main/modifyInfo', array('class' => 'form-group'));
			echo "<h3 class = text-left>Modifier vos informations personnelles :</h3>";
			echo "<ul class = text-left>";
			echo "<li><strong>N° de téléphone mobile : </strong></li>";
		echo form_input(array('type' => 'tel', 'id' => 'phoneNumber', 'class' => 'form-control', 'name' => 'phoneNumber', 'value' => set_value('telPort'), 'size' => 50, 'placeholder' => $infos['phone'], 'pattern' => '^0[6|7][0-9]{8}$', 'autofocus' => ''));
			echo br(1);
            echo "<li><strong>Adresse e-mail : </strong></li>";
		echo form_input(array('type' => 'email', 'id' => 'email', 'class' => 'form-control', 'name' => 'email', 'value' => set_value('mailPerso'), 'size' => 50, 'placeholder' => $infos['email'], 'pattern' => '^[A-Za-z][A-Za-z0-9._-]*@[A-Za-z][A-Za-z0-9._-]*.[a-z]{2,3}', 'autofocus' => ''));
			echo "</ul>";
        echo br(1);
		echo form_submit(array('value' => 'Valider', 'class' => 'btn btn-success'));
		echo nbs(4);
		echo anchor('main/privateSpace', 'Annuler', array('class' => 'btn btn-danger'));
		echo form_close();
	echo div_close();
?> 
