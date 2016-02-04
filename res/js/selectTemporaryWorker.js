var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
var url = reg.exec(document.URL)[1] + 'main/ajaxRequestTemporaryWorker';

var cellNames = ['Initiales','Nom','Prénom'];
var cellDatabaseNames = ['initials','lastname','firstname'];

function saveNewRow(){
	//$('#loading').modal('toggle');

	var data = {};
	data.action = 'insert';

	var id = "new";
	var row = document.getElementById(id);
	var children = row.children;
	for(var i = 0; i < children.length-1 ; i++)
	{
		data[cellDatabaseNames[i]]= children[i].firstChild.value;
	}
	
	$.post(url,data,function(xml)
	{
		$(xml).find('result').each(function()
		{
			var state = $(this).attr('state');
			var message = $(this).attr('message');
			
			alert(message);
			if(state == "success"){
				var newTeacher = [];
				for(var i = 0; i < children.length-1 ; i++)
				{
					newTeacher.push(children[i].firstChild.value);
					children[i].firstChild.value='';
				}
				
				var teacherName = document.createTextNode(newTeacher[2]+" "+newTeacher[1]);
				var option = document.createElement("option");
				var dropdown = document.getElementById("login");
				option.value = newTeacher[0];
				option.appendChild(teacherName);
				option.selected = true;
				dropdown.appendChild(option);
				
				$('#addButton').removeClass('disabled');
			}
			//$('#loading').modal('toggle');
		});
	});
}