function generateUrl()
{
	var choice = "downloadSkeleton/" + document.getElementById("table").value;
	var newUrl = "";
	var urlId = document.getElementById("link");
	
	var url = document.URL;
	url = url.split('/');
	url.pop();
	url.slice();

	url.push(choice);
	
	for (var i = 0; i < url.length; i++)
	{
		if(i != 0)
		{
			newUrl += "/";
			newUrl += url[i];
		}
		
	}
	
	urlId.href = newUrl;
}