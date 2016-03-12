<?php
	/**
	 * Vue affichant les modules dans lesquelles l'enseignant est ipliqué durant la période actuelle
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
	 */
	echo div(array('class' => 'text-center center-block'));
		echo heading('Modules pour P'.$periodNumber, 2);
		echo br();

        if($subjects == null){
            echo '<p>Vous n\'avez pas de module pour cette période</p>';
        }else{
            echo div(array('class' => 'text-center center-block'));
                $headers = array('Module', 'Intitulé', 'CM','TD','TP');

                echo '<table id="myTable" class="table table-bordered">';
                    echo '<tr>';
                        foreach ($headers as $header)
                            echo "<th class='text-center'>$header</th>";
                    echo '</tr>';

                    foreach ($subjects as $subject)
                    {
                        echo '<tr id="'. $subject['id_module'] .'">';
                        echo '<td>'. $subject['id_module'] .'</td><td>'. $subject['subject_name'] .'</td><td>'. $subject['nb_cm'] .'</td><td>'. $subject['nb_grp_td'] .'</td><td>'. $subject['nb_grp_tp'] .'</td>'; 
                        echo '</tr>';
                    }
                echo '</table>';
            echo div_close();
        }
        
	echo div_close();
?>