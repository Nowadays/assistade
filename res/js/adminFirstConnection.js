function setNextYear()
{
	var current = $('#currentYear').val();
	current = parseInt(current);
	current++;

	$('#nextYear').text('- ' + current);
}

function createDateInput()
{
	var nbPeriod = $('#periodNumber').val();
	nbPeriod = parseInt(nbPeriod);
	
	var fieldset = $('#dateField');
	
	fieldset.empty();
	fieldset.append($('<legend>Date de fin de saisie de voeux</legend>'));
	
	for(var i = 1; i <= nbPeriod; i++)
	{
		fieldset.append($('<label class="col-sm-3" for="p'+i+'">Date pour P'+i+'</label>'));
		
		var input = $('<input />');
		input.addClass('form-control');
		input.attr('type', 'date');
		input.attr('id', 'p'+i);
		input.attr('required', 'true');
		input.attr('name', 'period['+i+']');
		input.attr('placeholder', 'JJ/MM/AAAA');
		input.attr('pattern', '^[0-3][0-9]/[0|1][0-9]/[0-9]{4}$');
						
		fieldset.append(input);
	}
	fieldset.children('input').wrap('<div class="col-sm-9" />');
}


$('#currentYear').on('change', function()
{
	setNextYear();
});

$('#periodNumber').on('change', function()
{
	createDateInput();
});

setNextYear();
createDateInput();