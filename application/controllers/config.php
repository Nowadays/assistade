<?php
	/**
	* Contrôleur gérant les première conexions (admin + profs) et la configuration de l'application au début d'une année.
	*/
	class Config extends CI_Controller
	{
		private static $states = array('CREATE_DB' => 0, 'INIT_YEAR' => 1, 'INIT_PERIODS' => 2, 'INIT_TEACHER' => 3, 'INIT_SUBJECT' => 4, 
			'INIT_IN_CHARGE' => 5, 'INIT_ADMIN_INFO' => 6, 'INIT_GROUPE' => 7, 'INIT_NB_HOURS' => 8);

		public function __construct()
		{
			parent::__construct();
			$this->load->library('form_validation');
			$this->load->model('config_model');
		}

		/**
		 * Vérifie que c'est bien la première connection de l'administrateur et affiche la page d'explication.
		 *
		 * En cas de réinitialisation de l'application, la variable indiquant si c'est la première connexion de l'administrateur
		 * est positionné à TRUE. Si ce n'est pas la première connexion, redirige vers la page d'index du contrôleur "admin".
		 * Sinon, affiche la page d'explication avant le début de la configuration.
		 */
		public function adminFirstConnexion()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			$this->session->set_userdata('state', self::$states['CREATE_DB']);
			$this->load->templateWithoutMenu('Config/Admin/createDB');
		}

		/**
		 * Créer la BDD.
		 *
		 * Créer la Base de donnée et redirige vers la prochaine page de la configuration.
		 */
		public function createDB()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['CREATE_DB'])
				redirect('config/adminFirstConnexion');

			$this->config_model->createDatabase();

			$this->session->set_userdata('state', self::$states['INIT_YEAR']);

			redirect('config/initYear');
		}

		/**
		 * Demande l'année universitaire en cours et le nombre de périodes la composant.
		 */
		public function initYear()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['INIT_YEAR'])
				redirect('config/createDB');

			if($this->form_validation->run('initYear') !== FALSE)
			{
				$this->session->set_userdata('currentYear', $this->input->post('currentYear'));
				$this->session->set_userdata('periodNumber', $this->input->post('periodNumber'));
				$this->session->set_userdata('state', self::$states['INIT_PERIODS']);

				redirect('config/initPeriods');
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initYear', getdate(), array('adminFirstConnection.js'));
		}

		/**
		 * Demande les de fin de saisie de voeux de chaque période composant l'année universitaire.
		 *
		 * Puis enregistre l'année et les périodes.
		 */
		public function initPeriods()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if(($this->session->userdata('state') !== self::$states['INIT_PERIODS']) OR ($this->session->userdata('currentYear') === FALSE) OR ($this->session->userdata('periodNumber') === FALSE))
				redirect('config/initYear');

			if($this->input->post('period') !== FALSE)
			{
				try
				{
					$this->config_model->initYearAndPeriods($this->session->userdata('currentYear'), $this->input->post('period'));

					$this->session->set_userdata('state', self::$states['INIT_TEACHER']);
					$this->session->unset_userdata('periodNumber');

					redirect('config/initTeacher');
				}
				catch(Exception $e)
				{
					$data = array('title' => 'Erreur',
							  'content' => $e->getMessage(),
							  'state' => 'danger',
							  'static' => FALSE,
							  'button' => array(
										'value' => 'retour',
										'onclick' => 'admin',
										'visible' => true
										)
							  );

					$data['year'] = $this->session->userdata('currentYear');

					$this->session->set_userdata('state', self::$states['INIT_YEAR']);
					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initYear'), $data, array('adminFirstConnection.js'));
				}
			}
			else
			{
				$data = array('periodNumber' => $this->session->userdata('periodNumber'));
				$this->load->templateWithoutMenu('Config/Admin/initPeriods', $data);
			}

		}

		/**
		 * Affiche la page d'import des enseignants.
		 *
		 * Affiche la page permettant de télécharger un squellettes CSV permettant de remplir la base de données puis enregistre
		 * les enseignants s'il n'y a pas d'erreur. Puis affiche la page affichant le mot de passe de chaque enseignant.
		 */
		public function initTeacher()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['INIT_TEACHER'])
				redirect('config/initPeriods');

			$this->load->library('upload');
			$data = array('name' => 'professeurs', 'table' => 'teacher', 'src' => 'initTeacher');

			if(isset($_FILES['csv']) && $_FILES['csv']['size'] > 0)
			{
				try
				{
					$passwords = $this->config_model->insertFromCSV('teacher', $_FILES['csv']);

					$this->session->set_userdata('state', self::$states['INIT_SUBJECT']);
					$this->load->templateWithoutMenu('Config/Admin/teacherPasswords', array('passwords' => $passwords));
					unset($_FILES);
					redirect('config/initSubject');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initCSV'), $data);
				}
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initCSV', $data);

		}



		/**
		 * Affiche la page d'import des matières
		 *
		 * Affiche la page permettant d'importer les matière via un fichier CSV.
		 * Le squelette du fichier est proposé au téléchargement.
		 * Les données sont ensuites enregistrées en base de données.
		 */
		public function initSubject()
		{
            if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['INIT_SUBJECT'])
				redirect('config/initPeriods');

            $this->load->library('upload');
			$data = array('name' => 'matières', 'table' => 'subject', 'src' => 'initSubject');
			
			if(isset($_FILES['csv']) && $_FILES['csv']['size'] > 0)
			{
				try
				{				    
                    $this->config_model->insertFromCSV('subject', $_FILES['csv']);
					$this->session->set_userdata('state', self::$states['INIT_IN_CHARGE']);
                    redirect('config/initInCharge');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initCSV'), $data);
				}
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initCSV', $data);
		}

		/**
		 * Affiche la page d'import des groupes d'étudiants
		 *
         * Affiche la page permettant de télécharger un squellettes CSV permettant de remplir la base de données puis enregistre
		 * les groupes s'il n'y a pas d'erreur. Puis affiche la page affichant le mot de passe de chaque enseignant.
		 */
		public function initInCharge()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['INIT_IN_CHARGE'])
				redirect('config/initPeriods');

			$data = array();
			$data['teachers'] = $this->config_model->getPermanentWorkers();
			$data['subjects'] = $this->config_model->getSubjects();

            if($this->input->post() !== false){
                $hoursCM = $this->input->post('hoursCM');
                $hoursTD = $this->input->post('hoursTD');
                $hoursTP = $this->input->post('hoursTP');

                try{
                    $this->config_model->saveSubjectHours('cm',$hoursCM);
                    $this->config_model->saveSubjectHours('td',$hoursTD);
                    $this->config_model->saveSubjectHours('tp',$hoursTP);
                }catch(Exception $e){
                    $data['title'] = 'Erreur';
                    $data['content'] = $e->getMessage();
                    $data['state'] = 'danger';
                    $data['button'] = array('visible' => FALSE);

                    $this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initInCharge'), $data);
                }   
            }
            
			if ($this->input->post('manageResp') !== false)
			{
				try
				{
					$this->config_model->saveManagerSubject($this->input->post('manageResp'));

					$this->session->set_userdata('state', self::$states['INIT_GROUPE']);
					redirect('config/initGroupe');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initInCharge'), $data);
				}
				
				
				redirect('admin/manageResponsibles');
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initInCharge', $data);
		}

        /**
		 * Affiche la page permettant d'assigner aux matières un responsables
		 *
		 * Affiche la page pour désigner un responsable par chaque matière puis enregistre les données en base de donnée
		 */
		function initGroupe(){
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');

			if($this->session->userdata('state') !== self::$states['INIT_GROUPE'])
				redirect('config/initInCharge');

			$this->load->library('upload');
			$data = array('name' => 'groupes', 'table' => 'group_tp', 'src' => 'initGroupe');

			if(isset($_FILES['csv']) && $_FILES['csv']['size'] > 0)
			{
				try
				{
					$this->config_model->insertFromCSV('group_tp', $_FILES['csv']);
					$this->session->set_userdata('state', self::$states['INIT_ADMIN_INFO']);
					redirect('config/initAdminInfo');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initCSV'), $data);
				}
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initCSV', $data);
		}


		/**
		 * Affiche la page pour changer le mot de passe administrateur
		 *
		 * Affiche le formulaire de changement de mot de passe puis enregistre en base de donnée
		 */
		public function initAdminInfo()
		{
			if($this->session->userdata('adminFirstConnexion') === FALSE)
				redirect('admin');
			
			if($this->session->userdata('state') !== self::$states['INIT_ADMIN_INFO'])
				redirect('config/initPeriods');

			if($this->form_validation->run('initAdminInfo') !== FALSE)
			{
				$this->config_model->insertAdminPassword($this->input->post('password'));

				$this->session->set_userdata('state',self::$states['INIT_NB_HOURS']);
				$this->session->unset_userdata('adminFirstConnection');

				redirect('admin/signIn');
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initAdminInfo');
		}

		public function initNbHours(){
			if($this->session->userdata('state') == self::$states['INIT_NB_HOURS']){
				redirect('Admin/privateSpace');
			}
			$this->load->library('upload');
			$data = array('name' => 'les heures', 'table' => 'mini_nb_hours', 'src' => 'initNbHours');
			if(isset($_FILES['csv']) && $_FILES['csv']['size'] > 0)
			{
				try
				{
					$this->config_model->insertFromCSV('mini_nb_hours', $_FILES['csv']);
					$this->session->unset_userdata('state');
					redirect('admin/openWishInput');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->templateWithoutMenu(array('Main/message', 'Config/Admin/initCSV'), $data);
				}
			}
			else
				$this->load->templateWithoutMenu('Config/Admin/initCSV', $data);

		}

		/**
		 * Méthode fait télécharger le squelette d'un fichier CSV à l'utilisateur pour remplir une table en BDD
		 *
		 * Cette méthode prend un paramètre le nom d'une table et retroune un fichier CSV ayant comme première ligne
		 * le nom de chaque colonne de la table. Cette méthode ne fonctionne que pour les tables se trouvant dans le tableau
		 * csvTables du model "config_model" !
		 * @param  string $tableName Nom de la table concernée
		 */
		public function downloadSkeleton($tableName)
		{
			try
			{
				$data = $this->config_model->getFields($tableName);
				
				$data = implode(',', $data);
				
				$fileName = "";

				if($tableName === "teacher")
					$fileName = "Professeur";
				else if($tableName === "subject")
					$fileName = "Matiere";
				else if($tableName === "student_group_tp"){
					$fileName = "Groupe";
				}else if($tableName === "mini_nb_hours"){
					$fileName = "NbHeure";
				}
				
				$fileName .= ".csv";

				$this->load->helper('download');
				
				force_download($fileName, $data);
			}
			catch(Exception $e)
			{
				$data = array('title' => 'Erreur lors de la récupération des données !',
							  'content' => $e->getMessage(),
							  'state' => 'danger',
							  'static' => TRUE,
							  'button' => array(
										'value' => 'retour',
										'onclick' => 'config/initTeacher',
										'visible' => true
										)
							  );
				
				$this->load->templateWithoutMenu('Main/message', $data);
			}
		}


	}
?>