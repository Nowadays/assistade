<?php
	
	class Repartition_model extends CI_Model
	{
        function __construct()
		{
			parent::__construct();
			$this->load->database();
		}

		public function createDatabase()
		{
			$prefix = $this->db->dbprefix;
			$prefix = str_replace(".", "", $prefix);

			$this->db->trans_start();

			$this->db->query("DROP SCHEMA ade CASCADE");
            $this->db->query("CREATE SCHEMA IF NOT EXISTS $prefix");
            $this->db->query("SET SCHEMA '$prefix'");
            $this->db->query("CREATE TABLE groupe(
                id SERIAL NOT NULL,
                grp VARCHAR(2) NOT NULL,
                annee NUMERIC(1) CHECK(annee BETWEEN 1 AND 2) NOT NULL,
                type NUMERIC(1) CHECK(type BETWEEN 1 AND 3) NOT NULL,
                mob_reduite BOOLEAN NOT NULL,
                CONSTRAINT groupe_pk PRIMARY KEY(id))");
            $this->db->query("CREATE TABLE module(
                id SERIAL NOT NULL,
                intitule VARCHAR(30) NOT NULL,
                annee NUMERIC(1) NOT NULL,
                nb_heures_cm INTEGER NOT NULL,
                nb_heures_td INTEGER NOT NULL,
                nb_heures_tp INTEGER NOT NULL,
                CONSTRAINT module_pk PRIMARY KEY(id)
            )");
            $this->db->query("CREATE TABLE prof(
                id SERIAL NOT NULL,
                nom VARCHAR(30) NOT NULL,
                nbheures VARCHAR(2) NOT NULL,
                mob_reduite BOOLEAN NOT NULL,
                CONSTRAINT prof_pk PRIMARY KEY(id)
            )");
            $this->db->query("CREATE TABLE prof_modules(
                id SERIAL NOT NULL,
                prof INTEGER NOT NULL,
                module INTEGER NOT NULL,
                nb_groupes_cm INTEGER NOT NULL,
                nb_groupes_td INTEGER NOT NULL,
                nb_groupes_tp INTEGER NOT NULL,
                CONSTRAINT prof_modules_pk PRIMARY KEY(id),
                CONSTRAINT prof_modules_fk1 FOREIGN KEY(prof) REFERENCES prof(id),
                CONSTRAINT prof_modules_fk2 FOREIGN KEY(module) REFERENCES module(id)
            )");
            $this->db->query("CREATE TABLE prof_groupe_module(
                id SERIAL NOT NULL,
                prof INTEGER NOT NULL,
                module INTEGER NOT NULL,
                groupe INTEGER NOT NULL,
                mob_reduite BOOLEAN NOT NULL,
                CONSTRAINT prof_groupe_module_pk PRIMARY KEY(id),
                CONSTRAINT prof_groupe_module_fk1 FOREIGN KEY(prof) REFERENCES prof(id),
                CONSTRAINT prof_groupe_module_fk2 FOREIGN KEY(module) REFERENCES module(id),
                CONSTRAINT prof_groupe_module_fk3 FOREIGN KEY(groupe) REFERENCES groupe(id)
            )");
            $this->db->query("INSERT INTO groupe(grp,annee,type,mob_reduite) VALUES
                ('2A',2,1,'TRUE'),
                ('1A',1,1,'TRUE'),
                ('A',2,2,'TRUE'),
                ('B',2,2,'FALSE'),
                ('C',2,2,'FALSE'),
                ('F',1,2,'FALSE'),
                ('G',1,2,'FALSE'),
                ('H',1,2,'FALSE'),
                ('I',1,2,'TRUE'),
                ('A1',2,3,'FALSE'),
                ('A2',2,3,'TRUE'),
                ('B1',2,3,'FALSE'),
                ('B2',2,3,'FALSE'),
                ('C1',2,3,'FALSE'),
                ('F1',1,3,'FALSE'),
                ('F2',1,3,'FALSE'),
                ('G1',1,3,'FALSE'),
                ('G2',1,3,'FALSE'),
                ('H1',1,3,'FALSE'),
                ('H2',1,3,'FALSE'),
                ('I1',1,3,'TRUE'),
                ('I2',1,3,'FALSE')");
            $this->db->query("INSERT INTO module(intitule,annee,nb_heures_cm,nb_heures_td,nb_heures_tp) VALUES
                ('COO',1,1,2,2),
                ('Anglais 1A',1,0,2,1),
                ('PPP',1,0,1,1),
                ('Gestion de Projet',1,1,2,1),
                ('POO',1,1,2,2),
                ('Archi',1,1,1,1),
                ('EGO',1,1,1,1),
                ('EC 1A',1,0,1,1),
                ('Math',1,0,2,2),
                ('Anglais 2A',2,0,2,2),
                ('Crypto/Data Internet',2,1,0,2),
                ('Big Data',2,1,0,2),
                ('Web Client',2,0,1,2),
                ('CE',2,1,1,1),
                ('TS/Prog Mob',2,1,0,2),
                ('Rech OP',2,1,2,1),
                ('COMP/Prog Répartie',2,1,1,1),
                ('EC 2A',2,0,0,2);");
            $this->db->query("INSERT INTO prof(nom,nbheures,mob_reduite) VALUES
                ('BIGOU Karim',0,FALSE),
                ('CORREA William',0,FALSE),
                ('DELHAY-LORRAIN Arnaud',0,FALSE),
                ('GENAIVRE Elisabeth',0,FALSE),
                ('JEZEQUEL Tiphaine',0,FALSE),
                ('KHAROUNE Mouloud',0,FALSE),
                ('LE BOUFFANT Gwendal',0,FALSE),
                ('LE GALL Yolande',0,TRUE),
                ('LEAUTE Jean-Bernard',0,FALSE),
                ('LIETARD Ludovic',0,FALSE),
                ('LLANTA Anne-Isabelle',0,FALSE),
                ('MAINGUET Patricia',0,FALSE),
                ('MARTIN Arnaud',0,FALSE),
                ('MILET Fabienne',0,FALSE),
                ('NEDELEC Michèle',0,FALSE),
                ('NERZIC Pierre',0,FALSE),
                ('POULAIN Hervé',0,FALSE),
                ('RAHMOUNI Adib',0,FALSE),
                ('SIMON Claude',0,FALSE),
                ('VAILLANT Denis',0,FALSE),
                ('VIALAT Jean-Christophe',0,FALSE),
                ('BENJOUAD Mohammed',0,FALSE),
                ('CHERON Eric',0,FALSE),
                ('GRANGER Erwan',0,FALSE),
                ('HENRIO Isabelle',0,FALSE),
                ('LE TROCQUER Mickaël',0,FALSE),
                ('LOLLIERIC Pascal',0,FALSE),
                ('ATTIAOUI Dorra',0,FALSE),
                ('HARENT Olivier',0,FALSE),
                ('JAOUANNET Dominique',0,FALSE),
                ('REMPLIS Je',0,FALSE),
                ('LADOUSSE-EVEN Perrine',0,FALSE)");
            $this->db->query("INSERT INTO prof_modules(prof,module,nb_groupes_cm,nb_groupes_td,nb_groupes_tp) VALUES
                (1,6,0,1,2),
                (2,12,0,0,2),
                (3,13,0,1,1),
                (4,14,1,3,0),
                (4,7,1,4,8),
                (5,9,0,1,4),
                (5,16,0,1,3),
                (6,17,0,1,1),
                (6,4,0,1,0),
                (7,9,1,2,4),
                (7,11,1,0,2),
                (7,16,0,1,1),
                (8,3,0,1,2),
                (8,1,0,1,0),
                (8,5,0,1,0),
                (9,12,0,0,1),
                (9,13,0,2,4),
                (10,1,1,1,2),
                (10,17,1,0,2),
                (10,5,1,1,1),
                (10,17,1,1,1),
                (11,10,0,3,5),
                (12,4,0,0,3),
                (13,4,0,2,2),
                (13,17,0,0,1),
                (14,3,0,1,2),
                (14,18,0,0,5),
                (15,5,0,0,1),
                (15,1,0,0,1),
                (16,12,1,0,2),
                (16,11,1,0,3),
                (16,15,1,0,1),
                (17,1,0,1,1),
                (17,5,0,1,1),
                (18,16,1,1,2),
                (18,15,1,0,2),
                (18,9,0,1,0),
                (19,6,0,1,2),
                (20,2,0,4,8),
                (21,1,0,0,3),
                (21,5,0,0,3),
                (22,14,0,0,5),
                (23,5,0,0,1),
                (24,6,0,1,2),
                (25,3,0,1,1),
                (26,15,0,0,2),
                (27,6,0,1,2),
                (28,4,0,1,3),
                (29,8,0,4,8),
                (30,1,0,0,1),
                (30,5,0,0,1),
                (31,1,0,1,0),
                (31,3,0,0,1),
                (31,4,1,0,0),
                (31,5,0,1,0),
                (31,6,1,0,0),
                (31,17,0,1,1),
                (32,3,0,1,2)");
        
			$this->db->trans_complete(); 
            
			if($this->db->trans_status() === FALSE)
				echo "error";
		}
        
         public function repartition(){
            /*
                On prend la liste de toutes les combinaisons de prof/groupe/module possible 
            */
            //Liste des profs/cm/groupe possible
            /*$query=$this->db->query("SELECT ade.group.promo_id, ade.teacher.initials, ade.subject.id, type FROM ade.subject,ade.cm,ade.teacher,ade.groups, ade.nb_group WHERE ade.nb_group.id_module=ade.module.id AND ade.groups.promo_id=ade.subject.promo_id AND ade.teacher.initials=ade.nb_group.id_enseignant AND ade.nb_group.nb_cm>0 AND ade.groupe.type=1");
            $listcm=$query->result_array();*/
             $listcm=array();
             
            //Liste des profs/td/groupe possible
            $query=$this->db->query("SELECT ade.groups.id_grouptd as id, ade.teacher.initials as prof, ade.subject.id as module 
                                FROM ade.subject,ade.cm,ade.teacher,ade.groups, ade.nb_group 
                                WHERE ade.nb_group.id_module=ade.subject.id 
                                AND ade.groups.promo_id=ade.subject.promo_id 
                                AND ade.teacher.initials=ade.nb_group.id_enseignant 
                                AND ade.nb_group.nb_grp_td>0"
            );
            $listtd=$query->result_array();
            
            //Liste des profs/tp/groupe possible
            $query=$this->db->query("SELECT ade.groups.id_grouptp as id, ade.teacher.initials as prof, ade.subject.id as module
                                FROM ade.subject,ade.cm,ade.teacher,ade.groups, ade.nb_group 
                                WHERE ade.nb_group.id_module=ade.subject.id 
                                AND ade.groups.promo_id=ade.subject.promo_id 
                                AND ade.teacher.initials=ade.nb_group.id_enseignant 
                                AND ade.nb_group.nb_grp_tp>0"  
            );
            $listtp=$query->result_array();
            
             //Liste des profs/tp/groupe possible en cas de mobilité réduite
            /*$query=$this->db->query("SELECT ade.groupe.id,prof,module, type FROM ade.module,ade.prof,ade.groupe, ade.prof_modules WHERE ade.prof_modules.module=ade.module.id AND ade.groupe.annee=ade.module.annee AND ade.prof.id=ade.prof_modules.prof AND ade.prof.mob_reduite='t' AND ade.groupe.mob_reduite='t' AND ade.prof_modules.nb_groupes_cm>0 AND ade.groupe.type=1");
            $mobcm=$query->result_array();*/
             $mobcm=array();
             
             //Liste des profs/tp/groupe possible en cas de mobilité réduite
            /*$query=$this->db->query("SELECT ade.groupe.id,prof,module, type FROM ade.module,ade.prof,ade.groupe, ade.prof_modules WHERE ade.prof_modules.module=ade.module.id AND ade.groupe.annee=ade.module.annee AND ade.prof.id=ade.prof_modules.prof AND ade.prof.mob_reduite='t' AND ade.groupe.mob_reduite='t' AND ade.prof_modules.nb_groupes_td>0 AND ade.groupe.type=2");
            $mobtd=$query->result_array();*/
             $mobtd=array();
             
            //Liste des profs/tp/groupe possible en cas de mobilité réduite
            /*$query=$this->db->query("SELECT ade.groupe.id,prof,module, type FROM ade.module,ade.prof,ade.groupe, ade.prof_modules WHERE ade.prof_modules.module=ade.module.id AND ade.groupe.annee=ade.module.annee AND ade.prof.id=ade.prof_modules.prof AND ade.prof.mob_reduite='t' AND ade.groupe.mob_reduite='t' AND ade.prof_modules.nb_groupes_tp>0 AND ade.groupe.type=3");
            $mobtp=$query->result_array();*/
            $mobtp=array();
             
            /*
                On recense le nombre de groupes à charge d'un prof pour chaque type de cours 
            */
            //Nombre de groupes à charge d'un prof pour les cm
            $query=$this->db->query("SELECT id_enseignant as prof, id_module as module, nb_cm FROM ade.nb_group WHERE nb_cm>0");
            $nbgroupescm=$query->result_array();
            
            //Nombre de groupes à charge d'un prof pour les td
            $query=$this->db->query("SELECT id_enseignant as prof, id_module as module, nb_grp_td FROM ade.nb_group WHERE nb_grp_td>0");
            $nbgroupestd=$query->result_array();
            
            //Nombre de groupes à charge d'un prof pour les tp
            $query=$this->db->query("SELECT id_enseignant as prof, id_module as module, nb_grp_tp FROM ade.nb_group WHERE nb_grp_tp>0");
            $nbgroupestp=$query->result_array();
            
            // On commence la boucle avec autant de fois que l'on veut faire la répartition
            for($i=1;$i<=1;$i++){
                /*
                    On initialise le tableau de solutions et on crée une copie des Arrays déjà créé (si on veut faire plusieurs possibilités)
                */
                $res=Array();
                $cplistcm=$listcm;
                $cplisttd=$listtd;
                $cplisttp=$listtp;
                $cpmobcm=$mobcm;
                $cpmobtd=$mobtd;
                $cpmobtp=$mobtp;
                $cpnbgroupescm=$nbgroupescm;
                $cpnbgroupestd=$nbgroupestd;
                $cpnbgroupestp=$nbgroupestp;
                /*
                    On s'occupe des possibilités avec les personnes à mobilité réduite au début ne fonctionne pas totalement pour l'instant
                *//*
                $count=count($cpmobcm);
                while($count>0){
                    $data=array();
                    $data[]=$cpmobcm;
                    $data[]=$cplistcm;
                    $data[]=$cpnbgroupescm;
                    $data[]=$res;
                    $data=$this->repartmob($data);
                    $cpmobcm=$data[0];
                    $cplistcm=$data[1];
                    $cpnbgroupescm=$data[2];
                    $res=$data[3];
                    $count=count($cpmobcm);
                }
                $count=count($cpmobtd);
                while($count>0){
                    $data=array();
                    $data[]=$cpmobtd;
                    $data[]=$cplisttd;
                    $data[]=$cpnbgroupestd;
                    $data[]=$res;
                    $data=$this->repartmob($data);
                    $cpmobtd=$data[0];
                    $cplisttd=$data[1];
                    $cpnbgroupestd=$data[2];
                    $res=$data[3];
                    $count=count($cpmobtd);
                }
                $count=count($cpmobtp);
                while($count>0){
                    $data=array();
                    $data[]=$cpmobtp;
                    $data[]=$cplisttp;
                    $data[]=$cpnbgroupestp;
                    $data[]=$res;
                    $data=$this->repartmob($data);
                    $cpmobtp=$data[0];
                    $cplisttp=$data[1];
                    $cpnbgroupestp=$data[2];
                    $res=$data[3];
                    $count=count($cpmobtp);
                }
                */
                /* 
                    On répartie les CM
                */
                $count=count($cplistcm);
                while($count>0){
                    $data=array();
                    $data[]=$cplistcm;
                    $data[]=$cpnbgroupescm;
                    $data[]=$res;
                    $data=$this->repart($data);
                    $cplistcm=$data[0];
                    $cpnbgroupescm=$data[1];
                    $res=$data[2];
                    $count=count($cplistcm);
                }
                
                /* 
                    On répartie les TD
                */
                $count=count($cplisttd);
                while($count>0){
                    $data=array();
                    $data[]=$cplisttd;
                    $data[]=$cpnbgroupestd;
                    $data[]=$res;
                    $data=$this->repart($data);
                    $cplisttd=$data[0];
                    $cpnbgroupestd=$data[1];
                    $res=$data[2];
                    $count=count($cplisttd);
                }
                
                /* 
                    On répartie les TP
                */
                $count=count($cplisttp);
                while($count>0){
                    $data=array();
                    $data[]=$cplisttp;
                    $data[]=$cpnbgroupestp;
                    $data[]=$res;
                    $data=$this->repart($data);
                    $cplisttp=$data[0];
                    $cpnbgroupestp=$data[1];
                    $res=$data[2];
                    $count=count($cplisttp);
                }
            }
            return $res;
        }
        
        public function repartmob($data){
            $mob=$data[0];
            $list=$data[1];
            $nbgroupes=$data[2];
            $res=$data[3];
            $n=array_rand($mob);
            $combin=$mob[$n];
            unset($mob[$n]);                            
            $mob=array_values($mob);
            $res[]=$combin;
            foreach($list as $j=>$course){
                if(($course['prof']==$combin['prof'])&&($course['module']==$combin['module'])&&($course['id']==$combin['id'])){
                    unset($list[$j]);
                    break;
                }
            }
            $list=array_values($list);
            foreach($nbgroupes as $i=>$liste){
                if(($liste['prof']==$combin['prof'])&&($liste['module']==$combin['module'])){
                    $grp=$nbgroupes[0];
                    next($grp);
                    next($grp);
                    $key=key($grp);
                    $nbgroupes[$i][$key]=$nbgroupes[$i][$key]-1;
                    if($nbgroupes[$i][$key]==0){
                        foreach($mob as $j=>$course){
                            if(($course['prof']==$combin['prof'])&&($course['module']==$combin['module'])){
                                unset($mob[$j]);
                            }
                        }
                        $mob=array_values($mob);
                        foreach($list as $j=>$course){
                            if(($course['prof']==$combin['prof'])&&($course['module']==$combin['module'])){
                                unset($list[$j]);
                            }
                        }
                        $list=array_values($list);
                    }
                    break;
                }     
            }
            $data=Array();
            $data[]=$mob;
            $data[]=$list;
            $data[]=$nbgroupes;
            $data[]=$res;
            return $data;
        }
        
        public function repart($data){
            $list=$data[0];
            $nbgroupes=$data[1];
            $res=$data[2];
            $n=array_rand($list);
            $combin=$list[$n];
            unset($list[$n]);           
            $res[]=$combin;
            foreach($list as $j=>$course){
                if(($course['id']==$combin['id'])&&($course['module']==$combin['module'])){
                    unset($list[$j]);
                }
            }
            foreach($nbgroupes as $i=>$liste){
                if(($liste['prof']==$combin['prof'])&&($liste['module']==$combin['module'])){
                    $grp=$nbgroupes[0];
                    next($grp);
                    next($grp);
                    $key=key($grp);
                    $nbgroupes[$i][$key]=$nbgroupes[$i][$key]-1;
                    if($nbgroupes[$i][$key]==0){
                        foreach($list as $j=>$course){
                            if(($course['prof']==$combin['prof'])&&($course['module']==$combin['module'])){
                                unset($list[$j]);
                            }
                        }
                    }
                    break;
                }     
            }
            $data=Array();
            $data[]=$list;
            $data[]=$nbgroupes;
            $data[]=$res;
            return $data;
        }
	}
?>