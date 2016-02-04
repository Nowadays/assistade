var state = 0;
var hasClick = false;

function setColor(object, color)
{
	$(object).css('background-color', color);
}

function changeState(object, s)
{
	if(typeof(s) !== 'undefined')
		state = s;

	var input = object.children;

	$(input).attr('value', state.toString());

	switch(state)
	{
		case 0:
			setColor(object, 'white');
			break;
		case 1:
			setColor(object, 'rgb(46, 46, 46)');
			break;
		case 2:
			setColor(object, 'rgb(186, 20, 20)');
			break;
		case 3:
			setColor(object, 'rgb(80, 211, 89)');
			break;
		default:
			setColor(object, 'white');
	}
}

function setActive(object)
{
	$('#white, #black, #red, #green').removeClass('active');

	$(object).addClass('active');
}

$('.notSelectable').css('background-color', 'grey');
$('.selectable').each(function(index, element)
{
	var children = element.children;

	var state = $(children).attr('value');
	state = parseInt(state);

	if(state !== 0)
		changeState(element, state);
})

state = 0;

$('#white').on('click', function(){ state = 0; setActive(this);});
$('#black').on('click', function(){ state = 1; setActive(this);});
$('#red').on('click', function(){ state = 2; setActive(this);});
$('#green').on('click', function(){ state = 3; setActive(this);});

$('table').on('mousedown', function(){return false;});

$('.selectable').on('mousedown', function(){hasClick = true; changeState(this)});
$('body').on('mouseup', function(){hasClick = false;});

$('.selectable').on('mouseover', function(e)
{
	if(hasClick)
		changeState(this);
});

$('#reset').on('click', function()
{
	var list = $('.selectable');

	for (var i = 0; i < list.length; i++) 
	{
		changeState(list[i], 0);
	};
});