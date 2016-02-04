$('#period').on('change', function()
{
	getPlanning($(this).val())
});

getPlanning($('#period').val());