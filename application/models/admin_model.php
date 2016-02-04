<?php
	class Admin_model extends MY_Model
	{
		private static $csvTables = array('teacher', 'subject'); //Tables available for importing from CSV file

		public function __construct()
		{
			parent::__construct();
			$this->load->database();
		}

		/**
		* Méthode indiquant si les logins sont bien ceux de l'administrateur.
		* 
		* @param string $username Nom d'utilisateur transmis par l'utilisateur.
		* @param string $password Mot de passe transmis par l'utilisateur.
		*
		* @return boolean TRUE si c'est l'administrateur FALSE sinon.
		*/
		public function isAdmin($username, $password)
		{
			if($this->isFirstConnection())
				return ($username === "admin" && $password === "admin");
			else
			{
				$adminPassword = $this->db->select('*')->from('admin_info')->get()->result_array()[0]['admin_password'];

				return ($username === 'admin' && password_verify($password, $adminPassword));
			}
		}

		/**
		* Méthode indiquant si c'est la première fois que l'administrateur se connecte
		*
		* Cette méthode vérifie si une période est en cours en base de données, ainsi, si la table "actualPeriod" est vide, on devine
		* que c'est la première connection de l'administrateur. Il faut donc lancer la configuration de l'application.
		*
		* @return boolean TRUE si c'est la première connection FALSE sinon.
		*/
		public function isFirstConnection()
		{
			$res = $this->db->select('*')->from('current_period')->get();

			if($res === FALSE)
				$res = 0;
			else
				$res = count($res->result_array());

			return ($res === 0) ? TRUE : FALSE;
		}

		/**
		 * Méthode ouvrant la saisie des voeux pour la période en cours
		 */
		public function openPeriodWishInput()
		{
			$this->db->update('current_period', array('state' => '1'));
		}

		/**
		 * Méthode retournant le numéro de la période courrante.
		 *
		 * Exemple : Si nous sommes en 'P3' ça retournera 3.
		 * @return string Numéro de la période actuelle
		 */
		public function getCurrentPeriodNumber() //TODO Sécuriser un peu (si la méthode retourne autre chose ?)
		{
			return $this->getCurrentPeriod()['period_number'];
		}

		/**
		 * Méthode retournant l'année scolaire en cours
		 * @return string[]|boolean Retourne un tableau associatif avec comme clés : 'beginYear' (début de l'année scolaire) et 'endYear' (fin année scolaire)
		 * ou FALSE s'il n'y a aucune donnée
		 */
		public function getCurrentYear()
		{
			$this->db->select('first_year, second_year')->from('school_year')->join('period', 'school_year.first_year = period.year_id');
			$answer = $this->db->join('current_period', 'period.id = current_period.period_id')->get()->result_array();

			if(empty($answer))
				return FALSE;
			else
			{
				$years = array();
				$years['beginYear'] = $answer[0]['first_year'];
				$years['endYear'] = $answer[0]['second_year'];

				return $years;
			}
		}

		/**
		* Méthode renvoyant l'état du voeu de chaque professeur.
		*
		* Cette méthode renvoie un tableau associatif représentant la table "teacherWish". Chaque ligne du tableau contient
		* les initiales d'un professeur et l'état de son voeu (-1 = non renseigné, 1 = enregistré mais pas validé, 2 = validé définitivement).
		*
		* @param integer $period Parametre facultatif pour récupérer les voeux d'un période donnée. Si aucune donnée envoyé ou FALSE envoyé, la méthode
		* retournera les états des voeux de la période courrante
		*
		* @return array[] Tableau de tableau où chaque sous-tableau est un tableau associatif représentant une ligne de la table "teacherWish". 
		* Les clés sont : ("teacher_id", "state", "lastname", "firstname")
		*/
		public function getTeachersWishes($period = FALSE)
		{
			if($period === FALSE)
				$period = $this->getCurrentPeriodId();

			//attention ici voir pour modifier la requête (union impossible en active record), ATTENTION au schéma utilisé !!!
			$this->db->select('teacher_id, state, lastname, firstname')->distinct()->from('teacher_wish')->join('teacher','teacher.initials = teacher_wish.teacher_id');
			$this->db->where('period_id',$period);
			$teachersWithWish = $this->db->get();
			
			if($teachersWithWish === FALSE)
				$teachersWithWish = array();
			else
				$teachersWithWish = $teachersWithWish->result_array();
			
			$teachers = array();

			foreach($teachersWithWish as $index => $value)
				$teachers[] = $value['teacher_id'];
			
			if(!empty($teachers))
			{
				$this->db->select('initials AS teacher_id, lastname, firstname')->from('teacher');
				$this->db->where_not_in('initials', $teachers);
				$teachersWithoutWish = $this->db->get()->result_array();
			
			
				foreach($teachersWithoutWish as $index => $teacher)
					$teachersWithoutWish[$index]['state'] = -1;
						
				$result = array_merge($teachersWithWish, $teachersWithoutWish);
			}
			else
			{
				$teachers = $this->getTeachers();

				foreach ($teachers as &$t)
				{
					$t['state'] = -1;
					$t['teacher_id'] = $t['initials'];
				}

				$result = $teachers;
			}
			
			// Obtient une liste de colonnes
			foreach ($result as $key => $row) {
				$teacherLN[$key]  = $row['lastname'];
				$teacherWishState[$key] = $row['state'];
			}

			// Trie les données par lastname décroissant, state croissant
			// Ajoute $result en tant que dernier paramètre, pour trier par la clé commune
			array_multisort($teacherWishState, SORT_DESC, $teacherLN, SORT_ASC, $result);
			return $result;
		}

		/**
		* Méthode retournant le nom d'un professeur
		* 
		* Cette méthode renvoie le nom d'un professeur (prénom + nom) en fonction des initiales fournis.
		* Si les initiales sont invalides, la méthode lance une exeption
		*
		* @param string $id Initiales du professeur dont la méthode doit retourner le nom.
		*
		* @return string Retourne le prénom + nom
		*/
		public function getTeacherName($id)
		{
			if(!$this->isInitialsCorrect($id))
				throw new Exception('Initiales incorrectes !');

			$teacherInfo = $this->db->select('firstname, lastname')->from('teacher')->where('initials', $id)->get()->result_array();

			if(empty($teacherInfo))
				throw new Exception('Professeur inexistant !');
			
			return $teacherInfo[0]['firstname']. ' ' .$teacherInfo[0]['lastname'];
		}
		
		/**
		* Méthode retournant le mail d'un professeur
		* 
		* Cette méthode renvoie le mail d'un professeur en fonction des initiales fournis.
		* Si les initiales sont invalides, la méthode lance une exception
		*
		* @param string $id Initiales du professeur dont la méthode doit retourner le mail.
		*
		* @return string Retourne l'adresse email
		*/
		public function getTeacherEmail($id)
		{
			if(!$this->isInitialsCorrect($id))
				throw new Exception('Initiales incorrectes !');
				
			$teacherInfo = $this->db->select('email')->from('teacher_information')->where('teacher_id', $id)->get()->result_array();
			
			if(empty($teacherInfo))
				throw new Exception('Professeur inexistant !');
			
			return $teacherInfo[0]['email'];
		}
		
		/**
		 * Méthode retournant les crénaux disponibles
		 *
		 * Cette méthode retourne un tableau de tout les créanaux horaires en base de données via un tableau. Les crénaux sont de la forme XXhYY-XXhYY.
		 * 
		 * @return string[] Tableau associatif ayant comme clé l'id du crénaux et comme valeur le créneau sous la forme XXhYY-XXhYY.
		 */
		public function getHours()
		{
			if(empty($hours))
			{
				$result = array();
				$resultat = $this->db->select('*')->from('hours')->order_by('id','asc')->get()->result_array();

				foreach($resultat as $ligne)
				{
					$chaine = substr($ligne['start_time'], 0 , 5)."-".substr($ligne['end_time'], 0 , 5);
					$chaine = str_replace(':','h',$chaine);
					$result[$ligne['id']] = $chaine;
				}
			}
			else
				$result = $hours;

			return $result;
		}
		
		/**
		 * Méthode modifiant une matière en base de données
		 *
		 * Cette méthode effectue l'action demandé sur la matière fournis
		 * 
		 * @param  string $action  Action à effectuer parmis : 'insert', 'update', 'delete'
		 * @param  string[] $teacher Tableau associatif contenant les clé/valeur suivantes : 'id' (id de la matière de la forme 'M[0-4][0-3][0-9]{2}C?'),
		 * 'short_name' (nom court de la matière), et 'name' (nom de la matière)
		 * 
		 * @return string Renvoie un texte qui sera renvoyé à la requête AJAX. Les réponses possibles sont : 'identifiant déjà existant !' (si insert),
		 * 'nom court incorrect !', 'nom de la matière incorrect !', 'identifiant non existant !' (si update ou delete), 'Erreur : action inconnue !' et 'success'
		 */
		public function singleActionSubject($action,$subject)
		{
			if(!$this->isSubjectIdCorrect($subject['id']))
					return 'identifiant incorrect !';
					
			$subjectInfo = $this->db->select('*')->from('subject')->where('id', $subject['id'])->get()->result_array();

			if($action == 'insert')
			{
				if(!empty($subjectInfo))
					return 'identifiant déjà existant !';
					
				if(!$this->isSubjectNameCorrect	($subject['short_name']))
					return 'nom court incorrect !';
				
				if(!$this->isSubjectNameCorrect	($subject['subject_name']))
					return 'nom de la matière incorrect !';
				
				$this->db->insert('subject', $subject);
				return 'success';
			}
			else if($action == 'update' || $action == 'delete')
			{
				if(empty($subjectInfo))
					return 'identifiant non existant !';
				
				if($action == 'update')
				{
					if(!$this->isSubjectNameCorrect	($subject['short_name']))
						return 'nom court incorrect !';
				
					if(!$this->isSubjectNameCorrect	($subject['subject_name']))
						return 'nom de la matière incorrect !';
				
					$this->db->where('id', $subject['id']);				
					$this->db->update('subject', $subject);
				}
				else
				{
					$this->db->where('sub_id', $subject['id']);
					$this->db->delete('in_charge');
					
					$this->db->where('id', $subject['id']);				
					$this->db->delete('subject');
				}
				
				return 'success';
			}
			else return 'Erreur : action inconnue !';
		}

		/**
		 * Vérifie si le format de l'identifiant de matière est correcte ou non
		 * @param  string  $id Chaine de caractère à tester
		 * @return boolean     TRUE si la chaîne correspond au format d'identifiant des matière, FALSE sinon
		 */
		private function isSubjectIdCorrect($id)
		{
			return preg_match('#^M[1-4][1-3]0[1-9]C?$#',$id);
		}
		
		/**
		 * Vérifie si le format du nom de matière est correcte ou non
		 * @param  string  $id Chaine de caractère à tester
		 * @return boolean     TRUE si la chaîne correspond au format de nom des matière, FALSE sinon
		 */
		private function isSubjectNameCorrect($name)
		{
			return preg_match('#^[\PM|\PC]+$#', $name);
		}
		
		/**
		 * Gère la liaison matière <-> responsable en base de donnée
		 *
		 * Cette méthode associe des professeur à une ou des matières. Il sont responsable de cette matière.
		 * Pour cela, la méthode insert toutes les valeurs dans la table in_charge
		 * 
		 * @param  string[] $data tableau associatif ayant pour clé l'identifiant de la matière et pour valeur l'identifiant (initiales) d'un professeur
		 */
		public function saveManagerSubject($data)
		{
			// test : si chaque matière a un professeur
			if($this->db->count_all('subject') !== count($data))
				throw new Exception('valeurs invalides');
			
			$manageSubj = array();

			foreach ( $data as $subId => $teacherId )
				if($teacherId !== 'default')
					$manageSubj[] = array('teacher_id' => $teacherId, 'sub_id' => $subId);
			
			$this->db->empty_table('in_charge');
			$this->db->insert_batch('in_charge', $manageSubj);
		}
		
		/**
		 * Méthode retournant les responsables de matière
		 * @return array Tableau associatif ayant pour clé l'identifiant de la matière et pour valeur l'identifiant du professeur responsable
		 * (initiales).
		 */
		public function getResponsibles(){
			$query = $this->db->get('in_charge')->result_array();
			$result = array();
			foreach($query as $index => $value)
			{
				$result[$value['sub_id']]=$value['teacher_id'];
			}
			return $result;
		}

		public function insertTeacherPassword($teacherId)
		{
			$password = $this->generatePassword();
			$this->db->insert('teacher_password', array('teacher_id' => $teacherId, 'password' => password_hash($password, PASSWORD_DEFAULT)));

			return $password;
		}
	}
?>