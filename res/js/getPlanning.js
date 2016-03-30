function setState(obj, state)
{
	state = parseInt(state);

	var newClass = "";

	switch(state)
	{
		case 1:
			newClass += "unavailable";
			break;
		case 2:
			newClass += "avoid";
			break;
		case 3:
			newClass += "available";
			break;
		default:
			newClass += "coucou";
	}

	$(obj).addClass(newClass);
}

function ajaxAnswer(xml)
{
	var hasColorsChanged = false;

	$('.selectable').removeClass("avoid unavailable available");
	//$('.selectable').addClass('available');

	$(xml).find('timeslot').each(function()
	{
		var obj = $('#' + $(this).attr('id'));
		var state = $(this).attr('state');

		setState(obj, state);
		hasColorsChanged = true;
	});

	applyColors();

	$('#loading').modal('toggle');
}

function getPlanning(periodId)
{
	if(typeof(periodId) === "undefined")
		periodId = "";
	
	$('#loading').modal('toggle');

	var reg = new RegExp('^(http:\/\/[a-zA-Z0-9._\/-]*\/index.php\/)[a-zA-Z0-9._\/-]*$');
	var url = reg.exec(document.URL)[1];
	url += 'main/getAvailabilityXML/' + periodId;

	$.get(url, ajaxAnswer);
}

function applyColors()
{
	$('.available').css('background-color', 'rgb(92, 184, 92)');

	$('.avoid').css('background-color', 'rgb(217, 83, 79)');

	$('.unavailable').css('background-color', 'rgb(68, 68, 68)');
}