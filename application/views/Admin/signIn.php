<?php
	/**
	 * Vue affichant le formulaire de connection de l'administrateur
	 */
	
	echo div(array('style' => 'max-width: 500px;', 'class' => 'text-center center-block'));
		echo form_open('admin/signIn', array('class' => 'form-group'));
			echo form_label("Nom d'utilisateur", 'username');
			echo form_input(array('id' => 'username', 'name' => 'username', 'class' => 'form-control'));
            echo br(2);
			echo form_label('Mot de passe', 'password');
			echo form_password(array('id' => 'password', 'name' => 'password', 'class' => 'form-control'));

			echo form_hidden('th', 'value');
			echo br();
			echo form_submit(array('name' => 'test', 'value' => 'Connexion', 'class' => 'btn btn-success'));
		echo form_close();
	echo div_close();
?>

<div class="footer text-center">
    <p>Réalisé avec <span class="glyphicon glyphicon-heart" aria-hidden="true" style="color:#d9534f"></span> par vos étudiants préférés | IUT Lannion | 2016</p>
</div>