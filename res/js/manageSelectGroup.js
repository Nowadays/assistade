var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
var url = reg.exec(document.URL)[1] + 'admin/ajaxRequestSubject';

var cellNames = ['Identifiant','Nom de la matière','Groupe TD','Groupe TP'];
var cellDatabaseNames = ['id','subject_name','group_td','group_tp'];