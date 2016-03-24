<?php
	/**
	 * Classe surchargeant la classe "CI_Loader" uitiliser pour charger des vues, modèles, helper,..;
	 * Cette surcharge prend la place de la classe surchargée lors de son appel dans les contrôleurs
	 * ($this->load).
	 */
	class MY_Loader extends CI_Loader
	{
		/**
		* Affiche une ou plusieurs vues entouré du header, du menu (main_menu) et du footer
		*
		* @param string|string[] $views Nom de la vue ou tableau des vues à afficher
		*
		* @param string[] $vars Tableau associatif contenant les variables à envoyer aux vues.
		*
		* @param string[] $js Tableau associatif contenant le nom des fichiers javascript à rajouter
		*/
		public function template($views, array $vars = array(), array $js = array(), $title=NULL)
		{
			$this->displayTemplate('main', $views, $vars, $js, $title);
		}

		/**
		* Affiche une ou plusieurs vues entouré du header, du menu (admin_menu) et du footer
		*
		* @param string|string[] $views Nom de la vue ou tableau des vues à afficher
		*
		* @param string[] $vars Tableau associatif contenant les variables à envoyer aux vues.
		*
		* @param string[] $js Tableau associatif contenant le nom des fichiers javascript à rajouter
		*/
		public function admin_template($views, array $vars = array(), array $js = array(), $title=NULL)
		{
			$this->displayTemplate('admin', $views, $vars, $js, $title);
		}

		/**
		* Affiche une ou plusieurs vues entouré du header et du footer
		*
		* @param string|string[] $views Nom de la vue ou tableau des vues à afficher
		*
		* @param string[] $vars Tableau associatif contenant les variables à envoyer aux vues.
		*
		* @param string[] $js Tableau associatif contenant le nom des fichiers javascript à rajouter
		*/
		public function templateWithoutMenu($views, array $vars = array(), array $js = array(), $title=NULL)
		{
			$this->displayTemplate(FALSE, $views, $vars, $js, $title);
		}

		/**
		 * Méthode appelée par "template" , "admin_template" et "templateWithoutMenu" pour faire l'affichage.
		 *
		 * Cette méthode fait l'affichage réel des trois autres méthode de la classe. Elle encadre les vues du header et du footer.
		 * Elle peut aussi afficher le menu. Elle transamet le tableau de variables aux vues et peut inclure des fichiers javascript à la fin du fichier (avant le footer)
		 * @param  string $menu  Si ce paramètre vaut 'admin', on affiche le menu administrateur, s'il affiche 'main' on affiche le menu utilisateur. S'il vaut 
		 * autre chose, on n'affiche aucun menu.
		 * 
		 * @param  string|string[] $views Le nom de la vue à afficher ou un tableau contenant les vues à affciher. Elle seront afficher dans l'ordre du tableau.
		 * @param  array  $vars  Paramètre optionnel. Ce tableau, si renseigné, doit contenir les variables à transmettre aux vues. Il doit être remplis pour être
		 * utilisé par la fonction extract.
		 * @param  array  $js    Paramètre optionnel contenant le nom des fichier javascript à inclure en fin de fichier. Ces nom doivent terminer par leur extension. De plus,
		 * les fichiers doivent se situer à la racine du site dans le dossier res/js
		 */
		private function displayTemplate($menu, $views, array $vars = array(), array $js = array(), $title=NULL)
		{
            $data['title'] = "";
            
            if($title != NULL)
                $data['title'] = $title;
            
			$this->view('includes/header.php',$data);

			if($menu === "admin")
				$this->view('includes/admin_menu.php', $vars);
			else if($menu === "main")
				$this->view('includes/menu.php', $vars);
            else
                $this->view('includes/menu_empty.php', $vars);

			if(is_array($views))
				foreach ($views as $view)
					$this->view($view, $vars);
			else
				$this->view($views, $vars);

			$js[] = 'fadeIn.js';
			$jsToLoad = array('js' => $js);

			$this->view('includes/footer.php', $jsToLoad);
		}
	}
?>