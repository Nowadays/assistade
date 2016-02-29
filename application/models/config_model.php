<?php
	/**
	* Modèle pour le contrôleur Config
	*/
	class Config_model extends MY_Model
	{
		private static $csvTables = array('teacher', 'subject','student_group_tp','mini_nb_hours'); //Tables available for importing from CSV file

		function __construct()
		{
			parent::__construct();
			$this->load->database();
		}

		/**
		 * Insert l'année en cours et les périodes la composant
		 *
		 * Cette méthode enregsitre en base de données l'année en cours et les périodes ainsi que leurs dates de
		 * fin de saisie de voeux indiqué par l'administrateur dans la base de données. Si le format des dates n'est pas
		 * correcte, la méthode lance une exception.
		 * 
		 * @param  int $year 	Première année composant l'année universitaire en cours
		 * @param  string[]  $periodsEndTime Tableau contenant les dates de fin de saisie des périodes
		 */
		public function initYearAndPeriods($year, array $periodsEndTime)
		{
			if(!$this->isDatesCorrect($periodsEndTime, $year))
				throw new Exception("Le format d'une des dates des fin de saisies est incorrecte");

			$this->db->insert('school_year', array('first_year' => $year, 'second_year' => $year + 1));

			$data = array();

			for($i = 1; $i <= count($periodsEndTime); $i++)
				$data[] = array('period_number' => $i, 'year_id' => intval($year), 'end_time' => $periodsEndTime[$i]);

			$this->db->insert_batch('period', $data);

			$currentPeriodId = $this->db->select('id')->from('period')->where('period_number', 1)->where('year_id', $year)->get()->result_array()[0]['id'];
			$currentPeriodId = intval($currentPeriodId);

			$this->db->insert('current_period', array('period_id' => $currentPeriodId, 'state' => 0));
		}

		/**
		* Méthode remplissant des tables via fichiers CSV
		* 
		* Cette méthode permet de remplir, grâce à un fichier CSV, une des trois tables parmis : "teacher", "subject" et "teacherConstraint".
		* Le fichier CSV doit avoir comme première ligne, le nom des colonnes de la table à importer. Si la table mentionné n'est pas autorisé ou
		* si le nom des colonnes ne correspond pas à ceux en base de donnée, cela lancera une Exception avec un message.
		* 
		* @author groupeAssist'Edt
		* @param string $tableName Le nom de la table, le nom doit être celui d'une des trois tables autorisé.
		* @param array $fileInfo Tableau contenant toute les informations sur le fichier uploadé par l'utilisateur.
		*/
		public function insertFromCSV($tableName, array $fileInfo)
		{
			if(!in_array($tableName, self::$csvTables, TRUE))	//If table's name given isn't in csvTables
				throw new Exception("Une erreur inatendues est survenue. Le nom de la table à modifier est incorrect."); //Throw an error

			$file = fopen($fileInfo['tmp_name'], 'r');
			$fileColumns = fgetcsv($file);
			$valuesToInsert = array();
			$diff = array_diff($this->db->list_fields($tableName), $fileColumns);


            if(($fileColumns === FALSE) || !empty($diff)){//If file empty or first line doesn't match table's fields' name
                //Throw an error
                throw new Exception("Le nom des colonnes ne correspond pas aux valeurs attendues ou le fichier est vide !");
            }

			$tmp = array();

			if($tableName === 'teacher')
			{
				$passwordToInsert = array();
				$password = array();
			}

			while(($row = fgetcsv($file)) !== FALSE)
			{
				for($i = 0; $i < count($fileColumns); $i++)
					$tmp[$fileColumns[$i]] = trim($row[$i]);

				$valuesToInsert[] = $tmp;

				if($tableName === 'teacher')
				{
					$password[$tmp['firstname'] .' '. $tmp['lastname']] = $this->generatePassword();
					$passwordToInsert[] = array('teacher_id' => $tmp['initials'], 'password' => password_hash($password[$tmp['firstname'] .' '. $tmp['lastname']], PASSWORD_DEFAULT));
				}
			};

            if(empty($valuesToInsert)){
                throw new Exception("Fichier vide");
            }else{
                $this->db->insert_batch($tableName, $valuesToInsert);
            }

			if($tableName === 'teacher'){
                if (!empty($passwordToInsert))
                {
				    $this->db->insert_batch('teacher_password', $passwordToInsert);
				    return $password;
                }else{
                    throw new Exception("Password vide");
                }
            }
		}

		/**
		 * Retourne les champs de la table donnée
		 *
		 * Attention, cette méthode ne retourne que les chanmps des tables se trouvant dans le tableau "csvTables". Si 
		 * la table demandée ne se trouve pas dans ce tableau, la méthode lance une exception.
		 * @param  string $tableName Nom de la table (nom en base de donnée)
		 */
		public function getFields($tableName)
		{
			if(!in_array($tableName, self::$csvTables))
				throw new Exception('Paramètre invalide !');
				
			return $this->db->list_fields($tableName);
		}

		/**
		 * Méthode retournant les responsables de matières.
		 *
		 * Cette méthode retourne les responsables de matières dans un tableau ayant comme clé l'id de la matière dont ils sont responsables.
		 * @return string[] Tableau ayant pour clé l'id de la matière et pour valeur les initiales du professeur
		 */
		public function getResponsibles()
		{
			$query = $this->db->get('in_charge')->result_array();
			$result = array();

			foreach($query as $index => $value)
			{
				$result[$value['sub_id']]=$value['teacher_id'];
			}

			return $result;
		}

		/**
		 * Méthode récupérant les associations matières / responsable et l'enregistrant en base de donnée
		 *
		 * Cette méthode récupère un tableau contenant l'association matière / responsable.
		 * Elle vérifie que le tableau compte autant de ligne que de matière. Sinon, lance une exception.
		 * Enfin elle enregistre le tout en base de donnée
		 * @param  string[] $data Tableau ayant comme clé l'id de la matière et pour valeur l'id du professeur
		 */
		public function saveManagerSubject($data)
		{
			// test : si chaque matière a un professeur
			if($this->db->count_all('subject') !== count($data))
			{
				throw new Exception('valeurs invalides');
			}	
			
			$manageSubj = array();
			foreach ( $data as $subId => $teacherId )
			{
				if($teacherId !== 'default')
					$manageSubj[] = array('teacher_id'=>$teacherId, 'sub_id' =>$subId);
			}		
			
			$this->db->empty_table('in_charge');
			$this->db->insert_batch('in_charge', $manageSubj);
		}
        
        
        public function saveSubjectHours($table,$data)
		{
			// test : si chaque matière a un professeur
			/*if($this->db->count_all($table) !== count($data))
			{
				throw new Exception('valeurs invalides');
			}	*/
			
			foreach ( $data as $subId => $nbHours )
			{
                $this->db->insert($table,array('nb_hours'=>$nbHours, 'id' =>$subId));
			}		
		}

		/**
		 * Méthode inserant le nouveau mot de passe de l'administrateur en base de donnée
		 * @param  string $password Le nouveau mot de passe
		 */
		public function insertAdminPassword($password)
		{
			$this->db->insert('admin_info', array('admin_password' => password_hash($password, PASSWORD_DEFAULT)));
		}

		/**
		 * Méthode supprimant toute les tables de la base de donnée en ne gardant que le schéma
		 */
		public function cleanDb()
		{
			$prefix = $this->db->dbprefix;
			$prefix = str_replace(".", "", $prefix);

			$this->db->trans_start(); // Transaction car s'il y a une erreur, tout est annulé

			if(!empty($prefix)) // Créer le schéma s'il n'existe pas et on le prend comme schéma par défaut
			{
				$this->db->query("CREATE SCHEMA IF NOT EXISTS $prefix");
				$this->db->query("SET SCHEMA '$prefix'");
			}

			$this->cleanDatabase(); // Suppression des tables

			$this->db->trans_complete(); // Fin transaction
		}

		/**
		 * Méthode créant toute la base de donnée
		 *
		 * Cette méthode créer toutes les tables utiles à l'application en base de donnée.
		 * Si des tables au même nom que celle devant être installées existent, elles seront supprimées
		 */
		public function createDatabase()
		{
			$prefix = $this->db->dbprefix;
			$prefix = str_replace(".", "", $prefix);

			$this->db->trans_start(); // Transaction car s'il y a une erreur, tout est annulé

			if(!empty($prefix))
			{
				$this->db->query("CREATE SCHEMA IF NOT EXISTS $prefix");
				$this->db->query("SET SCHEMA '$prefix'");
			}

			$this->cleanDatabase(); // Suppression d'ancienne table au cas ou

			/************* Création de la BDD **************/

			$this->db->query("CREATE TABLE school_year(
				first_year NUMERIC(4) NOT NULL,
				second_year NUMERIC(4) NOT NULL,
				CONSTRAINT school_year_pk PRIMARY KEY(first_year)
				)");

			$this->db->query("CREATE TABLE period(
				id SERIAL NOT NULL,
				period_number NUMERIC(1) CHECK(period_number BETWEEN 1 AND 5) NOT NULL,
				year_id NUMERIC(4) NOT NULL,
				end_time DATE NOT NULL,
				CONSTRAINT period_pk PRIMARY KEY(id),
				CONSTRAINT period_fk FOREIGN KEY(year_id) REFERENCES school_year(first_year)
				)");

			$this->db->query("CREATE TABLE current_period(
				period_id	INTEGER NOT NULL,
				state 		NUMERIC(1, 0) CHECK(state BETWEEN -1 AND 2) NOT NULL,
				CONSTRAINT actualPeriod_pk	PRIMARY KEY(period_id),
				CONSTRAINT actualPeriod_fk	FOREIGN KEY(period_id)	REFERENCES period(id)
				)");

			$this->db->query("CREATE TABLE admin_info(
				admin_password	VARCHAR(255) NOT NULL,
				CONSTRAINT admin_info_pk PRIMARY KEY(admin_password)
				)");

			$this->db->query("CREATE TABLE subject(
				id				VARCHAR(6) CHECK(id ~ '^M[1-4][1-3]0[1-9]C?$') NOT NULL,
				short_name		VARCHAR(20) NOT NULL,
				subject_name	VARCHAR(80) NOT NULL,
				CONSTRAINT subject_pk PRIMARY KEY(id)
				)");
            
            $this->db->query("CREATE TABLE tp(
				id				VARCHAR(6) NOT NULL,
				nb_hours         INTEGER,
				CONSTRAINT tp_pk PRIMARY KEY(id),
                CONSTRAINT tp_fk1 FOREIGN KEY(id) REFERENCES subject(id) 
				)");
            
            $this->db->query("CREATE TABLE td(
				id				VARCHAR(6) NOT NULL,
				nb_hours         INTEGER,
				CONSTRAINT td_pk PRIMARY KEY(id),
                CONSTRAINT td_fk1 FOREIGN KEY(id) REFERENCES subject(id) 
				)");
            
            $this->db->query("CREATE TABLE cm(
				id				VARCHAR(6) NOT NULL,
				nb_hours         INTEGER,
				CONSTRAINT cm_pk PRIMARY KEY(id),
                CONSTRAINT cm_fk1 FOREIGN KEY(id) REFERENCES subject(id) 
				)");

			$this->db->query("CREATE TABLE teacher(
				initials	VARCHAR(3)	CHECK(initials ~ '^[A-Z]{2,3}$') NOT NULL,
				lastname	VARCHAR(25)	CHECK(lastname ~ '^[A-Z][a-zA-Zé -]+') NOT NULL,
				firstname	VARCHAR(25)	CHECK(firstname ~ '^[A-Z][a-zA-Zé -]+') NOT NULL,
				CONSTRAINT teacher_pk PRIMARY KEY(initials)
				)");

			$this->db->query("CREATE TABLE temporary_worker(
				teacher_id	VARCHAR(3) CHECK(teacher_id ~ '^[A-Z]{2,3}$') NOT NULL,

				CONSTRAINT temporary_worker_pk PRIMARY KEY(teacher_id),

				CONSTRAINT temporary_worker_fk FOREIGN KEY(teacher_id) REFERENCES teacher(initials)
				)");

			$this->db->query("CREATE TABLE teacher_wish(
				id serial NOT NULL,
				teacher_id character varying(3) NOT NULL,
				period_id integer NOT NULL,
				state numeric(1,0) CHECK (state BETWEEN -1 AND 2) NOT NULL,
				CONSTRAINT teacherwish_pk PRIMARY KEY (id),
				CONSTRAINT teacherwish_fk1 FOREIGN KEY (teacher_id)	REFERENCES teacher (initials),
				CONSTRAINT teacherwish_fk2 FOREIGN KEY (period_id)	REFERENCES period (id)
				)");

			$this->db->query("CREATE TABLE hours(
				id serial NOT NULL,
				start_time time without time zone NOT NULL,
				end_time time without time zone NOT NULL,
				CONSTRAINT hours_pk PRIMARY KEY (id)
				)");

			$this->db->query("CREATE TABLE in_charge(
				teacher_id	VARCHAR(3) NOT NULL,
				sub_id		VARCHAR(6) UNIQUE NOT NULL,
				CONSTRAINT inCharge_pk	PRIMARY KEY(teacher_id, sub_id),
				CONSTRAINT inCharge_fk1	FOREIGN KEY(teacher_id)	REFERENCES teacher(initials),
				CONSTRAINT inCharge_fk2	FOREIGN	KEY(sub_id)		REFERENCES subject(id)
				)");

			$this->db->query("CREATE TABLE teacher_information(
				teacher_id	VARCHAR(3) NOT NULL,
				phone	CHAR(10) CHECK(phone ~ '^0[6|7][0-9]{8}$') NOT NULL,
				email	VARCHAR(60) CHECK(email ~ '^[A-Za-z][A-Za-z0-9._-]*@[A-Za-z][A-Za-z0-9._-]*.[a-z]{2,3}$') NOT NULL,
				CONSTRAINT teacherInformation_pk	PRIMARY KEY(teacher_id),
				CONSTRAINT teacherInformation_fk	FOREIGN KEY(teacher_id)	REFERENCES teacher(initials)
				)");

			$this->db->query("CREATE TABLE teacher_password(
				teacher_id VARCHAR(3) NOT NULL,
				password VARCHAR(255) NOT NULL,
				CONSTRAINT teacher_password_pk PRIMARY KEY(teacher_id),
				CONSTRAINT teacher_password_fk FOREIGN KEY(teacher_id) REFERENCES teacher(initials)
				)");

			$this->db->query("CREATE TABLE time_slot(
				id				SERIAL NOT NULL,
				slot_day		NUMERIC(1) CHECK(slot_day BETWEEN 1 AND 5) NOT NULL,
				hour_id			INTEGER NOT NULL,
                status          INTEGER NOT NULL,
				CONSTRAINT timeSlot_pk PRIMARY KEY(id),
				CONSTRAINT timeSlot_fk FOREIGN KEY(hour_id) REFERENCES hours(id)
				)");

			$this->db->query("CREATE TABLE involved_time_slot(
				wish_id integer NOT NULL,
				timeslot_id integer NOT NULL,
				availability_level numeric(1,0) CHECK (availability_level BETWEEN 1 AND 3) NOT NULL,
				CONSTRAINT involvedtimeslot_pk PRIMARY KEY (wish_id, timeslot_id),
				CONSTRAINT involvedtimeslot_fk1 FOREIGN KEY (wish_id)		REFERENCES teacher_wish (id) ,
  				CONSTRAINT involvedtimeslot_fk2 FOREIGN KEY (timeslot_id)	REFERENCES time_slot (id)
  				)");
            
            
            $this->db->query("CREATE TABLE student_group(
				id SERIAL NOT NULL,
                CONSTRAINT student_group_pk PRIMARY KEY(id)
  				)");
            
            $this->db->query("CREATE TABLE student_group_td(
                id INTEGER NOT NULL,
				id_grouptd VARCHAR(1) CHECK(id_grouptd ~ '^[A-Z]$') UNIQUE NOT NULL,
                CONSTRAINT student_group_td_pk PRIMARY KEY(id_grouptd),
                CONSTRAINT student_group_td_fk1 FOREIGN KEY(id) REFERENCES student_group(id)
  				)");
            
            $this->db->query("CREATE TABLE student_group_tp(
                id INTEGER NOT NULL,
                id_grouptp VARCHAR(2) CHECK(id_grouptd ~ '^[A-Z]{1,2}$') UNIQUE NOT NULL,
				id_grouptd VARCHAR(1) NOT NULL,
                CONSTRAINT student_group_tp_pk PRIMARY KEY(id_grouptp),
                CONSTRAINT student_group_tp_fk1 FOREIGN KEY(id) REFERENCES student_group(id),
                CONSTRAINT student_group_tp_fk2 FOREIGN KEY(id_grouptd) REFERENCES student_group_td(id_grouptd)
  				)");
            
            $this->db->query("CREATE TABLE course(
				id_course SERIAL NOT NULL,
                teacher_id VARCHAR(3) NOT NULL,
                student_group_id INTEGER,
                subject_id VARCHAR(6) NOT NULL,
                period_id INTEGER NOT NULL,
                CONSTRAINT course_pk PRIMARY KEY(id_course),
                CONSTRAINT course_fk1 FOREIGN KEY(teacher_id) REFERENCES teacher(initials),
                CONSTRAINT course_fk2 FOREIGN KEY(student_group_id) REFERENCES student_group(id),
                CONSTRAINT course_fk3 FOREIGN KEY(subject_id) REFERENCES subject(id),
                CONSTRAINT course_fk4 FOREIGN KEY(period_id) REFERENCES period(id)
  				)");
            
            $this->db->query("CREATE OR REPLACE VIEW courseDetail AS
				SELECT *
                FROM course
                INNER JOIN subject
                ON course.subject_id = subject.id
  				");
            
            //Pourra être supprimée par la suite, il faudra alors une fonction de calcul du nombre d'heures en prenant en compte le nombre de groupe par matières et le nombre d'heure pour chaque groupe dans la matière
            $this->db->query("CREATE TABLE mini_nb_hours(
                teacher_id VARCHAR(3) NOT NULL,
                period_id INTEGER NOT NULL,
                nb_hours INTEGER,
                CONSTRAINT mini_nb_hours_pk PRIMARY KEY(teacher_id,period_id),
                CONSTRAINT mini_nb_hours_td_fk1 FOREIGN KEY(teacher_id) REFERENCES teacher(initials),
                CONSTRAINT mini_nb_hours_td_fk2 FOREIGN KEY(period_id) REFERENCES period(id)
  				)");
            
            $this->db->query("CREATE TABLE nb_group(
                teacher_id VARCHAR(3) NOT NULL,
                period_id INTEGER NOT NULL,
                subject_id VARCHAR(6) NOT NULL,
                CONSTRAINT nb_group_pk PRIMARY KEY(teacher_id,period_id,subject_id),
                CONSTRAINT nb_group_fk1 FOREIGN KEY(teacher_id) REFERENCES teacher(initials),
                CONSTRAINT nb_group_fk2 FOREIGN KEY(period_id) REFERENCES period(id),
                CONSTRAINT nb_group_fk3 FOREIGN KEY(subject_id) REFERENCES subject(id)
  				)");
            
            $this->db->query("CREATE VIEW group_tp AS
                SELECT * FROM student_group_tp");
            
            $this->db->query("CREATE VIEW subjects AS
                SELECT subject.id, short_name, subject_name, cm.nb_hours as hours_cm, td.nb_hours as hours_td, tp.nb_hours as hours_tp
                FROM subject
                LEFT JOIN cm ON subject.id = cm.id
                LEFT JOIN td ON subject.id = td.id
                LEFT JOIN tp ON subject.id = tp.id");

            
            /************* Triggers *************/
            
            $this->db->query('CREATE OR REPLACE FUNCTION insert_group() RETURNS trigger AS $group$
                BEGIN
                    PERFORM * FROM student_group WHERE id = NEW.id;
                    IF NOT FOUND
                    THEN
                        INSERT INTO student_group VALUES(NEW.id);
                    END IF;
                    PERFORM * FROM student_group_td WHERE id=NEW.id AND id_grouptd=NEW.id_grouptd;
                    IF NOT FOUND
                    THEN
                        INSERT INTO student_group_td VALUES(NEW.id,NEW.id_grouptd);
                    END IF;
                    PERFORM * FROM student_group_tp WHERE id=NEW.id AND id_grouptd=NEW.id_grouptd AND id_grouptp=NEW.id_grouptp;
                    IF NOT FOUND
                    THEN
                        INSERT INTO student_group_tp VALUES(NEW.id,NEW.id_grouptp,NEW.id_grouptd);
                    END IF;
                    RETURN NEW;
                END;
                $group$ LANGUAGE plpgsql');
                
            $this->db->query("CREATE TRIGGER insert_group
                INSTEAD OF INSERT
                ON group_tp
                FOR EACH ROW
                EXECUTE PROCEDURE insert_group()");
            
            $this->db->query('CREATE OR REPLACE FUNCTION insert_subject() RETURNS trigger AS $subject$
                BEGIN
                    PERFORM * FROM subject WHERE id = NEW.id;
                    IF NOT FOUND
                    THEN
                        INSERT INTO subject values (NEW.id, NEW.short_name, NEW.subject_name);
                    END IF;
                    INSERT INTO cm VALUES(NEW.id, NEW.hours_cm);            
                    INSERT INTO td VALUES(NEW.id, NEW.hours_td);
                    INSERT INTO tp VALUES(NEW.id, NEW.hours_tp);
                    RETURN NEW;
                END;
                $subject$ LANGUAGE plpgsql');
                
            $this->db->query("CREATE TRIGGER insert_subject
                INSTEAD OF INSERT
                ON subjects
                FOR EACH ROW
                EXECUTE PROCEDURE insert_subject()");
            
            $this->db->query('CREATE OR REPLACE FUNCTION delete_subject() RETURNS trigger AS $del_subject$
                BEGIN
                    PERFORM * FROM cm WHERE id = OLD.id;
                    IF FOUND
                    THEN
                        DELETE FROM cm WHERE id = OLD.id;
                    END IF;
                    PERFORM * FROM td WHERE id = OLD.id;
                    IF FOUND
                    THEN
                        DELETE FROM td WHERE id = OLD.id;
                    END IF;
                    PERFORM * FROM tp WHERE id = OLD.id;
                    IF FOUND
                    THEN
                        DELETE FROM tp WHERE id = OLD.id;
                    END IF;
                    PERFORM * FROM subject WHERE id = OLD.id;
                    IF FOUND
                    THEN
                        DELETE FROM subject WHERE id = OLD.id;
                    END IF;
                    RETURN NEW;
                END;
                $del_subject$ LANGUAGE plpgsql');
                
            $this->db->query("CREATE TRIGGER delete_subject
                INSTEAD OF DELETE
                ON subjects
                FOR EACH ROW
                EXECUTE PROCEDURE delete_subject()");
            
            $this->db->query('CREATE OR REPLACE FUNCTION update_subject() RETURNS trigger AS $ud_subject$
                BEGIN
                    IF OLD.hours_cm <> NEW.hours_cm
                    THEN
                        UPDATE cm SET nb_hours = NEW.hours_cm WHERE id = NEW.id;
                    END IF;
                    IF OLD.hours_td <> NEW.hours_td
                    THEN
                        UPDATE td SET nb_hours = NEW.hours_td WHERE id = NEW.id;
                    END IF;
                    IF OLD.hours_tp <> NEW.hours_td
                    THEN
                        UPDATE tp SET nb_hours = NEW.hours_tp WHERE id = NEW.id;
                    END IF;
                    IF OLD.short_name <> NEW.short_name OR OLD.subject_name <> NEW.subject_name
                    THEN
                        UPDATE subject SET short_name = NEW.short_name, subject_name = NEW.subject.name WHERE id = NEW.id;
                    END IF;
                    RETURN NEW;
                END;
                $ud_subject$ LANGUAGE plpgsql');
                
            $this->db->query("CREATE TRIGGER update_subject
                INSTEAD OF UPDATE
                ON subjects
                FOR EACH ROW
                EXECUTE PROCEDURE update_subject()");
     
            
			/************* Remplissage de la base de donnée *************/

			$this->db->query("INSERT INTO hours VALUES
				(DEFAULT, '08:00:00', '09:00:00'),
				(DEFAULT, '09:00:00', '10:00:00'),
				(DEFAULT, '10:15:00', '11:15:00'),
				(DEFAULT, '11:15:00', '12:15:00'),
				(DEFAULT, '13:30:00', '14:30:00'),
				(DEFAULT, '14:30:00', '15:30:00'),
				(DEFAULT, '15:45:00', '16:45:00'),
				(DEFAULT, '16:45:00', '17:45:00')");

			$this->db->query("INSERT INTO time_slot VALUES
				(1,1,1,0),
				(2,1,2,0),
				(3,1,3,0),
				(4,1,4,0),
				(5,1,5,0),
				(6,1,6,0),
				(7,1,7,0),
				(8,1,8,0),
				(9,2,1,0),
				(10,2,2,0),
				(11,2,3,0),
				(12,2,4,0),
				(13,2,5,0),
				(14,2,6,0),
				(15,2,7,0),
				(16,2,8,0),
				(17,3,1,0),
				(18,3,2,0),
				(19,3,3,0),
				(20,3,4,0),
				(21,3,5,0),
				(22,3,6,0),
				(23,3,7,0),
				(24,3,8,0),
				(25,4,1,0),
				(26,4,2,0),
				(27,4,3,0),
				(28,4,4,0),
				(29,4,5,1),
				(30,4,6,1),
				(31,4,7,1),
				(32,4,8,1),
				(33,5,1,0),
				(34,5,2,0),
				(35,5,3,0),
				(36,5,4,0),
				(37,5,5,0),
				(38,5,6,0),
				(39,5,7,0),
				(40,5,8,0)");

			$this->db->trans_complete(); // Fin transaction

			// TODO : gérer le cas ou la base de données n'a pas été bien crée
			if($this->db->trans_status() === FALSE)
				echo "error";
		}

		/**
		 * Méthod effectuant la suppression des tables en base de donnée
		 * @return [type] [description]
		 */
		private function cleanDatabase()
		{
			$this->db->query("DROP TABLE IF EXISTS school_year CASCADE");
			$this->db->query("DROP TABLE IF EXISTS period CASCADE");
			$this->db->query("DROP TABLE IF EXISTS current_period CASCADE");
			$this->db->query("DROP TABLE IF EXISTS admin_info CASCADE");
			$this->db->query("DROP TABLE IF EXISTS subject CASCADE");
			$this->db->query("DROP TABLE IF EXISTS teacher CASCADE");
			$this->db->query("DROP TABLE IF EXISTS temporary_worker");
			$this->db->query("DROP TABLE IF EXISTS teacher_wish CASCADE");
			$this->db->query("DROP TABLE IF EXISTS hours CASCADE");
			$this->db->query("DROP TABLE IF EXISTS in_charge CASCADE");
			$this->db->query("DROP TABLE IF EXISTS teacher_information CASCADE");
			$this->db->query("DROP TABLE IF EXISTS teacher_password CASCADE");
			$this->db->query("DROP TABLE IF EXISTS time_slot CASCADE");
			$this->db->query("DROP TABLE IF EXISTS involved_time_slot CASCADE");
		}

		/**
		 * Méthode vérifiant les dates.
		 *
		 * Cette méthode vérifie le format des dates ainsi que leur cohérence.
		 * Une date doit se trouver entre le 1er janvier de l'année actuelle et le 31 décembre de l'année suivant.
		 * Une date est de la forme JJ/MM/AAAA où le "/" peut aussi être "-".
		 * Elle peut aussi être sous la forme AAAA/MM/JJ où le "/" peut aussi être "-"
		 * Enfin, les dates du tableau doivent être strictement croissante (pas de dates égales)
		 * @param  string[]   $dates Tableau contenant les dates
		 * @param  int  $year  première année de l'année universitaire en cours
		 * @return boolean        TRUE si les dates sont cohérentes et au bon format, FALSE sinon
		 */
		private function isDatesCorrect(array $dates, $year)
		{
			foreach ($dates as $date)
			{
				$d = new DateTime($date);
				$min = new DateTime();
				$max = new DateTime();

				$min->setDate(intval($year - 1), 12, 31);
				$max->setDate(intval($year + 2), 1, 1);

				if(($d->getTimestamp() < $min->getTimestamp()) OR ($d->getTimestamp() > $max->getTimestamp()))
					return FALSE;
			}

			$firstTest = TRUE;
			$secondTest = TRUE;
			$thirdTest = TRUE;

			foreach ($dates as $date)
			{
				if(!preg_match('#^[0-3][0-9][/|-][0|1][0-9][/|-][0-9]{4}$#', $date))
				{
					$firstTest = FALSE;
					break;
				}
			}

			foreach ($dates as $date)
			{
				if(!preg_match('#^[0-9]{4}[/|-][0|1][0-9][/|-][0-3][0-9]$#', $date))
				{
					$secondTest = FALSE;
					break;
				}
			}

			if($firstTest OR $secondTest)
			{
				for($i = 1; $i <= count($dates) - 1; $i++)
				{
					$d1 = new DateTime($dates[$i]);
					$d2 = new DateTime($dates[$i + 1]);

					if($d1 >= $d2)
					{
						$thirdTest = FALSE;
						break;
					}
				}
			}

			return (($firstTest OR $secondTest) AND $thirdTest);
		}
	}
?>