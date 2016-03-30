<?php
	/**
	 * Cette vue affiche le menu visiteur, utilisateur et responsable de matière.
	 * Cette vue nécessite les variables suivantes : (optionnel) $username => le prénom + nom de l'utilisateur
	 * (seulement si nom d'utilisateur renseigné, optionnel) $wishState => état de la saisie des voeux (0 => pas saisie, 1 => données enregistré, 2 => données validé)
	 * (seulement si nom d'utilisateur renseigné, optionnel) $is_responsible => indique si l'utilisateur est un responsable de matière ou non
	 * (seulement si is_responsible est renseigné) $responsibleId => id du responsable
	 * (seulement si is_responsible est renseigné) $teacherId => id du teacher à qui modifier le planning (peut être l'id du responsable ou celui d'un vacataire dont le reponsable s'occupe)
	 */
?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="menu-admin">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
			
			<a class="navbar-brand" href="<?php echo site_url().'/main/privateSpace'; ?>"><?php echo '<img src="'.base_url().'res/img/assistade.svg'.'"></img></a>' ?>
		</div>
		
		<div class="collapse navbar-collapse">
			<?php
				$elems = array();

				if(isset($username) && !empty($username))
				{
					$elems[] = array($username, 'main/privateSpace');
                    
                    $elems[] = array('Créneaux CM', 'main/getHoursCM/1A');
                    
					if(isset($wishState) && $wishState !== 2)
						$elems[] = array('Renseigner mes disponibilités', 'main/setAvailability');
					else
						$elems[] = array('Mes disponibilités', 'main/displayMyAvailability');

					$elems[] = array('Mes anciens Planning', 'main/displayOlderPlanning');
					
<<<<<<< HEAD
                    $elems[] = array('Voir mes modules', 'main/displaySubjects');

                    $elems[] = array('kalash', 'main/SelectGroup');
=======
                    $elems[] = array('Mes modules', 'main/displaySubjects');
>>>>>>> origin/master
                    
					if(isset($is_responsible) && $is_responsible === true)
					{
						if($responsibleId === $teacherId)
							$elems[] = array('Gestion des vacataires', 'main/selectTemporaryWorkers');
						else
							$elems[] = array('Déconnexion du vacataire', 'main/signOutFromTemporaryWorker');
					}
						
					$elems[] = array('Déconnexion', 'main/signOut');
				}
				else
				{
					$elems[] = array('Connexion', 'main/signIn');
					$elems[] = array('Espace Administrateur', 'admin/signIn');
				}

				echo menuEntry($elems);
			?>
		</div>
	</div>
</nav>