var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
var url = reg.exec(document.URL)[1] + 'admin/ajaxRequestSubject';

var cellNames = ['Identifiant','Nom court','Nom de la matière','Heures CM/Semaine','Heures TD/Semaine','Heures TP/Semaines'];
var cellDatabaseNames = ['id','short_name','subject_name','hours_cm','hours_td','hours_tp'];

