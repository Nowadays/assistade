<?php
/**
 * User: Abdel
 * 
 */



echo div(array('class' => 'text-center center-block'));
$headers = array('ID', 'Nom du module','Groupe TD','Groupe TP');

echo '<table id="myTable" class="table table-bordered">';
echo '<tr>';
foreach ($headers as $header)
    echo "<th class='text-center'>$header</th>";
echo '</tr>';

foreach ($groups as $group)
{
    echo '<tr id="'.$group['id_course'].'">';
    echo '<td>'. $group['id_course'] .'</td><td>'. $group['subject_name'] .'</td><td>'. $group['id_grouptd'] .'</td><td>'. $group['id_grouptp'] .'</td>';
    echo '<td>'.nbs(4).'<span style="cursor: pointer;" class="glyphicon glyphicon-trash" data-original-title="Supprimer" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\''. $group['id_grouptp'] .'\')"></span></td>';
    echo '</tr>';
}
echo '</table>';
echo div_close();

?>