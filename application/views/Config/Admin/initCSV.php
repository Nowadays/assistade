<?php
	/**
	 * Vue génériques pour l'import via CSV. Cette vues attend le nom ($name) des éléments à importer ("Matières", "Professeurs"),
	 * le nom de la méthode à qui renvoyer les données ($src) et le nom en base de donnée de la table qui va recevoir les données ($table)
	 */
	echo div(array('class' => 'text-center'));
		echo heading("Configuration d'une nouvelle année", 1);
		echo heading("Quelques paramètres pour bien commencer...", 2);
	echo div_close();

	echo div(array('style' => 'max-width: 750px;', 'class' => 'panel panel-info text-center center-block'));
		echo div(array('class' => 'panel-heading'));
			echo heading("Insertion des $name");
		echo div_close();

		echo div(array('class' => 'panel-body'));
			echo form_open_multipart("config/$src", array("class" => "centered form-horizontal"));
				echo div(array('class' => 'form-group'));
					echo form_label('Squellette CSV', 'link', array('class' => 'col-sm-3'));
					echo anchor("config/downloadSkeleton/$table", 'Télécharger le squelette', array('id' => 'link', 'class' => 'btn btn-info col-sm-6'));
				echo div_close();

				echo div(array('class' => 'form-group'));
					echo form_label('Fichier au format CSV', 'csv');
					echo form_upload(array('id' => 'csv', 'name' => 'csv', 'style' => 'margin: auto;'));
				echo div_close();

				echo br(2);

				echo form_submit(array('class' => 'btn btn-info'), 'Envoyer !');
		echo form_close();
		echo div_close();
	echo div_close();
?>