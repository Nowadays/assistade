<?php
	/**
	 * Contrôleur gérant l'administrateur
	 */
	class Admin extends CI_Controller
	{
		/**
		 * Constructeur chargeant le modèle "admin_model", les library "form_validation" et "email", et le helper "download"
		 */
		public function __construct()
		{
			parent::__construct();
			$this->load->model('admin_model');
			$this->load->library('form_validation');
			$this->load->library('email');
			$this->load->helper('download');
		}

		/**
		 * Méthode affichant la page par défaut du contrôleur
		 *
		 * Cette méthode affiche l'accueil avec le menu d'administrateur
		 */
		public function index()
		{
			$this->requireConnected();

			$view = array('Main/home');
			$data = array();
			$this->addMessage($view,$data);

			$this->load->admin_template($view, $data);
		}

		/**
		* Méthode vérifiant si l'administrateur est connecté, et permettant sa connexion
		*
		* Cette méthode verifie :
		* Si l'administrateur est connecté.
		* Sinon s'il n'est pas connecté, elle vérifie s'il y a des données envoyées par le formulaire de connexion "signIn"
		*	Si les données ('username' et 'password') sont invalides, elle affiche un message d'erreur.
		*	Sinon l'administrateur est connecté, puis
		*		Si c'est sa première connexion, l'administrateur est redirigé vers la méthode "firstConnection"
		*		Sinon, l'administrateur est redirigé vers sa méthode principale "summary"
		* Sinon elle charge le formulaire de connexion administrateur.
		**/
		public function signIn()
		{
			if($this->isAdminConnected())
				redirect('admin/summary');
			else if(($this->input->post('username') !== FALSE) && ($this->input->post('password') !== FALSE))
			{
				if(!$this->admin_model->isAdmin($this->input->post('username'), $this->input->post('password')))
				{
					$views = array('Main/message', 'Admin/signIn');

					$data = array('title' => 'Erreur de connexion',
								  'content' => 'Le couple identifiant/mot de passe est incorrect !',
								  'state' => 'danger',
								  'static' => false,
								  'button' => array(
										'value' => 'retour',
										'onclick' => 'admin',
										'visible' => true
										),
								  'connected' => $this->isAdminConnected());
					$this->load->admin_template($views, $data);
				}
				else
				{
					$this->session->set_userdata('admin', TRUE);
					$this->session->set_userdata('username', 'Administrateur');

					if($this->admin_model->isFirstConnection())
					{
						$this->session->set_userdata('adminFirstConnexion', TRUE);
						redirect('config/adminFirstConnexion');
					}
					else
						redirect('admin/privateSpace');
				}
			}
			else
				$this->load->admin_template('Admin/signIn', array('connected' => $this->isAdminConnected()));
		}

		
		/**
		* Méthode déconnectant l'administrateur
		*
		* Cette méthode vide la variable de session contenant les identifiants de l'administrateur
		* puis redirige vers la méthode "index"
		**/
		public function signOut()
		{
			$this->session->unset_userdata('admin');
			$this->session->unset_userdata('username');

			$this->session->sess_destroy();

			redirect('main/index');
		}

		/**
		* Méthode affichant l'espace personnel de l'administrateur
		*
		* Cette page affiche l'année et la période en cours et la date de fin de saisie de voeux de la période.
		* De plus, si la saisie des voeux n'est pas ouverte, un bouton permet l'ouverture de la saisie.
		* Enfin, un bouton permettant de recommencer une année (en effacant toutes les données) est disponible
 		**/
		public function privateSpace()
		{
			//$years = $this->admin_model->getCurrentYear();

			/*$data = array();
			$data['beginYear'] = $years['beginYear'];
			$data['endYear'] = $years['endYear'];
			$data['period'] = $this->admin_model->getCurrentPeriod();*/
			
			$data = $this->admin_model->getCurrentYear();
			$data['period'] = $this->admin_model->getCurrentPeriod();
			$data['res']=$this->admin_model->getState($data['period']);

			$this->load->admin_template('Admin/privateSpace', $data);
		}

		/**
		 * Méthode permettant de passer à une nouvelle année scolaire.
		 *
		 * Pour le moment, cette méthode ne fait qu'indiquer que c'est la première conenction de l'administrateur pour lancer la première configuration
		 * de l'application qui (ré)initialise tout.
		 */
		public function newYear()
		{
			$this->load->model('config_model');
			$r='DROP SCHEMA ade CASCADE';
			$this->db->query($r);
			$this->config_model->createDatabase();
			$this->session->set_userdata('adminFirstConnexion', TRUE);
			redirect('config/adminFirstConnexion');
		}

		/**
		* Méthode autorisant la saisie des voeux et enregistrant les heures de CM pour les deux promos
		*
		* Cette méthode ouvre la saisie des voueux pour la période actuelle en bloquant les heures de CM pour les enseignants
		**/
		public function openWishInput($promo = NULL)
		{
            if($this->input->post('timeSlot') !== FALSE && $promo != NULL){  
                $this->admin_model->insertHoursCM($this->input->post('timeSlot'),$promo);
                
                $nb = count(array_filter($this->input->post('timeSlot'), function($k){return $k == 3;}));
                
                if($nb < $this->admin_model->getNbHoursCM($promo)){
                    $message['state']= 'danger';
                    $message['title']= 'Attention';
                    $message['content']= 'Vous n\'avez pas renseigné suffisemment de créneaux';
                    $message['static']= FALSE;
                                        
                    $this->session->set_userdata('message', $message);                    
                    redirect('admin/initHoursCM/'.$promo);
                }
                if($nb > $this->admin_model->getNbHoursCM($promo)){
                    $message['state']= 'danger';
                    $message['title']= 'Attention';
                    $message['content']= 'Vous avez renseigné trop de créneaux';
                    $message['static']= FALSE;
                                        
                    $this->session->set_userdata('message', $message);
                    redirect('admin/initHoursCM/'.$promo);
                }
                                      
                if($promo == '1A'){
                    redirect('admin/initHoursCM/2A');
                }else{
                    redirect('admin/initNbGroups');
                }
            }

            $this->admin_model->openPeriodWishInput();
            
			$data = array('title' => 'Succès !',
						  'content' => 'La saisie des voeux a bien été ouverte !',
						  'state' => 'success',
						  'static' => TRUE,
						  'button' => array(
										'value' => 'retour',
										'onclick' => '/admin',
										'visible' => true
										)
						  );

			$this->load->admin_template('Main/message', $data);
		}

		/**
		 * Méthode affichant la liste des professeurs et des vacataires ainsi que l'état de la saisie des voeux.
		 *
		 * Cette méthode donne les liste des professeurs et des vacataires ainsi que l'état de leur saisie mais permet d'accèder
		 * au planning de ceux qui ont déjà enregistré leur planning ou ceux qui ont validé définitvement.
		 */
		public function summary()
		{
			$this->requireConnected();

			$data['teacherWishes'] = $this->admin_model->getTeachersWishes();

			$this->load->admin_template('Admin/summary', $data);
		}
        
        /**
		 * Méthode affichant le planning avec les créneaux horaires des CM de la période.
		 *
		 */
		public function getHoursCM($promo)
		{
			$this->requireConnected();
            
            $view = array('Admin/getHoursCM');
			$data = array();
			$this->addMessage($view,$data);

			$data['hours'] = $this->admin_model->getHours();
            $data['status'] = $this->admin_model->getHoursStatus();
            $data['periodNumber'] = $this->admin_model->getCurrentPeriod()['period_number'];
            $data['cmHours'] = $this->admin_model->getHoursCM($promo);
            $data['promo'] = $promo;
            
			$this->load->admin_template($view, $data, array('getAvailability.js'));
		}
        
        /**
		 * Méthode permettant la modification des créneaux des CM d'une promo
		 *
		 * Cette méthode affiche les CM de la promo donnée et permet à l'administrateur de les modifier
		 * 
		 * @param  string $promo Identifiant de la promp à afficher
		 */
		public function modifyHoursCM($promo)
		{
			$this->requireConnected();
                
            if($this->input->post('timeSlot') !== FALSE)
			{    
                $message['state']= 'success';
                $message['title']= 'Succès';
                $message['content']= 'Vos disponibilités ont bien été enregistrées';
                $message['static']= FALSE;
                
                $redirect = 'admin/getHoursCM/';
                
                $nb = count(array_filter($this->input->post('timeSlot'), function($k){return $k == 3;}));
                
                if($nb < $this->admin_model->getNbHoursCM($promo)){
                    $message['state']= 'danger';
                    $message['title']= 'Attention';
                    $message['content']= 'Vous n\'avez pas renseigné suffisemment de créneaux';
                    $message['static']= FALSE;
                    
                    $redirect = 'admin/modifyHoursCM/';
                }
                if($nb > $this->admin_model->getNbHoursCM($promo)){
                    $message['state']= 'danger';
                    $message['title']= 'Attention';
                    $message['content']= 'Vous avez renseigné trop de créneaux';
                    $message['static']= FALSE;

                    $redirect = 'admin/modifyHoursCM/';
                }
                    
                $this->admin_model->insertHoursCM($this->input->post('timeSlot'), $promo);

                $this->session->set_userdata('message', $message);
                redirect($redirect.$promo);
            }
                
			try
			{		
                $view = array('Admin/modifyHoursCM');
                $data = array();
			    $this->addMessage($view,$data);
                
				$period = $this->admin_model->getCurrentPeriod();
			
                $this->admin_model->setBlockedHoursCM($promo);
                
				$data['hours'] = $this->admin_model->getHours();
                $data['status'] = $this->admin_model->getHoursStatus();
                $data['periodNumber'] = $period['period_id'];
                $data['cmHours'] = $this->admin_model->getHoursCM($promo);  
                $data['promo'] = $promo;
                $data['nbHours'] = $this->admin_model->getNbHoursCM($promo);
                
				$this->load->admin_template($view, $data, array('getAvailability.js'));
                
                $this->admin_model->unsetBlockedHours();
			}
			catch(Exception $e)
			{
				$data = array('title' => 'Erreur',
							  'content' => $e->getMessage(),
							  'state' => 'danger',
							  'button' => array('visible' => FALSE),
							  'button' => array(
										'value' => 'retour',
										'onclick' => 'admin/getHoursCM/'.$promo,
										'visible' => true
										)
							  );

				$this->load->admin_template('Main/message', $data);
			}
		}
        
        /**
		 * Méthode permettant dse saisir les créneaux réservés aux heures de CM d'une promo
		 *
		 * Cette méthode affiche un planning vierge permettant à l'administrateur de saisir les heures de CM 
         * pour les rendre indisponnibles pour les enseignants
		 */
		public function initHoursCM($promo)
		{
			$this->requireConnected();
            
            $view = array('Admin/initHoursCM');
            $data = array();
            $this->addMessage($view,$data);

            if($promo === '1A')
                $this->admin_model->deleteHoursCM();
            
            $this->admin_model->setBlockedHoursCM($promo);
            
			$data['hours'] = $this->admin_model->getHours();
            $data['status'] = $this->admin_model->getHoursStatus();
            $data['periodNumber'] = $this->admin_model->getCurrentPeriod()['period_number'];
            $data['promo'] = $promo;
            $data['nbHours'] = $this->admin_model->getNbHoursCM($promo);
            $data['cmHours'] = $this->admin_model->getHoursCM($promo);
            
			$this->load->admin_template($view, $data, array('getAvailability.js'));

            $this->admin_model->unsetBlockedHours();
        }
        
        /**
		 * Méthode permettant d'insérer le nombre de groupe pour chaque matière pour chaque enseignant poiur une période donnée
		 *
		 * Cette méthode affiche une page permettant de charger un fichier CSV
		 */
		public function initNbGroups()
		{
			$this->requireConnected();
            $this->load->model('config_model');
            
            $view = array('Admin/initCSV');
            $data = array();
            $this->addMessage($view,$data);
            
            $this->load->library('upload');
			$data = array('name' => 'prévisions des enseignants', 'table' => 'nb_group', 'src' => 'initNbGroups');
			
			if(isset($_FILES['csv']) && $_FILES['csv']['size'] > 0)
			{
				try
				{				    
                    $this->config_model->insertFromCSV($data['table'], $_FILES['csv']);
                    redirect('admin/openWishInput');
				}
				catch(Exception $e)
				{
					$data['title'] = 'Erreur';
					$data['content'] = $e->getMessage();
					$data['state'] = 'danger';
					$data['button'] = array('visible' => FALSE);

					$this->load->admin_template(array('Main/message', $view), $data);
				}
			}
			else
                $this->load->admin_template($view, $data);
        }

		/**
		 * Méthode affichant le planning d'un professeur donné.
		 *
		 * Cette méthode affiche le planning de la période actuelle du professeur donnée. 
		 * De plus, les informations de contact du professeur sont affichées.
		 * Enfin si le planning est validé définitivement, l'administrateur à la possibilité de le modifier.
		 * @param  string $initials Initiales (id) du professeur à afficher
		 */
		public function getTeacherPlanning($initials)
		{
			$this->requireConnected();

			try
			{				
				$period = $this->admin_model->getCurrentPeriod();
			
				$data['teacherName'] = $this->admin_model->getTeacherName($initials);
				$data['teacherInitials'] = $initials;
				$data['infos']=$this->admin_model->getTeacherInformations($initials);
				$data['hours'] = $this->admin_model->getHours();
                $data['status'] = $this->admin_model->getHoursStatus();
                $data['effectivHours'] = $this->admin_model->getTeacherHours($initials);
                $data['miniHours'] = $this->admin_model->getTeacherMiniHours($initials)*1.5;
				$data['TeacherTimeSlot'] = $this->admin_model->getTeacherTimeSlots($initials, $period['period_id']);
				$data['wishState'] = $this->admin_model->getTeacherWishState($initials);

				$this->load->admin_template('Admin/displayTeacherPlanning', $data, array('getAvailability.js'));
			}
			catch(Exception $e)
			{
				$data = array('title' => 'Erreur',
							  'content' => $e->getMessage(),
							  'state' => 'danger',
							  'button' => array('visible' => FALSE),
							  'button' => array(
										'value' => 'retour',
										'onclick' => 'admingetTeacherPlanning/'.$initials,
										'visible' => true
										)
							  );

				$this->load->admin_template('Main/message', $data);
			}
		}
		
		/**
		 * Méthode permettant la modification du planning d'un professeur
		 *
		 * Cette méthode affiche le planning du professeur donné et permet à l'administrateur de le modifier
		 * 
		 * @param  string $initials Initiales (id) du professeur à afficher
		 */
		public function modifyTeacherPlanning($initials)
		{
			$this->requireConnected();

			if($this->input->post('timeSlot') !== FALSE)
			{				
				$this->admin_model->insertWish($this->input->post('timeSlot'), 2, $initials);				
				redirect('admin/getTeacherPlanning/'.$initials);
			}

			try
			{		
				$period = $this->admin_model->getCurrentPeriod();
			
				$data['teacherName'] = $this->admin_model->getTeacherName($initials);
				$data['teacherInitials'] = $initials;
				$data['infos']= $this->admin_model->getTeacherInformations($initials);
				$data['hours'] = $this->admin_model->getHours();
                $data['status'] = $this->admin_model->getHoursStatus();
                $data['miniHours'] = $this->admin_model->getTeacherMiniHours($initials)*1.5;
                $data['effectivHours'] = $this->admin_model->getTeacherHours($initials);
				$data['TeacherTimeSlot'] = $this->admin_model->getTeacherTimeSlots($initials, $period['period_id']);
				$data['wishState'] = $this->admin_model->getTeacherWishState($initials);

				$this->load->admin_template('Admin/modifyTeacherPlanning', $data, array('getAvailability.js'));
			}
			catch(Exception $e)
			{
				$data = array('title' => 'Erreur',
							  'content' => $e->getMessage(),
							  'state' => 'danger',
							  'button' => array('visible' => FALSE),
							  'button' => array(
										'value' => 'retour',
										'onclick' => 'admin/getTeacherPlanning/'.$initials,
										'visible' => true
										)
							  );

				$this->load->admin_template('Main/message', $data);
			}
		}

		/**
		 * Méthode affichant la liste modifiable des professeurs
		 *
		 * Cette méthode affiche une liste des professeur et permet pour chaque professeur de le supprimer ou
		 * le modifier. De plus, l'administrateur à la possibilité d'en créer un nouveau
		 */
		public function manageTeachers()
		{
			$this->requireConnected();
			$teachers = $this->admin_model->getTeachers();			
			$this->load->admin_template('Admin/manageTeachers', array('teachers' => $teachers), array('tabManagement.js','manageTeachers.js'));
		}
		
		/**
		 * Méthode permettant la modification de la table des professeurs
		 *
		 * Cette méthode permet de supprimer, de modifier ou de créer un professeur.
		 * Elle ne répond qu'aux requêtes AJAX. Les données sont envoyé par POST.
		 * 'action' indique l'action à effectuer : insert, update ou delete.
		 * 'initials' indique les initiales (id) du professeurs à créer ou modifier
		 * 'lastname' indique le nom de famille du professeur
		 * 'firstname' indique le prénom du professeur
		 */
		public function ajaxRequestTeacher()
		{
			$this->requireConnected();
			if($this->input->is_ajax_request())
			{
				try
				{
					$action = $this->input->post('action');
					$initials = $this->input->post('initials');
					$lastname = $this->input->post('lastname');
					$firstname = $this->input->post('firstname');
				
					if($action == 'insert' || $action == 'update')
					{
						$result = $this->admin_model->singleActionTeacher($action,array('initials' => $initials, 'lastname' => $lastname, 'firstname' => $firstname));
					}
					else if($action == 'delete')
					{
						$result = $this->admin_model->singleActionTeacher($action,array('initials' => $initials));
					}
					else
					{
						$data = array('state' => 'failed', 'message' => 'Erreur : Action inconnue !');
						$this->load->view('Admin/databaseReturnXML', $data);
					}
					
					if($result == 'success')
					{
						if($action == 'insert')
						{
							$password = $this->admin_model->insertTeacherPassword($initials);
							$message = "Insertion réussie ! Son mot de passe de première connexion est $password ";
						}
						if($action == 'update')
							$message = 'Mise à jour réussie !';
						if($action == 'delete')
							$message = 'Suppression réussie !';
						
						$data = array('state' => 'success', 'message' => $message);
					}
					else
					{
						$data = array('state' => 'failed', 'message' => $result);
					}
					$this->load->view('Admin/databaseReturnXML', $data);
				}
				catch(Exception $e)
				{
					$data = array('state' => 'failed', 'message' => $e->getMessage());
					$this->load->view('Admin/databaseReturnXML', $data);
				}
			}
			else
			{
				$data = array('title' => 'Tut tut...', 'content' => "Vous n'êtes pas censé vous trouvez ici...", 'state' => 'warning', 'static' => TRUE);
				$this->load->template('Main/message', $data);
			}
		}
		
		/**
		 * Méthode affichant la liste modifiable des matières
		 *
		 * Cette méthode affiche une liste des matières et permet pour chaque matière de la supprimer ou
		 * la modifier. De plus, l'administrateur à la possibilité d'en créer une nouvelle
		 */
		public function manageSubjects()
		{
			$this->requireConnected();
			$subjects = $this->admin_model->getSubjects();
			$this->load->admin_template('Admin/manageSubjects', array('subjects' => $subjects), array('tabManagement.js','manageSubjects.js'));
		}

		public function manageGroup(){
			//shit happen here
            $this->requireConnected();
            $groups = $this->admin_model->getGroups();
            $this->load->admin_template('Admin/manageGroups',array('groups' => $groups), array('tabManagement.js','manageGroups.js'));
		}

		/**
		 * Méthode permettant la modification de la table des matières
		 *
		 * Cette méthode permet de supprimer, de modifier ou de créer une matière.
		 * Elle ne répond qu'aux requêtes AJAX. Les données sont envoyé par POST.
		 * 'action' indique l'action à effectuer : insert, update ou delete.
		 * 'id' indique l'id de la métière à créer ou modifier. C'est de la forme 'M[1-4][1-3][0-9]{2}C?'
		 * 'short_name' indique le nom court de la matière
		 * 'subject_name' indique le nom long de la matière
		 */
		public function ajaxRequestSubject()
		{
			$this->requireConnected();

			if($this->input->is_ajax_request())
			{
				try
				{
					$action = $this->input->post('action');
					$id = $this->input->post('id');
					$short_name = $this->input->post('short_name');
					$subject_name = $this->input->post('subject_name');
                    $hours_cm = $this->input->post('hours_cm');
                    $hours_td = $this->input->post('hours_td');
                    $hours_tp = $this->input->post('hours_tp');
                    
					if($action == 'insert' || $action == 'update')
					{
						$result = $this->admin_model->singleActionSubject($action, array('id' => $id, 'short_name' => $short_name, 'subject_name' => $subject_name, 'hours_cm' => $hours_cm, 'hours_td' => $hours_td, 'hours_tp' => $hours_tp));
					}
					else if($action == 'delete')
					{
						$result = $this->admin_model->singleActionSubject($action, array('id' => $id));
					}
					else
					{
						$data = array('state' => 'failed', 'message' => 'Erreur : Action inconnue !');
						$this->load->view('Admin/databaseReturnXML', $data);
					}
					
					if($result == 'success')
					{

                        if($action == 'insert')
							$message = 'Insertion réussie !';
						if($action == 'update')
							$message = 'Mise à jour réussie !';
						if($action == 'delete')
							$message = 'Suppression réussie !';
						
						$data = array('state' => 'success', 'message' => $message);
					}
					else
					{
						$data = array('state' => 'failed', 'message' => $result);
					}
					$this->load->view('Admin/databaseReturnXML', $data);
				}
				catch(Exception $e)
				{
					$data = array('state' => 'failed', 'message' => $e->getMessage());
					$this->load->view('Admin/databaseReturnXML', $data);
				}
			}
			else
			{
				$data = array('title' => 'Tut tut...', 'content' => "Vous n'êtes pas censé vous trouvez ici...", 'state' => 'warning', 'static' => TRUE);
				$this->load->template('Main/message', $data);
			}
		}
		
		/**
		 * Méthode permettant de gérer l'association matière <-> responsable
		 *
		 * Cette méthode affiche la liste de matière avec en face leur responsable de matière respectif.
		 * Le responsable peut être modifier.
		 */
		public function manageResponsibles()
		{
			$this->requireConnected();
			
			if ($this->input->post('manageResp') !== false)
			{
				
				$this->admin_model->saveManagerSubject($this->input->post('manageResp'));
				
				$this->session->set_userdata('message', array('title' => 'Succès',
															  'content' => 'Sauvegarde réussie !',
															  'state' => 'success',
															  'static' => false));
				
				redirect('admin/manageResponsibles');
			}
			
			$view = array('Admin/manageResponsibles');
			$data = array();
			$data['teachers'] = $this->admin_model->getPermanentWorkers();
			$data['subjects'] = $this->admin_model->getSubjects();
			$data['responsibles'] = $this->admin_model->getResponsibles();
			
			$this->addMessage($view,$data);
			$this->load->admin_template($view, $data);
			
		}
		
		/**
		 * Méthode permettant d'envoyer un mail de rappel aux professeurs en retard sur leur saisie de voeux
		 * 
		 * @param  string $data Tableau sérialisé des id des professeurs à qui envoyer le mail
		 */
		public function reminderMail($data)
		{
			$data = unserialize(urldecode($data));
			
			foreach($data as $d)
			{
				if(!$this->admin_model->isTeacherFirstConnection($d))
				{
					$recipient = $this->admin_model->getTeacherEmail($d);
				
					$this->email->from('donotreply@univ-rennes1.fr', 'Assist\' EDT');
					$this->email->to($recipient);
					$this->email->subject("Rappel: Validation du planning");
					$this->email->message('N\'oubliez pas de valider votre planning sur Assist\' EDT');                        
					$this->email->send();
				}
			}
            
            $data['teacherWishes'] = $this->admin_model->getTeachersWishes();
            $view = array('Admin/summary');
            $this->session->set_userdata('message', array('title' => 'Succès',
															  'content' => 'Le mail de rappel a bien été envoyé !',
															  'state' => 'success',
															  'static' => false));
            
            $this->addMessage($view,$data);
            $this->load->admin_template($view, $data);
		}
		
		/**
		 * Vérifie la connection de l'administrateur
		 *
		 * Cette méthode redirige vers la page de connection si l'administrateur n'est pas connecté
		 */
		private function requireConnected()
		{
			if(!$this->isAdminConnected())
				redirect('admin/signIn');

			$this->admin_model->checkPeriodState();
		}

		/**
		 * Cette méthode vérifie si l'administrateur est connecté ou non
		 *
		 * Cette méthode vérifie que la variable de session indiquant que l'administrateur est connecté existe.
		 * 
		 * @return boolean Retourne si oui ou non l'admoinistrateur est connecté
		 */
		private function isAdminConnected()
		{
			$connected = FALSE;

			if($this->session->userdata('admin') !== FALSE)
				$connected = ($this->session->userdata('admin') === TRUE) ? TRUE : FALSE;

			return $connected;
		}
		
		/**
		 * Ajoute la vue "message" et rajoute les données nécessaire à cette vue
		 * 
		 * Vérifie si un message est en attente dans la variables de session "message" et si oui, rajoute
		 * la vue message au tableau de vues et les données nécessaire à la vue "message" dans le tableau de données.
		 * 
		 * @param string[] &$views Référence vers le tableau contenant le nom des vues à afficher
		 * @param string[] &$data  Référence vers le tableau associatif contenant les données à transmettre aux vues
		 */
		private function addMessage( array &$views, array &$data) //fonction pour afficher des messages
		{
			if($this->session->userdata('message') !== FALSE)
			{
				$message = $this->session->userdata('message');

				foreach($message as $id=>$value)
					$data[$id]=$value;

				$this->session->unset_userdata('message');
				array_unshift($views,'Main/message');
			}
		}
	}
?>