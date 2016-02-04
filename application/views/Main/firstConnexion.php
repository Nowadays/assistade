<?php
	/**
	 * Vue demandant les informations lors de la première connexion.
	 * Cette vue nécessite les variables suivantes : $is_responsible => TRUE si responsable de matière FALSE sinon
	 * (optionnel) $responsibleId => id du responsable connecté
	 * (seulement si $responsibleId renseigné) $teacherId => id de l'enseignant auquel on modifie le planning
	 */
	echo div(array('style' => 'max-width: 700px;', 'class' => 'alert alert-info text-center center-block'));

	echo form_open('main/firstConnexion', array('class' => 'form-group'));
		echo '<strong>VOS COORDONNEES HORS IUT</strong>';
		echo br(1);
		echo '&emsp;&emsp;Indiquer où vous joindre <strong>rapidement</strong> si vous n\'êtes pas à l\'IUT, notamment en fin de semaine';
		echo br(2);
		echo '&emsp;&emsp;Numéro de téléphone portable :&emsp;&emsp;';
		echo form_input(array('type' => 'tel', 'id' => 'phoneNumber', 'class' => 'form-control', 'name' => 'phoneNumber', 'value' => set_value('phoneNumber'), 'size' => 50, 'placeholder' => "Numéro de téléphone portable", 'pattern' => '^0[6|7][0-9]{8}$', 'required' => '', 'autofocus' => ''));
		echo br(1);
		echo '&emsp;&emsp;Adresse électronique personnelle :&emsp;&emsp;';
		echo form_input(array('type' => 'email', 'id' => 'email', 'class' => 'form-control', 'name' => 'email', 'value' => set_value('email'), 'size' => 50, 'placeholder' => "Adresse électronique personnelle", 'pattern' => '^[A-Za-z][A-Za-z0-9._-]*@[A-Za-z][A-Za-z0-9._-]*.[a-z]{2,3}$', 'required' => ''));
		echo br(1);		
		if((isset($responsibleId) && $teacherId === $responsibleId) || !$is_responsible)
		{
			echo '&emsp;&emsp;Mot de passe :&emsp;&emsp;';
			echo form_password(array('name' => 'password', 'class' => 'form-control'));
			echo '&emsp;&emsp;Retapez le mot de passe :&emsp;&emsp;';
			echo form_password(array('name' => 'password', 'class' => 'form-control'));
		}
		echo br(1);
		echo form_submit(array('value' => 'Connexion', 'class' => 'btn btn-info'));
	echo form_close();

	echo div_close();
?> 
