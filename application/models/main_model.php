<?php
	/**
	* Classe Modèle pour le contrôleur "main"
	*
	* Cette classe fournit toutes les informations nécessaire au contrôleur "main" en faisant le lien avec la base
	* de données.
	*/
	class Main_model extends MY_Model
	{
		/**
		* @var array[] Tableau recensant tout les créneaux horaires.
		*/
		private static $hours = array();
		/**
		 * Tableau static remplis lors de la première instanciation de la classe
		 * @var array[] Tableau recensant tout les créneaux horaires + jour
		 */
		private static $timeslot = array();

		/**
		* Méthode qui construit l'objet en initialisant la connection à la base de données
		* Et si les attribut static ne sont pas encore remplis, les créer
		*/
		public function __construct()
		{
			$this->load->database();

			if(empty(self::$hours))
				self::$hours = $this->getHours();
			if(empty(self::$timeslot))
				self::$timeslot = $this->getTimeSlots();
		}

		/**
		 * Méthode indiquant si la saisie des voeux est ouverte ou non
		 *
		 * Cette méthode retourne iun booléen indiquant si les professeurs peuvent renseigner leur voeux pour la période courante
		 * @return boolean TRUE si saisie des voeux ouverte FALSE sinon
		 */
		public function isWishInputOpen()
		{
			$ans = $this->db->select('*')->from('current_period')->get()->result_array();

			return (!empty($ans) AND $ans[0]['state'] == 1) ? TRUE : FALSE;
		}
		
		/**
		 * Méthode retournant la liste des vacataires
		 *
		 * Cette méthode retourne un tableau contentant des tableaux associatif représentant chacun une ligne de la table teacher. Les clés sont : 'initials', 'firstname', 'lastname'
		 * @return array[] Tableau contentant des tableaux associatif représentant chacun une ligne de la table teacher. Les clés sont : 'initials', 'firstname', 'lastname'
		 */
		public function getTemporaryWorkersList()
		{
			$teacherId = $this->session->userdata('teacherId');
			
			
			$results = $this->db->select('initials, firstname, lastname')->from('teacher')->join('temporary_worker', 'initials = teacher_id')->order_by('lastname', 'asc')->get()->result_array();
			
			return $results;
		}

		/**
		* Méthode renvoyant un professeur.
		* 
		* Cette méthode renvoie le nom, le prénom et les initiales d'un professeur en fonction de l'id (initiales)
		* fournit en paramètre.
		* 
		* @param string $id Initiales du professeur qui l'identifie dans la table.
		* 
		* @return string[] Tableau associatif ayant comme clé le nom de la colonne (initials, lastname, firstname) et en valeur les informations
		* du professeur.
		*/
		public function getTeacherById($id)
		{
			return $this->db->select('*')->from('teacher')->where('initials', $id)->get()->result_array()[0];
		}
		
		/**
		* Méthode qui retourne le nom et prénom d'un professeur.
		* 
		* Cette méthode renvoie le nom et le prénom d'un professeur sous forme de chaîne de caractères en fonction des initiales fournises en paramètre.
		* 
		* @param string $id Initiales du professeur qui l'identifie dans la table.
		*
		* @return string Renvoie une chaîne de caractères formée ainsi : "prénom nom"
		*
		**/
		public function getTeacherName($id)
		{
			$teacherInfo = $this->getTeacherById($id);

			if(!empty($teacherInfo))
				return $teacherInfo['firstname']. ' ' .$teacherInfo['lastname'];
			else
				return "Error";
		}
		
		/**
		* Méthode renvoyant un booléen.
		* 
		* Cette méthode renvoie un booléen afin de renseigner si le professeur effectue sa première connexion.
		* 
		* @param string $id Initiales du professeur qui l'identifie dans la table.
		* 
		* @return TRUE ou FALSE
		*/
		public function isFirstConnexion($id)
		{
			$this->db->where("teacher_id", $id);
			$query = $this->db->get('teacher_information')->result_array();

			return empty($query);
		}
		
		/**
		 * Méthode retournant les créneaux horaires
		 *
		 * Cette méthode retourne un tableau associatif ayant pour clé l'id du crénaux et pour valeur un tableau associatif représentant une ligne de la table 
		 * 'time_slot'. Les clés sont : 'id', 'slot_day', 'hour_id'
		 * @return array[] Tableau associatif ayant pour clé l'id du crénaux et pour valeur un tableau associatif représentant une ligne de la table 
		 * 'time_slot'. Les clés sont : 'id', 'slot_day', 'hour_id'
		 */
		public function getTimeSlots()
		{			
			if(empty($timeslot))
			{
				$result = [];
				$query_results = $this->db->select('*')->from('time_slot')->order_by('id','asc')->get();

				if($query_results === FALSE)
					return; //Si la table n'existe pas, on sort de la méthode sans rien retourner
				else
					$query_results = $query_results->result_array();

				foreach($query_results as $ligne)
					$result[$ligne['id']] = $ligne;
			}
			else
				$result = $timeslot;

			return $result;
		}
		
		/**
		 * Méthode faisant l'insertion des données de première connexion
		 *
		 * Cette méthode remplis la table 'teacher_information' avec les données d'un professeur se connectant pour la première fois
		 * 
		 * @param  array   $data     Tableau associatif ayant pour clé les nom de colonnes de la table 'teacher_information'
		 * @param  boolean $password Paramètre optionnel. Si est à TRUE, on récupère le mot de passe dans le tableau passé à l'indice 'password' et on le met
		 * à jour dans la table 'teacher_password'
		 */
		public function insertFirstConnexion(array $data, $password = FALSE)
		{
			$this->db->insert('teacher_information',$data);
			if($password !== FALSE)
				$this->db->where('teacher_id', $data['teacher_id'])->update('teacher_password', array('password' => password_hash($password, PASSWORD_DEFAULT)));
		}
		
		/**
		 * Méthode retournant les horaires des jours de la semaine
		 *
		 * Cette méthode retourne les tranches horraires disponibles pour les jours de la semaine. Elle les retourne sous la forme d'un tableau 
		 * associatif ayant pour clé l'id de l'horaire et pour valeur un tableau associatif représentant une ligne du tableau 'hours'. Les clés sont : 'id', 'start_time',
		 * 'end_time'
		 * 
		 * @return array[] Tableau associatif ayant pour clé l'id de l'horaire et pour valeur un tableau associatif représentant une ligne du tableau 'hours'. Les clés sont : 'id', 'start_time',
		 * 'end_time'
		 */
		public function getHours()
		{
			if(empty($hours))
			{
				$result = [];
				$resultat = $this->db->select('*')->from('hours')->order_by('id','asc')->get();

				if($resultat === FALSE) //Si la table n'existe pas, on sort de la méthode sans rien retourner
					return;
				else
					$resultat = $resultat->result_array();

				foreach($resultat as $ligne)
				{
					$chaine = substr($ligne['start_time'], 0 , 5)."-".substr($ligne['end_time'], 0 , 5);
					$chaine = str_replace(':', 'h', $chaine);
					$result[$ligne['id']] = $chaine;
				}
			}
			else
				$result = $hours;

			return $result;
		}
		
		/**
		 * Méthode permettant de modifier les informations d'un professeur
		 *
		 * Cette méthode modifie les information fournies lors de la première connection d'un professeur. Elle peut modifier le numéro de téléphone et/ou l'adresse email.
		 * 
		 * @param  string $id    Initiales (id) du professeur concerné
		 * @param  string $phone Numéro de téléphone. Si la chaîne est vide, on ne modifie pas
		 * @param  string $mail  Adresse email. Si la chaîne est vide, on ne modifie pas
		 */
		public function modifyInfo($id, $phone, $mail)
		{
			if(!empty($phone))
				$this->db->update('teacher_information', array("phone" => $phone), array("teacher_id" => $id));
			if(!empty($mail))
				$this->db->update('teacher_information', array("email" => $mail), array("teacher_id" => $id));
		}
		
		/**
		 * Méthode créeant en base de données un vacataire.
		 *
		 * Cette méthode créer un vacataire dans la table 'teacher' et dans y met son id (initiales) dans le table 'temporary_worker'
		 * 
		 * @param  string[] $teacher Tableau associtaif ayant pour clé les noms des colonnes de la table 'teacher'
		 * @return [type]          [description]
		 */
		public function insertTemporaryWorker($teacher)
		{
			$result = $this->singleActionTeacher('insert',$teacher);
			if($result == 'success')
			{
				$this->db->insert('temporary_worker',array('teacher_id' => $teacher['initials']));
				return 'success';
			}
			else
				return $result;	
		}

		/**
		 * Méthode indiquant si le professeur actuellement connecté est un repsonsable ou non
		 * 
		 * @return boolean TRUE si responsable de matière, FALSE sinon
		 */
		public function isResponsible()
		{
			$teacherId = $this->session->userdata('teacherId');
			
			$results = $this->db->select('*')->from('in_charge')->where('teacher_id', $teacherId)->get()->result_array();
			
			return (!empty($results)); //renvoie true s'il est responsable, false sinon
		}

		/**
		 * Méthode indiquant si le couple login (actuellement c'est l'id du professeur)/mot de passe fournit par le professeur est correcte ou non
		 * 
		 * @param  string  $id       Initiales (id) du professeur. Fait office de login
		 * @param  string  $password Mot de passe fournit à la connection
		 * 
		 * @return boolean           TRUE si les informations sont correctes (le professeur peut se connecter), FALSE sinon
		 */
		public function isTeacherLoginCorrect($id, $password)
		{
			$hash = $this->db->select('password')->from('teacher_password')->where('teacher_id', $id)->get()->result_array();

			if(!empty($hash))
				return password_verify($password, $hash[0]['password']);
			else
				return FALSE;
		}
	}
?>