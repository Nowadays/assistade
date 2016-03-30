<?php
	/**
	 * Cette vue affiche le formulaire de connexion enseignant.
	 * Elle affiche une liste déroulante avec le prénom + nom de chaque professeurs et un champs "mot de passe"
	 * Cette vue nécessite les variables suivantes : $teachers => tableau de tableau associatif décrivant les enseignants titulaires.
	 * Les clés sont : 'initials' , 'firstname' , 'lastname'
	 */
	echo div(array('style' => 'max-width: 500px;', 'class' => 'text-center center-block'));
		$data = array();
		foreach ($teachers as $t)
			$data[$t['initials']] = $t['firstname'] . ' ' . $t['lastname'];

		echo form_open('main/signIn', array('class' => 'form-group'));
			echo form_label('Sélectionnez votre identifiant', 'login');
			echo nbs(2);
			echo form_dropdown('id', $data, '', 'id="login" class="form-control"');
			echo br(2);
			echo form_label('Mot de passe', 'password');
			echo br(1);
			echo form_password(array('name' => 'password', 'id' => 'password', 'class' => 'form-control'));
			echo br(2);
			echo form_submit(array('value' => 'Connexion', 'class' => 'btn btn-success'));
		echo form_close();
	echo div_close();		
?>

<div class="footer text-center">
    <p>Réalisé avec <span class="glyphicon glyphicon-heart" aria-hidden="true" style="color:#d9534f"></span> par vos étudiants préférés | IUT Lannion | 2016</p>
</div>
