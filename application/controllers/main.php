<?php
	/**
	 * Contrôleur principal gérant les utilisateurs (Professeurs)
	 */
	class Main extends CI_Controller
	{
		/**
		 * Constructeur de la classe
		 *
		 * Construit l'objet en chargant le 'main_model', le helper 'form' et les librairies 'session' et 'form_validation'
		 */
		public function __construct()
		{
			parent::__construct();
			$this->load->model('main_model');
			$this->load->helper('form');
			$this->load->library(array('session', 'form_validation'));
		}

		/**
		 * Index : c'est la page affichée par défaut lorsqu'on accède au contrôleur.
		 *
		 * Cette méthode affiche l'accueil à l'utilisateur.
		 */
		public function index()
		{
			$view = array('Main/home');
			$data = array();
			$this->addUserInfo($data);
			$this->addMessage($view,$data);

			$this->load->template($view, $data);
		}

		/**
		* Méthode gèrant la connexion d'un professeur
		*
		* Cette méthode gère la connexion d'un professeur. Elle affiche une liste, via la view signIn, contenant tout les noms des professeurs.
		* Le professeur choisit son nom dans la liste, donne son mot de passe et clique sur connexion. Les données du formulaire sont retransmis à cette même méthode qui 
		* connecte le professeur en gardant dans la session son id (ses initiales) et son nom d'utilisateur (nom + prénom).
		* Enfin, le professeur est redirigé vers son espace personnel.
		*/
		public function signIn()
		{
			//précaution pour l'instant, à voir si on garde
			$this->session->unset_userdata('teacherId');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('is_responsible');
			$this->session->unset_userdata('responsibleId');
			
			if($this->form_validation->run() !== FALSE) //Le formulaire nous est-il retourné et les données valide ?
			{
				$id = $this->input->post('id');
				$password = $this->input->post('password');

				if($this->main_model->isTeacherLoginCorrect($id, $password)) //Bon couple identifiant/mot de passe ?
				{
					$this->session->set_userdata('teacherId', $id);
					$this->session->set_userdata('username', $this->main_model->getTeacherName($id));
					if($this->main_model->isResponsible()) //Est-ce un responsable ?
					{
						$this->session->set_userdata('is_responsible', true); //Si oui, on l'indique dans une variable de session
						$this->session->set_userdata('responsibleId', $id); //et on indique son id
					}
					
					
					$this->requireFirstConnexion(); //Si première connection, on est redirigé pour donner infos

					redirect('main/privateSpace'); //Si pas première connection, redirigé vers page perso
				}
				else //Si les login sont incorrectes, on affiche une erreur + formulaire de connection
				{
					$this->session->set_userdata('message', array('title' => 'Erreur',
															  'state' => 'danger',
															  'content' => 'Mot de passe incorrecte',
															  'static' => FALSE));
				}
			}
			
			$view = array();
			$data = array();
			
			$teachers = $this->main_model->getPermanentWorkers(); //On récupère seulement les profs non vacataires
			
			if(!empty($teachers)) //Si la liste n'est pas vide, on affiche le formulaire
			{
				$view[] = 'Main/signIn';
				$data['teachers'] = $teachers;
			}
			else
				$data['message'] = array('title' => 'Site indisponible', 'content' => 'Le site n\'est pas encore disponible', 'state' => 'danger');

			
			$this->addMessage($view,$data); //Rajoute un message à la liste de vues s'il y en a un à afficher

			$this->load->template($view,$data);
		}
		
		/**
		 * Méthode gérant la première connection de l'utilisateur
		 * 
		 * Cette méthode demande les informations de contacte du professeur (Numéro de téléphone portable et email)
		 * De plus, si l'enseignant n'est pas un vacataire, demande un nouveau mot de passe
		 */
		public function firstConnexion()
		{
			$this->requireConnected();

			if($this->input->post('phoneNumber') === FALSE)
			{
				$data = array();
				$this->addUserInfo($data);
				$this->load->template('Main/firstConnexion', $data);
			}
			else
			{
				$info = array(
					'teacher_id' => $this->session->userdata('teacherId'),
					'phone' => $this->input->post('phoneNumber'),
					'email' => $this->input->post('email')
				);

				$this->main_model->insertFirstConnexion($info, $this->input->post('password'));
				redirect('main/privateSpace');
			}
		}

		/**
		* Méthode déconnectant le professeur.
		*
		* Cette méthode déconnecte le professeur en retirant son id et son nom d'utilisateur (nom + prénom) de la session
		* De plus, si l'enseignant était responsable de matière, retire le drapeau l'indiquant et son id de responsable
		*/
		public function signOut()
		{
			$this->session->unset_userdata('teacherId');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('is_responsible');
			$this->session->unset_userdata('responsibleId');
			
			$message['state']='success';
			$message['title']='Succès';
			$message['content']='Vous avez bien été déconnecté';
			$message['static']=false;
			
			$this->session->set_userdata('message', $message);

			redirect('main/index');
		}
		
		/**
		 * Méthode déconnectant du vacataire
		 * 
		 * Cette méthode déconnecte le responsable de matière de la gesion du vacataire en réaffectant à teacher_id son responsibleId
		 * et en mettant à jour le nom d'utilisateur.
		 */
		public function signOutFromTemporaryWorker()
		{
			$id = $this->session->userdata('responsibleId');
			$this->session->set_userdata('teacherId', $id);
			$this->session->set_userdata('username', $this->main_model->getTeacherName($id));
			
			$message['state']='success';
			$message['title']='Succès';
			$message['content']='Vous avez bien été déconnecté du vacataire';
			$message['static']=false;
			
			$this->session->set_userdata('message', $message);

			redirect('main/privateSpace');
		}

		/**
		 * Méthode affichant l'espace personnel
		 * 
		 * Cette méthode affiche l'espace personnel de l'enseigant. L'ensignant voit ainsi ses informations de contacts
		 * (numéro de téléphone + adresse email) et à la possibilité de les modifier
		 */
		public function privateSpace()
		{
			$this->checkUserState();
			
			$view = array('Main/privateSpace');
			$data = array();
			$this->addUserInfo($data);
			$this->addMessage($view,$data);
			
			$data['infos']=$this->main_model->getTeacherInformations($data['teacherId']);

			$this->load->template($view, $data);
		}
        
        /**
		 * Méthode affichant le planning avec les créneaux horaires des CM de la période.
		 *
		 */
		public function getHoursCM($promo)
		{
			$this->checkUserState();
            
            $view = array('Main/getHoursCM');
            $data = array();
			$this->addUserInfo($data);
            
			$data['hours'] = $this->main_model->getHours();
            $data['status'] = $this->main_model->getHoursStatus();
            $data['periodNumber'] = $this->main_model->getCurrentPeriod()['period_number'];
            $data['cmHours'] = $this->main_model->getHoursCM($promo);
            $data['promo'] = $promo;
            
			$this->load->template($view, $data, array('getAvailability.js'));
		}

		/**
		 * Méthode permettant la modification des information de contacts
		 * 
		 * Cette méthode affiche les formulaire pour la modification des informations de contacts, applique les modifications et
		 * redirige vers l'espace personnel
		 */
		public function modifyInfo()
		{
			$this->checkUserState();
			
			$view = array('Main/modifyInfo');
			$data = array();
			$this->addUserInfo($data);
			$this->addMessage($view,$data);
			$data['infos']=$this->main_model->getTeacherInformations($data['teacherId']);

			if($this->input->post('phoneNumber') == FALSE || $this->input->post('email') == FALSE)
				$this->load->template($view, $data);
			else
			{
				$this->main_model->modifyInfo($this->session->userdata('teacherId'),
											  $this->input->post('phoneNumber'),
											  $this->input->post('email')
				);
				
				$message['state']='success';
				$message['title']='Validé';
				$message['content']='Les changements ont bien été enregistrés';
				$message['static']=false;
				
				$this->session->set_userdata('message', $message);
				
				redirect('main/privateSpace');
			}
		}
		
		/**
		 * Méthode affichant le formulaire pour modifier les disponibilités du profeseur
		 */
		public function setAvailability()
		{
			$this->checkUserState();

			if($this->main_model->isWishInputOpen())
			{
				if($this->input->post('timeSlot') !== FALSE)
				{  
                    if($this->main_model->getTeacherHours($this->session->userdata('teacherId')) < 1.5*$this->main_model->getTeacherMiniHours($this->session->userdata('teacherId'))){
                        $message['state']= 'warning';
				        $message['title']= 'Attention';
                        $message['content']= 'Vous n\'avez pas renseigné suffisemment d\'heures disponibles';
                        $message['static']= FALSE;                        
                    }else{
                        $message['state']= 'success';
                        $message['title']= 'Succès';
                        $message['content']= 'Vos disponibilités ont bien été enregistrées';
                        $message['static']= FALSE;
                    }
                    
                    $state = ($this->input->post('save') !== FALSE) ? 1 : 2;
					
                    if($state === 1){
                        $this->main_model->insertWish($this->input->post('timeSlot'), $state, $this->session->userdata('teacherId'));   
                    }else{
                        if($this->main_model->getTeacherHours($this->session->userdata('teacherId')) < 1.5*$this->main_model->getTeacherMiniHours($this->session->userdata('teacherId'))){
                            $message['state']= 'danger';
                            $message['title']= 'Attention';
                            $message['content']= 'Votre voeux n\'a pas pu être accepté, vous n\'avez pas renseigné suffisemment d\'heures disponibles';
                            $message['static']= FALSE;
                        }else{
                            $this->main_model->insertWish($this->input->post('timeSlot'), $state, $this->session->userdata('teacherId'));
                        }
                    }
                    
                    $this->session->set_userdata('message', $message);
					
					redirect('main/setAvailability');
				}

				$data = array();

				$wishState = $this->main_model->getTeacherWishState();
			
				if($wishState == 2)
					redirect('main/displayMyAvailability');
				else
				{   
					$view = array('Main/getAvailability');
					$data = array();

					$this->addMessage($view,$data);
					$this->addUserInfo($data);

					$data['hours'] = $this->main_model->getHours();
                    $data['status'] = $this->main_model->getHoursStatus();
                    $data['effectivHours'] = $this->main_model->getTeacherHours($this->session->userdata('teacherId'));
                    $data['miniHours'] = floor($this->main_model->getTeacherMiniHours($this->session->userdata('teacherId'))*1.5);
					$data['periodNumber'] = $this->main_model->getCurrentPeriod()['period_number'];
				
					if($wishState == 1)
						$data['TeacherTimeSlot'] = $this->main_model->getTeacherTimeSlots($this->session->userdata('teacherId'));
					
					$this->load->template($view, $data, array('getAvailability.js'));
					
				}
			}
			else
			{
				$data = array('title' => 'Erreur',
							  'content' => 'La saisie des voeux pour la période actuelle est fermée !',
							  'state' => 'warning',
							  'static' => TRUE);
				$this->addUserInfo($data);
				
				$this->load->template('Main/message', $data);
			}
		}

		/**
		 * Méthode retournant les créanaux horaires et le niveau de disponibilité du professeur
		 * 
		 * Cette méthode fournit les l'Id des créneaux horaires et leur niveau de disponibilité du professeur. Cette méthode renvoie ces informations
		 * en XML et ne répond qu'au requêtes AJAX
		 * @param  boolean|integer $periodId Id de la période du voeux à récupérer
		 */
		public function getAvailabilityXML($periodId = FALSE)
		{
			$this->checkUserState();

			if($this->input->is_ajax_request())
			{
				$data = array();
				$data['period'] = $this->main_model->getTeacherTimeSlots($this->session->userdata('teacherId'), $periodId); //If FALSE return current timeSlots else timeSlots of the given period

				$this->load->view('Main/getAvailabilityXML', $data);
			}
			else
			{
				$data = array();
				$data['title'] = 'Tut tut...';
				$data['content'] = "Vous n'êtes pas censé vous trouvez ici...";
				$data['state'] = 'warning';
				$data['static'] = TRUE;

				$this->load->template('Main/message', $data);
			}
			
		}

		/**
		 * Méthode affichant les anciens plannings
		 * 
		 * Cette méthode affiche une liste déroulante avec les anciennes période et en fonction de la période choisie, affiche le voeux qui avait été formulé.
		 */
		public function displayOlderPlanning()
		{
			$this->checkUserState();

			$periods = $this->main_model->getPeriods();

			$data = array();

			$this->addUserInfo($data);
			
			$data['periods'] = array();

			foreach ($periods as $period)
			{
				$data['periods'][$period['id']] = $period['period_number'];
			}

			ksort($data['periods']);

			$data['hours'] = $this->main_model->getHours();
			$data['TeacherTimeSlot'] = $this->main_model->getTeacherTimeSlots($this->session->userdata('teacherId'), key($data['periods']));


			$this->load->template(array('Main/periodList', 'Main/displayAvailability'), $data, array('getPlanning.js', 'getOlderPlanning.js'));
		}

		/**
		 * Méthode affichant le planning définitif actuel
		 */
		public function displayMyAvailability()
		{
			$this->checkUserState();
			
			$view = array('Main/displayAvailability');
			$data = array();
			$this->addMessage($view,$data);
			$data['hours'] = $this->main_model->getHours();
            $data['status'] = $this->main_model->getHoursStatus();
            $data['effectivHours'] = $this->main_model->getTeacherHours($this->session->userdata('teacherId'));
            $data['miniHours'] = floor($this->main_model->getTeacherMiniHours($this->session->userdata('teacherId'))*1.5);
			$this->addUserInfo($data);

			$this->load->template($view, $data, array('getPlanning.js', 'displayAvailability.js'));
		}
		
        
        /**
		 * Méthode affichant les matières dans lesquelles l'enseignant est concerné dans la période actuelle
		 */
		public function displaySubjects()
		{
			$this->checkUserState();
			
			$view = array('Main/displaySubjects');
			$data = array();
            $data['periodNumber'] = $this->main_model->getCurrentPeriod()['period_number'];
            $data['subjects'] = $this->main_model->getTeacherSubjects($this->session->userdata('teacherId'),$data['periodNumber']);
            $this->addUserInfo($data);

			$this->load->template($view, $data);
		}
        
        
		/**
		 * Propose la liste des vacataire ou propose la création d'un vacataire
		 * 
		 * Cette méthode propose de se connecter en tant qu'un vacataire en le sélectionnant dans la liste ou bien
		 * permet la création d'un vacataire pour ensuite le gérer
		 */
		public function selectTemporaryWorkers()
		{
			$this->checkUserState();
			$this->requireResponsible();
			
			if($this->input->post('id') !== FALSE)
			{
				$id = $this->input->post('id');
				$this->session->set_userdata('teacherId', $id);
				$this->session->set_userdata('username', $this->main_model->getTeacherName($id));
	
				$this->requireFirstConnexion();

				redirect('main/privateSpace');
			}
			
			$view = array('Main/selectTemporaryWorkers');
			$data = array();
			
			$temporaryWorkers = $this->main_model->getTemporaryWorkersList();
			
			if(!empty($temporaryWorkers))
			{
				$data['temporaryWorkers'] = $temporaryWorkers;
			}

			$this->addUserInfo($data);
			$this->addMessage($view,$data);

			$this->load->template($view,$data,array('selectTemporaryWorker.js'));
		}
		
		/**
		 * Méthode permettant de créer le vacataire.
		 * 
		 * Cette méthode créer le vacataire en base de données suite à une requête AJAX fournissant les informations (initiales, nom, prénom) via POST
		 * Cette méthode ne répond qu'au requêtes AJAX
		 */
		public function ajaxRequestTemporaryWorker()
		{
			$this->requireConnected();
			if($this->input->is_ajax_request())
			{
				try
				{
					$initials = $this->input->post('initials');
					$lastname = $this->input->post('lastname');
					$firstname = $this->input->post('firstname');

					$result = $this->main_model->insertTemporaryWorker(array('initials' => $initials, 'lastname' => $lastname, 'firstname' => $firstname));
					
					if($result == 'success')
					{
						$message = 'Insertion réussie !';
						$data = array('state' => 'success', 'message' => $message);
					}
					else
					{
						$data = array('state' => 'failed', 'message' => $result);
					}
					$this->load->view('Main/databaseReturnXML', $data);
				}
				catch(Exception $e)
				{
					$data = array('state' => 'failed', 'message' => $e->getMessage());
					$this->load->view('Main/databaseReturnXML', $data);
				}
			}
			else
			{
				$data = array('title' => 'Tut tut...', 'content' => "Vous n'êtes pas censé vous trouvez ici...", 'state' => 'warning', 'static' => TRUE);
				$this->load->template('Main/message', $data);
			}
		}

		/**
		 * Méthode vérifiant différents points sur le visiteur de la page
		 * 
		 * Cette méthode vérifie dans l'ordre : si le visiteur est bien un professeur connecté, si ce n'est pas sa première connection et lance la vérification
		 * de la date pour le changement de période
		 */
		private function checkUserState()
		{
			$this->requireConnected();
			$this->requireFirstConnexion();
		}

		/**
		 * Vérifie si le visiteur est un professeur connecté
		 * 
		 * Si le visiteur n'est pas connecté, on le redirige vers la page de connection en lui affichant un message d'erreur
		 * @return [type] [description]
		 */
		private function requireConnected()
		{
			if($this->session->userdata('teacherId') === FALSE)
			{
				$message['state'] = 'warning';
				$message['title'] = 'Attention';
				$message['content'] = 'Vous devez être connecté pour accéder à cette page';
				$message['static'] = FALSE;
				
				$this->session->set_userdata('message', $message);

				$this->main_model->checkPeriodState();
				
				redirect('main/signIn');
			}
		}

		/**
		 * Vérifie si c'est la première connection du professeur
		 * 
		 * Si c'est sa première connection, le professeur est redirigé vers la méthode "firstConnexion"
		 */
		private function requireFirstConnexion()
		{
			$id = $this->session->userdata('teacherId');
			
			if($this->main_model->isFirstConnexion($id))
			{
				$message['state'] = 'warning';
				$message['title'] = 'Attention';
				$message['content'] = 'Vous devez d\'abord avoir renseigné vos informations avant de pouvoir continuer !';
				$message['static'] = FALSE;
				
				$this->session->set_userdata('message', $message);
			
				redirect('main/firstConnexion');
			}
		}
		
		/**
		 * Vérifie si l'utilisateur est un responsable de matière
		 * 
		 * Si cet utilisateur n'est pas responsable de matière, il est redirigé vers l'accueil avec un message d'erreur
		 * @return [type] [description]
		 */
		private function requireResponsible()
		{
			if($this->session->userdata('is_responsible') === FALSE)
			{
				$message['state'] = 'warning';
				$message['title'] = 'Attention';
				$message['content'] = 'Vous devez être responsable de matière pour accéder à cette page';
				$message['static'] = FALSE;
				
				$this->session->set_userdata('message', $message);
				
				redirect('main/index');
			}
		}
		
		/**
		 * Méthode rajoutant les informations de l'utilisateur dans le tableau fournit en paramètre
		 * 
		 * @param array Référence vers le tableau associatif dans lequel les données de l'utilisateur 
		 * (initiales, nom d'utilisateur, responsable de matière ou non, id de responsable et état du voeux de la période actuelle) seront placées
		 * 
		 * @return array Retourne également le tableau
		 */
		private function addUserInfo(array &$data)
		{
			if($this->session->userdata('teacherId') !== FALSE)
			{
				$data['teacherId'] = $this->session->userdata('teacherId');
				$data['username'] = $this->session->userdata('username');
				$data['is_responsible'] = $this->session->userdata('is_responsible'); //false s'il ne l'est pas
				$data['responsibleId'] = $this->session->userdata('responsibleId');
				$data['wishState'] = $this->main_model->getTeacherWishState();
			}

			return $data;
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

				foreach($message as $id => $value)
					$data[$id]=$value;

				$this->session->unset_userdata('message');
				array_unshift($views,'Main/message');
			}
		}
	}
?>
