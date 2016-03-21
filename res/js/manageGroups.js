/**
 * Created by Morgan on 10/03/2016.
 */
var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
var url = reg.exec(document.URL)[1] + "admin/ajaxRequestGroups";

var cellNames = ['Identifiant TD','Identifiant TP','Année'];
var cellDatabaseNames = ['id_grouptp','id_grouptd','promo_id'];

