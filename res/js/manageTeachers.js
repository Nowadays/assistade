var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
var url = reg.exec(document.URL)[1] + 'admin/ajaxRequestTeacher';

var cellNames = ['Initiales','Nom','Prénom'];
var cellDatabaseNames = ['initials','lastname','firstname'];


