var base_url = window.location.origin;

$('#tabteachers').on('click', function(){
		$.get(base_url+"/assistEDT/index.php/admin/manageTeachers", returnTeachersTab);
	});

function returnTeachersTab(xml){
	var teacherDiv = document.getElementById("teachers");
	
	var TeacherTable = document.getElementById("teacherTable").firstChild;
	
	//while(TeacherTable.hasChildNodes())
	//{
	//	TeacherTable.removeChild(TeacherTable.childNodes[0]);
	//}	
	
	$(xml).find('teacher').each(function()
	{
		var row = document.createElement("tr");
		var div1 = document.createElement("td");
		var div2 = document.createElement("td");
		
		var name = document.createTextNode($(this).attr('firstname') + " " + $(this).attr('name'));
		//var options = ;
		
		div1.appendChild(name);
		//div2.appendChild(name);
		
		row.appendChild(div1);
		row.appendChild(div2);
		TeacherTable.appendChild(row);
	});
	
	alert("maj");
}
$('#tabsubjects').on('click', function(){});
$('#tabresponsibles').on('click', function(){});
$('#tabinsert').on('click', function(){
		xhr = new XMLHttpRequest();
		if(!xhr){
			alert("Erreur de création de l'objet XML HTTP Request");
		}else{
			xhr.open("GET", base_url+"/assistEDT/index.php/admin/manageDatabase",true);
			alert(base_url+"/assistEDT/index.php/admin/manageDatabase");
			xhr.onreadystatechange = returnInsertTab;
			xhr.send(null);
		}
	});

function returnInsertTab(){
	if(xhr.readyState == 4){
		if(xhr.status == 200){
			alert(xhr.responseText);
			var myResponse = xhr.responseText;
			var myTab = document.createTextNode(myResponse);
			var insertDiv = document.getElementById("insert");
			insertDiv.appendChild(myTab);
		} else {
			alert("Erreur retour requête XMLHTTP : "+xhr.status);
		}
	}
}
