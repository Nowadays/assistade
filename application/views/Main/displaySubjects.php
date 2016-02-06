<?php
	/**
	 * Vue affichant les modules dans lesquelles l'enseignant est ipliqué durant la période actuelle
	 * Cette vue nécessite les variables suivantes : $periodNumber => numéro de la période en cours
	 */
	echo div(array('style' => 'max-width: 800px;', 'class' => 'text-center center-block'));
		echo heading('Modules pour P'.$periodNumber, 2);
		echo br();

        if($subjects == null){
            echo '<p>Vous n\'avez pas de module pour cette période</p>';
        }else{
            echo div(array('style' => 'max-width: 700px;', 'class' => 'text-center center-block'));
                $headers = array('Module', 'Intitulé');

                echo '<table id="myTable" class="table table-bordered">';
                    echo '<tr>';
                        foreach ($headers as $header)
                            echo "<th>$header</th>";
                    echo '</tr>';

                    foreach ($subjects as $subject)
                    {
                        echo '<tr id="'. $subject['id'] .'">';
                        echo '<td>'. $subject['id'] .'</td><td>'. $subject['subject_name'] .'</td>'; 
                        echo '</tr>';
                    }
                echo '</table>';
            echo div_close();
        }
        
	echo div_close();
?>