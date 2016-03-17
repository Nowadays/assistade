//notes : pour faire fonctionner ce script il faut :
//avoir un tableau dans la vue avec comme identifiant "myTable"
//avoir créé un tableau cellNames qui contient la liste des noms de cellules (sauf celles de modifications)
//avoir défini la fonction insertIntoDatabase()

function optionHTML(id,edit) {
	if(edit) //renvoie deux icones pour enregistrer ou pour annuler
		return '<span style="cursor: pointer;" class="glyphicon glyphicon-floppy-disk" data-original-title="Enregistrer" data-placement="top" data-toogle="tooltip" onclick="saveChanges(\''+id+'\')"></span>&nbsp&nbsp&nbsp&nbsp<span style="cursor: pointer;" class="glyphicon  glyphicon glyphicon-remove" data-original-title="Annuler" data-placement="top" data-toogle="tooltip" onclick="cancelChanges(\''+id+'\')"></span>';
	else //renvoie deux icones pour modifier ou pour supprimer
		return '<span style="cursor: pointer;" class="glyphicon glyphicon-pencil" data-original-title="Modifier" data-placement="top" data-toogle="tooltip" onclick="editRow(\''+id+'\')""></span>&nbsp&nbsp&nbsp&nbsp<span style="cursor: pointer;" class="glyphicon glyphicon-trash" data-original-title="Supprimer" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\''+id+'\')"></span></td>';
}

function addRow() {
	var table = document.getElementById("myTable");

	var row = table.insertRow();
	row.id = "new";

	for(var i = 0; i < cellNames.length; i++){	
		var cell1 = row.insertCell();
		
		cell1.innerHTML = '<input type="text" class="form-control" placeholder="'+cellNames[i]+'" style="padding : 0px;"/>';
	}
	
	$('#addButton').addClass('disabled');

	var cell1 = row.insertCell();		
	cell1.innerHTML = '<span style="cursor: pointer;" class="glyphicon glyphicon-floppy-disk" data-original-title="Enregistrer" data-placement="top" data-toogle="tooltip" onclick="saveNewRow()"></span>&nbsp&nbsp&nbsp&nbsp<span style="cursor: pointer;" class="glyphicon  glyphicon glyphicon-remove" data-original-title="Annuler" data-placement="top" data-toogle="tooltip" onclick="deleteRow(\'new\')"></span>';
}

function saveNewRow(){
	$('#loading').modal({backdrop: 'static', keyboard: false});

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
			
			//alert(message);
			if(state == "success"){
				$('#modal-content').html('<h3>Succès</<h3><p>' + message + '<br /><br /><button class="btn btn-primary" data-dismiss="modal">Fermer</button></p>');

				id = children[0].firstChild.value;
				row.id = id;
			
				for(var i = 0; i < children.length-1 ; i++)
				{
					children[i].replaceChild(document.createTextNode(children[i].firstChild.value),children[i].firstChild);
				}
				
				var cell = document.createElement("td");		
				cell.innerHTML = optionHTML(id,false);
				
				row.replaceChild(cell,row.lastChild);
				$('#addButton').removeClass('disabled');
			}
			else if(state == "failed")
				$('#modal-content').html('<h3>Echec</<h3><p>Raison : ' + message + '<br /><br /><button class="btn btn-primary" data-dismiss="modal">Fermer</button></p>');
			//$('#loading').modal('toggle');
		});
	});
}

function editRow(id)
{
	var row = document.getElementById(id);
	var children = row.children;
	for(var i = 1; i < children.length-1 ; i++)
	{
		var text = children[i].firstChild.data;
		
		var cell = document.createElement("td");
		cell.innerHTML = '<input type="text" class="form-control" placeholder="'+text+'" value="'+text+'" style="padding : 0px;"/>';
		
		row.replaceChild(cell,children[i]);
	}
	
	var cell = document.createElement("td");
	cell.innerHTML = optionHTML(id,true);
	row.replaceChild(cell,row.lastChild);
}

function saveChanges(id){
	$('#loading').modal('toggle');

	var data = {};
	data.action = 'update';
	data[cellDatabaseNames[0]] = id;
	var row = document.getElementById(id);
	var children = row.children;
	for(var i = 1; i < children.length-1 ; i++)
	{
		data[cellDatabaseNames[i]] = children[i].firstChild.value;
	}
	
	$.post(url, data, function(xml)
	{
		$(xml).find('result').each(function()
		{
			var state = $(this).attr('state');
			var message = $(this).attr('message');
			
			alert(message);
			if(state == "success"){
				for(var i = 1; i < children.length-1 ; i++)
				{
					children[i].replaceChild(document.createTextNode(children[i].firstChild.value),children[i].firstChild);
				}
				
				var cell = document.createElement("td");		
				cell.innerHTML = optionHTML(id,false);
				
				row.replaceChild(cell,row.lastChild);
			}
			$('#loading').modal('toggle');
		});
	});
}

function deleteRow(id){
	if(id == 'new') {
		var row = document.getElementById(id);	
		row.parentNode.removeChild(row);
		$('#addButton').removeClass('disabled');
	}else{
		if (confirm("Voulez-vous supprimer cette ligne")) {
			$('#loading').modal('toggle');

			var row = document.getElementById(id);
			var children = row.children;

			var data = {};
			data.action = 'delete';
			data[cellDatabaseNames[0]] = id;
			
			$.post(url, data, function(xml)
			{
				$(xml).find('result').each(function()
				{
					var state = $(this).attr('state');
					var message = $(this).attr('message');
					
					alert(message);
					if(state == "success"){
							var row = document.getElementById(id);	
							row.parentNode.removeChild(row);
					}
					$('#loading').modal('toggle');
				});
			});
		}
	}
}

function cancelChanges(id)
{
	var row = document.getElementById(id);
	var children = row.children;
	for(var i = 1; i < children.length-1 ; i++)
	{
		children[i].replaceChild(document.createTextNode(children[i].firstChild.placeholder),children[i].firstChild);
	}
	
	var cell = document.createElement("td");		
	cell.innerHTML = optionHTML(id,false);
	
	row.replaceChild(cell,row.lastChild);
}

function showModal()
{
	$('#myModal').appendTo("html").modal('show');
}