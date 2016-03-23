<?php
	/**
	 * Vue affichant le menu de l'administrateur
	 * Cette vue nécessite les variables suivantes : (optionnel) $connected => si TRUE indique que l'administrateur est connecté si non présent ou FALSE
	 * indique que l'administrateur n'est pas connecté.
	 */
?>	

<nav class="navbar navbar-inverse navbar-fixed-top" style="margin-bottom: 200px;">
	<div class="col-md-8 col-md-offset-2">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php 
                $index = (isset($connected) && !$connected) ? '/main' : '/admin'; 
                if(isset($connected) && !$connected)
                    echo '<a class="navbar-brand" href="'.site_url() . $index . '"><img src="'.base_url().'res/img/assistade.min.svg'.'"></img></a>';
                else
                    echo '<a class="navbar-brand" href="'.site_url() . $index . '/privateSpace"><img src="'.base_url().'res/img/assistade.min.svg'.'"></img></a>';
            ?>
		</div>
		
		<div class="navbar-collapse collapse">
			<?php
				if(isset($connected) && !$connected)
					$elems = array(array("Retour à l'accueil", 'main'));
				else
					$elems = array( array('Heures CM', 'admin/getHoursCM/1A'),
									array('Voeux des professeurs', 'admin/summary'),
									array('Gestion professeurs', 'admin/manageTeachers'),
									array('Gestion matières', 'admin/manageSubjects'),
									array('Gestion responsables de matières', 'admin/manageResponsibles'),
									array('Gestion groupes','admin/manageGroup'),
									array('Déconnexion', 'admin/signOut'));

				echo menuEntry($elems);
			?>
		</div>
	</div>
</nav>

<br />
