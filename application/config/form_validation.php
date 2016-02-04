<?php
	$config = array(
			'admin/firstConnection' => array(
												array('field' => 'password',
													  'label' => 'Mot de passe',
													  'rules' => 'required|alpha_dash'),
												array('field' => 'passwordBis',
													  'label' => 'Deuxième mot de passe',
													  'rules' => 'required|alpha_dash|matches[password]'),
												array('field' => 'currentYear',
													  'label' => 'Année en cours',
													  'rules' => 'required|integer'),
												array('field' => 'periodNumber',
													  'label' => 'Nombre de période',
													  'rules' => 'required|is_natural_no_zero'),
												array('field' => 'period[]',
													  'label' => 'Date de fin de saisie de période',
													  'rules' => 'required')
											),

			'initYear' => array(
												array('field' => 'currentYear',
													  'label' => 'Année en cours',
													  'rules' => 'required|integer'),
												array('field' => 'periodNumber',
													  'label' => 'Nombre de période',
													  'rules' => 'required|is_natural_no_zero')
											),
			'initAdminInfo' => array(			array('field' => 'password',
													  'label' => 'Mot de passe',
													  'rules' => 'required|alpha_dash'),
												array('field' => 'passwordBis',
													  'label' => 'Deuxième mot de passe',
													  'rules' => 'required|alpha_dash|matches[password]')
											),
			'main/signIn' => array(
												array('field' => 'password',
													  'label' => 'Mot de passe',
													  'rules' => 'required')
											)
		);