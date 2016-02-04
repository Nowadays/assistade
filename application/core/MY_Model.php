<?php
	/**
	 * Classe Model de CodeIgniter surchargée pour mettre en commun des méthodes utiles aux autres modèles.
	 */
	class MY_Model extends CI_Model
	{
		protected static $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		protected static $specialCaracters = '$*%!+-/?:#&<>';

		/**
		 * Constructeur chargant la base de données et chargant une bibliothèque tierces si les fonction passord_hash et password_verify de PHP ne sont pas disponibles
		 */
		public function __construct()
		{
			parent::__construct();
			$this->load->database();

			if(!function_exists('password_hash') OR !function_exists('password_verify'))
				require_once(BASEPATH . '../res/php/password.php');
		}

		/**
		 * Méthode retournant la période actuelle.
		 * @return array[] Tableau représentant la ligne de la table actualperiod et de la table period
		 */
		public function getCurrentPeriod()
		{
			//A VOIR ICI LAQUELLE DES FONCTIONS GARDER
			$currentPeriod = $this->db->select('*')->from('current_period')->join('period', 'current_period.period_id = period.id')->get()->result_array();

			return (empty($currentPeriod)) ? FALSE : $currentPeriod[0];
		}

		/**
		 * Méthode retournant l'identifiant de la période actuelle
		 * @return integer Identifiant de la période actuelle
		 */
		public function getCurrentPeriodId()
		{
			$currentPeriod = $this->db->select('*')->from('current_period')->get();
			
			if(!empty($currentPeriod) AND $currentPeriod !== FALSE)
			{
				$currentPeriod = $currentPeriod->result_array();
				$currentPeriod = $currentPeriod[0]['period_id'];
				$currentPeriod = intval($currentPeriod);
				return $currentPeriod;
			}
			else
				return FALSE;
		}

		/**
		 * Méthode retournant les période antérieur à celle actuelle
		 *
		 * Cette méthode retourne un tableau avec toutes les périodes passées.
		 * 
		 * @return array[] Tableau contenant des tableaux représentant chacun une ligne de la table period. Ces tableaux ont pour clés : ''
		 */
		public function getPeriods()
		{
			$currentPeriod = $this->getCurrentPeriod();

			if($currentPeriod !== FALSE)
				return $this->db->select('*')->from('period')->where('year_id', $currentPeriod['year_id'])->where('end_time <', $currentPeriod['end_time'])->get()->result_array();
			else
				return FALSE;
		}

		/**
		 * Méthode modifiant un professeur en base de données
		 *
		 * Cette méthode effectue l'action demandé sur le professeur fournis
		 * 
		 * @param  string $action  Action à effectuer parmis : 'insert', 'update', 'delete'
		 * @param  string[] $teacher Tableau associatif contenant les clé/valeur suivantes : 'initials' (initiales du professeur),
		 * 'lastname' (nom de famille), et 'firstname' (prénom)
		 * 
		 * @return string Renvoie un texte qui sera renvoyé à la requête AJAX. Les réponses possibles sont : 'Initiales incorrectes !',
		 * 'Nom incorrect !', 'Prénom incorrect !', 'Initiales déjà existantes !' (si insert), 'Initiales non existantes !' (si update ou delete),
		 * 'Erreur : action inconnue !' et 'success'
		 */
		public function singleActionTeacher($action,$teacher)
		{
			if(!$this->isInitialsCorrect($teacher['initials']))
					return 'Initiales incorrectes !';
					
			$teacherInfo = $this->db->select('firstname, lastname')->from('teacher')->where('initials', $teacher['initials'])->get()->result_array();
		
			if($action == 'insert' || $action == 'update')
			{
				if(!$this->isNameCorrect($teacher['lastname']))
					return 'Nom incorrect !';
				if(!$this->isNameCorrect($teacher['firstname']))
					return 'Prénom incorrect !';
					
				if($action == 'insert')
				{
					if(!empty($teacherInfo))
						return 'Initiales déjà existantes !';
					
					$this->db->insert('teacher', $teacher);					
				}
				else
				{
					if(empty($teacherInfo))
						return 'Initiales non existantes !';
				
					$this->db->where('initials', $teacher['initials']);				
					$this->db->update('teacher', $teacher);
				}
				return 'success';
			}
			else if($action == 'delete')
			{
				if(empty($teacherInfo))
					return 'Initiales non existantes !';
				
				$results = $this->db->select('id')->from('teacher_wish')->where('teacher_id',$teacher['initials'])->get()->result_array();
				foreach($results as $index=>$value)
				{
					$this->db->where('wish_id', $value['id']);							
					$this->db->delete('involved_time_slot');
				}
								
				$this->db->where('teacher_id',$teacher['initials']);
				$this->db->delete('teacher_wish');
				
				$this->db->where('teacher_id', $teacher['initials']);							
				$this->db->delete('in_charge');
				
				$this->db->where('teacher_id', $teacher['initials']);							
				$this->db->delete('temporary_worker');
							
				$this->db->where('teacher_id', $teacher['initials']);							
				$this->db->delete('teacher_information');
				
				$this->db->where('initials', $teacher['initials']);							
				$this->db->delete('teacher');
					
				return 'success';
			}
			else 
				return 'Erreur : action inconnue !';
		}

		/**
		 * Méthode mettant à jour la période courrante
		 *
		 * Cette méthode mets à jour la table current_period à la dernière period. Elle met l'id de la période dont la date de fin de saisie de voeux n'est pas encore dépassé.
		 * S'il n'y a plus de période, on y mets -1
		 */
		public function checkPeriodState()
		{
			$currentPeriod = $this->db->select('*')->from('period')->join('current_period', 'period.id = current_period.period_id')->get()->result_array()[0];
			$today = new DateTime('NOW');
			$endPeriod = new DateTime($currentPeriod['end_time']);

			if($today > $endPeriod)
			{
				$this->db->select('id')->from('period')->where('period_number', $currentPeriod['period_number'] + 1)->where('year_id', $currentPeriod['year_id']);
				$nextPeriodId = $this->db->get()->result_array();


				if(empty($nextPeriodId))
					$this->db->update('current_period', array('state' => -1));
				else
				{
					$nextPeriodId = $nextPeriodId[0]['id'];
					$this->db->update('current_period', array('period_id' => $nextPeriodId, 'state' => 0));

					$this->checkPeriodState();
				}
			}
		}
		
		/**
		* Méthode renvoyant la liste des professeurs
		*
		* Cette méthode renvoie un tableau contenant d'autre tableau représentant les n-uplets de la table "teacher".
		* Chaque colonne est acessible par son nom.
		*
		* @return array[]|boolean Renvoie un tableau de tableau associatif représentant chacun une ligne de la table teacher. Les clés
		* sont : 'initials', 'lastname', 'firstname'. Si la table n'existe pas, renvoie FALSE
		*/
		public function getTeachers()
		{
			$res = $this->db->select('*')->from('teacher')->order_by('lastname', 'asc')->get();
			return ($res === FALSE) ? array() : $res->result_array();
		}

		/**
		* Méthode renvoyant la liste des matières
		*
		* Cette méthode renvoie un tableau contenant d'autre tableau représentant les n-uplets de la table "subject".
		* Chaque colonne est acessible par son nom.
		*
		* @return array[] Renvoie un tableau de tableau associatif représentant chacun une ligne de la table teacher. Les clés
		* sont : 'id', 'short_name', 'subject_name'.
		*/
		public function getSubjects()
		{
			return $this->db->select('*')->from('subject')->order_by('id', 'asc')->get()->result_array();
		}
		
		/**
		 * Méthode retournant les vacataires
		 *
		 * Cette méthode renvoie un tableau d'id de teacher. Ces id sont ceux des vacataires
		 * @return string[] Tableau contenant les ids des vacataires
		 */
		public function getTemporaryWorkers()
		{			
			
			$query_result = $this->db->select('teacher_id')->from('temporary_worker')->get();

			if($query_result === FALSE)
				return array();
			else
				$query_result = $query_result->result_array();
			
			$results = array();

			foreach($query_result as $index => $value)
				$results[] = $value['teacher_id'];
			
			return $results;
		}
		
		/**
		 * Méthode retournant les professeurs permanent
		 *
		 * Cette méthode renvoie un tableau d'id de teacher. Ces id sont ceux des professeurs permanent
		 * @return string[] Tableau contenant les ids des professeurs permanent
		 */
		public function getPermanentWorkers()
		{
			$temporary_ids = $this->getTemporaryWorkers();
			
			 $this->db->select('*')->from('teacher');

			 if(!empty($temporary_ids))
			 	$this->db->where_not_in('initials', $temporary_ids); //Tout les profs qui ne sont pas vacataires

			 $res = $this->db->order_by('lastname', 'asc')->get();

			 if($res === FALSE) //Si la table n'existe pas (cas ou l'application n'a pas encore de BDD crée)
			 	$res = array();
			 else
			 	$res = $res->result_array();

			 return $res;
		}

		/**
		* Méthode renvoyant l'état du voeu d'un professeur pour une période
		*
		* @param string $teacherId Id du professeur. Si non indiqué, on récupère le teacherId dans la variable de session
		* @param integer $period identifiant de la période concernée. Si non indiqué, on prend la période courrante
		* 
		* @return int Etat du voeu du professeur donné pour la période donné : cela vaudra -1 pour non renseigné, 1 pour enregistré mais pas validé, 2 pour validé
		*/
		public function getTeacherWishState($teacherId = NULL, $period = NULL)
		{
			if($period === NULL)
				$period = $this->getCurrentPeriodId();
		
			if($teacherId === NULL)
				$teacherId = $this->session->userdata('teacherId');

			$results = $this->db->select('state')->from('teacher_wish')->where('teacher_id', $teacherId)->where('period_id', $period)->get()->result_array();

			return (empty($results)) ? 1 : intval($results[0]['state']);
		}

		/**
		 * Méthode retournant l'id du voeu d'un professeur pour une période donnée
		 *
		 * @param  integer $period    Numéro de la période associé au voeu. Si non indiqué, on prend la version courrante
		 * @param  string $teacherId Initiales (id) du professeurs. Si non renseigné, on prend le teacherId dans la variable de session
		 * @return integer Id du voeu. S'il n'y a pas de voeu, retourne -1
		 */
		public function getTeacherWishId($period = NULL, $teacherId = NULL)
		{
			if($period === NULL)
				$period = $this->getCurrentPeriodId();

			if($teacherId === NULL)
				$teacherId = $this->session->userdata('teacherId');
			
			if(intval($period) <= 0)
				return -1;

			$results = $this->db->select('id')->from('teacher_wish')->where('teacher_id', $teacherId)->where('period_id', $period)->get()->result_array();

			return (empty($results)) ? -1 : intval($results[0]['id']);
		}

		/**
		* Méthode retournant les crénaux horaires d'un professeur pour une période donnée
		*
		* Cette méthode retourne un tableau associatif ayant pour clé l'id d'un timeslot et pour valeur, la ligne de la table involved_time_slot associé
		* (tableau associatif avec comme clés : 'wish_id', 'timeslot_id', 'availability_level' -> valeur entre 1 (pas disponible) et 3 (disponible))
		*
		* @param string $teacherId Id (initiales) du professeur concerné
		* @param integer $period Numéro de la période du voeu à récupérer. Si non indiqué, on prend la période actuelle
		*
		* @return array[] Tableau associatif avec pour clé l'id d'un time_slot et pour valeur un tableau associatif représentant une ligne de la
		* table involved_time_slot clés : 'wish_id', 'timeslot_id', 'availability_level' -> valeur entre 1 (pas disponible) et 3 (disponible))
		*/
		public function getTeacherTimeSlots($teacherId, $period = FALSE)
		{
			if($period === FALSE)
				$period = $this->getCurrentPeriodId();

			$result = [];
			$wish_id = $this->getTeacherWishId($period, $teacherId);
			
			if($wish_id !== -1)
			{
				$query_results = $this->db->select('*')->from('involved_time_slot')->where('wish_id', $wish_id)->order_by('timeslot_id', 'asc')->get()->result_array();

				foreach($query_results as $ligne)
					$result[$ligne['timeslot_id']] = $ligne;
			}
			
			return $result;
		}
        
        
        /**
		* Méthode retournant le nombre d'heures de disponnibilité d'un professeur pour une période donnée
		*
		* Cette méthode retourne un entier correspondant au nombre d'heures de disponnibilité du professeur dans la période donnée
		*
		*/
        public function getTeacherHours($teacherId, $period = FALSE)
        {
            if($period === FALSE)
				$period = $this->getCurrentPeriodId();

			$result = null;
			$wish_id = $this->getTeacherWishId($period, $teacherId);
			
			if($wish_id !== -1)
			{
				$query_results = $this->db->select('count(*)')->from('involved_time_slot')->where('wish_id', $wish_id)->where('availability_level', 3)->get()->result_array();

				$result = $query_results[0]['count'];
			}
			
			return $result;
        }
        
        /**
		* Méthode retournant le nombre d'heures de disponnibilité d'un professeur pour une période donnée
		*
		* Cette méthode retourne un entier correspondant au nombre d'heures de disponnibilité du professeur dans la période donnée
		*
		*/
        public function getTeacherMiniHours($teacherId, $period = FALSE)
        {
            if($period === FALSE)
				$period = $this->getCurrentPeriodId();

			$result = null;
			$wish_id = $this->getTeacherWishId($period, $teacherId);
			
			if($wish_id !== -1)
			{
				$query_results = $this->db->select('nb_hours')->from('mini_nb_hours')->where('period_id', $period)->where('teacher_id', $teacherId)->get()->result_array();

				$result = $query_results[0]['nb_hours'];
			}
			
			return $result;
        }

		/**
		 * Méthode retournant les informations sur un professeur donné.
		 *
		 * Cette méthode retourne un tableau contenant toutes les informations (numéro de téléphone portable et adresse email) du professeur demandé
		 * 
		 * @param  string $id Initiales (id) du professeur à qui récupérer les données
		 * 
		 * @return array Tableau associatif représentant une ligne de la table teacher_information. Les clés sont : 'teacher_id', 'phone', 'email'
		 */
		public function getTeacherInformations($id)
		{
			$query = $this->db->get_where('teacher_information', array('teacher_id' => $id))->result_array()[0];
			
			return $query;
		}

		/**
		* Méthode qui insére le voeux d'un professeur
		* 
		* Cette méthode insert dans les tables "teacherWish" et "involvedtimeslot" les données passé en paramètre
		* 
		* @param array $availability Tableau de tableau contenant les différents créneaux sélectionnés par le professeurs et son niveau de disponibilité.
		* @param int $state Indique si les voeux sont définitifs ($state = 3) ou non ($state = 2)
		*/
		public function insertWish(array $availability, $state, $teacherId = NULL)
		{	
			//wish_id = -1 -> créer voeux,
			//state = 1 -> maj, insérer si != 0, modif si nouvelle valeur !=0, supprimer sinon
			//state = 2 -> valider définitivement => insérer si !=0 et !=3, modif si nouvelle valeur !=0 et !=3, supprimer sinon
			
			if($teacherId === NULL)
				$teacherId = $this->session->userdata('teacherId');
			
			$period = $this->getCurrentPeriodId();
			
			$wish_id = $this->getTeacherWishId($period, $teacherId);
			
			if($wish_id === -1) //si le voeux n'existe pas (premier enregistrement)
			{
				$data = array(
					'teacher_id' => $teacherId,
					'period_id' => $period,
					'state' => 1
				);

				$this->db->insert('teacher_wish',$data);
				$wish_id = $this->getTeacherWishId($period);
			}
			else //si le voeux existe déjà
				$this->db->where('wish_id',$wish_id)->delete('involved_time_slot');
			
			$dataToInsert = array();
			
			if($state == 1)
			{
				foreach ($availability as $timeslot_id => $value) //on enregistre tous les voeux !=0
				{
					if($value != 0)
					{
						$data = array(
							'wish_id' => $wish_id,
							'timeslot_id' => $timeslot_id,
							'availability_level' => $value
						);

						$dataToInsert[] = $data;
					}
				}
			}
			else //si state ==2
			{
				foreach ($availability as $timeslot_id => $value) //on enregistre tous les voeux !=0
				{
					if($value != 0)
					{
						$data = array(
							'wish_id' => $wish_id,
							'timeslot_id' => $timeslot_id,
							'availability_level' => $value
						);

						$dataToInsert[] = $data;
					}
				}
			}
			
			if(!empty($dataToInsert)) //pour vérifier qu'on ne fait pas d'insertion de données sans aucune données (remise à zéro + sauvegarder par ex.)
				$this->db->insert_batch('involved_time_slot', $dataToInsert);

			$this->db->where('id', $wish_id)->update('teacher_wish', array('state' => $state));
		}

		/**
		 * Méthode "abstraite" indiquant si c'est la première connexion d'un utilisateur ou de l'administrateur
		 *
		 * Cette méthode est censé est abstraite mais si on la définit comme tel, la classe doit aussi l'être sauf que le framework
		 * CodeIgniter instancie la classe ce qui est impossible...
		 * 
		 */
		public function isFirstConnexion($id = FALSE) {}

		/**
		 * Méthode permettant de générer un mot de passe de manière pseudo-aléatoire
		 *
		 * Cette méthode renvoie un mot de passe pseudo-aléatoire de la forme :
		 * 8 lettres miniscule ou majuscule puis un nombre entre 0 et 9 et un caractère spécial.
		 * En regex : [a-zA-Z]{8}[0-9][$*%!+-/?:#&<>]
		 * @return string Mot de passe généré
		 */
		protected function generatePassword()
		{
			$password = "";

			$alphabet = str_split(self::$alphabet);
			$specialCars = str_split(self::$specialCaracters);

			$alphabetLen = count($alphabet);
			$specialCarsLen = count($specialCars);

			for($i = 0; $i < 8; $i++)
				$password .= $alphabet[mt_rand(0, $alphabetLen - 1)];

			$password .= mt_rand(0, 9);
			$password .= $specialCars[mt_rand(0, $specialCarsLen - 1)];

			return $password;
		}
		
		/**
		 * Méthode vérifiant si le format des initiales d'un professeur est correct.
		 * 
		 * @param  string  $initials Initiales à tester
		 * @return boolean           TRUE si les initiales correspondent, FALSE sinon.
		 */
		protected function isInitialsCorrect($initials)
		{
			return preg_match('#^[A-Z]{2,3}$#', $initials);
		}
		
		/**
		 * Méthode vérifiant si le format du nom/prénom d'un professeur est correct.
		 * 
		 * @param  string  $initials nom/prénom à tester
		 * @return boolean           TRUE si le nom/prénom correspond, FALSE sinon.
		 */
		protected function isNameCorrect($name)
		{
			return preg_match('#^[A-Z][a-zA-Zé -]+$#', $name);
		}
	}
?>