<?php
	/**
	 * Cette vue affiche un message à l'utilisateur.
	 * Cette vue nécessite que le message soit dans la variable $content
	 *
	 * Pour personnalisé le message, vous pouvez fournir les variables suivantes :
	 * $state => Type de message (danger, warning, info, success, primary, default ; voir documentation bootstrap) par défaut : info
	 * $title => Titre du message, par défaut "Information"
	 * $static => Booléen indiquant si le message doit disparaitre (FALSE) ou rester (TRUE), par défaut TRUE
	 * $button => Tableau permettant de personnaliser le bouton. Clé possible :
	 * 		- 'value' => Texte afficher dans le bouton, par défaut "Continuer"
	 * 		- 'onclick' => Lien vers lequel pointe le bouton, par défaut base_url()
	 * 		- 'visible' => Booléen indiquant si le bouton doit être visible ou non, par défaut TRUE
	 *
	 * Attention, le bouton n'est affiché seulement si 'static' vaut TRUE !
	 */
	echo div(array('style' => 'max-width: 500px;', 'class' => 'text-center center-block'));
		if( !isset($state) )
		{
			$state= 'info';
		}
		else
			if($state !== 'success' && $state !== 'warning' && $state !== 'danger')
				$state = 'info';
		
		if(!isset($title) )
			$title='Information';
		
		if(!isset($static) )
			$static = true;
		else
			if($static !== true && $static !== false)
				$static = true;
		
		if(!isset($button))
		{
			$button['value'] = 'Continuer';
			$button['onclick'] = base_url();
			$button['visible'] = true;
		}
		else
		{
			if(!isset($button['value']))
				$button['value'] = 'Continuer';
			
			if(!isset($button['onclick']))
				$button['onclick'] = base_url();
			
			if(!isset($button['visible']))
				$button['visible'] = true;
		}
	?>
	<div class="alert alert-<?php echo($state); if(!$static) { echo ' fade in'; } ?>">
		<b><?php echo $title." </b> ".br().$content ?>
	</div>
	<?php if($static === true && $button['visible'] === true)
	{ 
		echo anchor($button['onclick'], $button['value'], array("class" => "btn btn-info"));
	} 
	echo div_close();
?>