<?php
/**
 * Created by PhpStorm.
 * User: Morgan
 * Date: 10/03/2016
 * Time: 14:30
 */
/**
 * Vue permettant le gestion des matières (Ajout, modification et suppression).
 *
 * Cette vue nécessite les variables suivantes : 'subjects' => tableau de tableaux associatif représentant chacun une matière ('id', 'short_name', 'subject_name')
 */

echo div(array('class' => 'modal fade', 'id' => 'loading', 'tabindex' => -1, 'aria-hidden' => 'true'));
echo div(array('class' => 'modal-dialog'));
echo div(array('class' => 'modal-content'));
echo div(array('id' => 'modal-content', 'class' => 'modal-body center-block text-center'));
echo '<p>Chargement</p>';
echo nbs(4);
echo img('res/img/loading.gif');
echo div_close();
echo div_close();
echo div_close();
echo div_close();

echo div(array('class' => 'text-center center-block'));
$headers = array('Identifiant TD', 'Identifiant TP','Année','Options');

echo '<table id="myTable" class="table table-bordered">';
echo '<tr>';
foreach ($headers as $header)
    echo "<th class='text-center'>$header</th>";
echo '</tr>';

foreach ($groups as $group)
{
    echo '<tr id="'.$group['id_grouptp'].'">';
    echo '<td>'. $group['id_grouptd'] .'</td><td>'. $group['id_grouptp'] .'</td><td>'. $group['promo_id'] .'</td>';
    echo '<td>'.nbs(4).'<span style="cursor: pointer;" class="glyphicon glyphicon-trash" data-original-title="Supprimer" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\''. $group['id_grouptp'] .'\')"></span></td>';
    echo '</tr>';
}
echo '</table>';
echo '<button id="addButton" class="btn btn-info" onclick="addRow()">Ajouter une ligne</button>';
echo div_close();





?>