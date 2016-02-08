<?php
	
	if(!function_exists('boolval'))
	{
		/**
		 * Fonction de PHP disponible à partir de la version 5.5
		 *
		 * Cette fonction est définie pour les version antérieur à PHP 5.5
		 *
		 * @param string $str Chaîne à tester
		 *
		 * @return boolean Renvoie VRAI si str est différent de 0, FALSE sinon
		 */
		function boolval($str)
		{
			return ($str !== "0");
		}
	}

	if(!function_exists('availabilityLegend'))
	{
		/**
		 * Fonction affichant la légende couleurs des plannings
		 * 
		 * @return string Renvoie le code HTML à afficher
		 */
		function availabilityLevel()
		{
			return '<span class="label" style="background-color:#2E2E2E">Indisponible</span>
					<span class="label" style="background-color:#CC0000">Horaires à éviter</span>
					<span class="label label-success">Horaires disponibles</span>';
		}
	}

	if(!function_exists('availabilityTable'))
	{
		/**
		 * Fonction retournant le planning de saisie de voeux ou le planning fixe
		 *
		 * Cette fonction retourne le code HTML du planning à afficher
		 * 
		 * @param  array   $hours Tableau des différentes tranches horaires. Ce tableau a pour clé l'id de la tranche horaire et pour valeur la tranche sous la forme "HHhMM-HHhMM"
		 * @param  boolean $form  Paramètre indiquant si le tableau est utilisé en tant que formulaire ou non
		 * 
		 * @return string  Code HTML à afficher
		 */
		function availabilityTable(array $hours, array $status=NULL, $form = FALSE)
		{
            if($status === NULL){
                $status = array();
                for($i = 1; $i <= 5; $i++){
                    for($j = 1; $j <= 8; $j++){
                        if(($i === 4) && ($j > 4))
                            array_push($status,array('status' => 1));
                        else
                            array_push($status,array('status' => 0));
                    }
                }
            }
            
            
			$header =  array('', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');
			$table = '<table class="table table-bordered">';

			$table .= '<tr>';
				foreach ($header as $value)
					$table .= "<th>$value</th>";
			$table .= '</tr>';

			foreach ($hours as $hourId => $hour)
			{    
				$table .= '<tr>';
					$table .= "<td>$hour</td>";
					for($i = 1; $i <= 5; $i++)
					{
						                        
                        if(($status[($i-1)*8+$hourId-1]['status'] == 1)){
							$table .= '<td class="notSelectable" style="background-color: grey"></td>';
                        }
                        else
						{
							$timeslot_id = ($i-1) * count($hours) + $hourId;

							if($form !== FALSE)
							{
								$table .= '<td class="selectable">';

								if((is_array($form)) AND isset($form[$timeslot_id]))
									$table .= form_hidden('timeSlot['.$timeslot_id.']', $form[$timeslot_id]['availability_level']);
								else
									$table .= form_hidden('timeSlot['.$timeslot_id.']', 0);

								$table .= '</td>';
							}
							else
								$table .= '<td id="'.$timeslot_id.'" class="selectable"></td>';
						}
					}
				$table .= '</tr>';
			}

			$table .= '</table>';

			return $table;
		}
	}

	if(!function_exists('div'))
	{
		/**
		 * Fonction renvoyant le code HTML d'ouverture d'un div
		 *
		 * Cette fonction ouvre un div
		 * 
		 * @param  array  $attributes Paramètre optionnel. Tableau associatif ayant pour clé le nom d'un attribut et pour valeur sa valeur
		 * 
		 * @return string             Renvoie le code HTML d'ouverture d'un div avec les attributs contenus dans le tableau passé en paramètre
		 */
		function div(array $attributes = array())
		{
			$attrList = "";

			foreach ($attributes as $attr => $val) {
				$attrList .= $attr.'="'.$val.'" ';
			}

			return "<div $attrList>";
		}
	}
	
	if(!function_exists('div_close'))
	{
		/**
		 * Fonction retournant le code HTML de fermeture d'un div.
		 *
		 * Cette fonction referme un div.
		 * 
		 * @return string Retourne le code HTML de fermeture du div
		 */
		function div_close()
		{
			return '</div>';
		}
	}
	
	if(!function_exists('display_validation_errors'))
	{
		/**
		 * Fonction affichant une erreur.
		 *
		 * Cette fonction affiche une erreur dans un encadré rouge d'un certaine taille.
		 * 
		 * @param  string  $error Texte de l'erreur
		 * @param  integer $width Paramètre optionnel. Largeur en pixel de l'encadré. Par défaut : 750px
		 * 
		 */
		function display_validation_errors($error, $width = 750)
		{
			if(!empty($error))
			{
				echo div(array('class' => 'alert alert-danger center-block', 'style' => "max-width: ${width}px;"));
					echo $error;
				echo div_close();
			}
		}
	}


	if(!function_exists('menuEntry'))
	{
		/**
		* Fonction retournant une liste pour le menu.
		*
		* Cette fonction retourne une liste <ul> avec les class pour être la liste du menu.
		* 
		* @param array[] $elements Tableau contenant d'autre tableaux. Chacun de ses tableau à pour premier élément le 
		* nom de l'entrée du menu. Le second élément est le contrôleur suivis de la méthode (de la forme "contrôleur/méthode") vers lequel l'entrée du menu va pointé.
		*
		* @return string Retourne le code html de la liste ul construit grâce au paramètre.
		*/
		function menuEntry(array $elements)
		{
			if(empty($elements))
				return;

			$data = array();

			foreach ($elements as $line)
			{
				$data[] = anchor($line[1], $line[0]);
			}

			return ul($data, array('class' => 'nav navbar-nav'));
		}
	}

	if(!function_exists('formatDate'))
	{
		/**
		 * Fonction formatant la date donnée
		 *
		 * Cette fonction formate la date passée en argument en une date du format JJ/MM/AAAA
		 * 
		 * @param  string $date La date à formater
		 * 
		 * @return string       La date formatée sous la forme JJ/MM/AAAA
		 */
		function formatDate($date)
		{
			$formater = new DateTime($date);
			return $formater->format('d/m/Y');
		}
	}

	if(!function_exists('formatTimeSlotArray'))
	{
		/**
		* Fonction retounant un tableau de créneau formaté
		*
		* Cette fonction renvoie un tableau avec comme clé un string de la forme "idJour;idCreneauHoraire" et comme valeur la ligne correspondante du tableau
		* de créneaux fournis en entrée.
		*
		* @param array[] Tableau contenant les lignes de la tablea timeSlot correspondant à un professeurs.
		*
		* @return array[] Tableau associatif ayant pour clé un string de la forme "idJour;idCreneauHoraire" et comme valeur la ligne correspondante du tableau fournis
		* en paramètre
		*/
		function formatTimeSlotArray(array $timeSlot)
		{
			$formatedArray = array();

			foreach ($timeSlot as $slot) 
			{
				$day = $slot['slot_day'];
				$hourId = getTimeSlotHourId($slot['start_time'], $slot['end_time']);

				$formatedArray["$day;$hourId"] = $slot;
			}

			return $formatedArray;
		}
	}
?>