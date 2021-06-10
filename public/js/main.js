$(document).ready(function ()
{
	$('[data-toggle=deleteConfirmation]').confirmation({
		rootSelector: '[data-toggle=deleteConfirmation]',
		title: 'Etes-vous sûr ?',
		btnOkLabel: 'Oui',
		btnCancelLabel: 'Non',
		btnOkClass: 'btn btn-sm btn-danger'
	});

	$('[data-toggle="tooltip"]').tooltip();

	$('textarea.ckeditor').ckeditor();



});


function searching()
{
	var url = updateURLParameter(window.location.href, "search", $('#search').val());
	window.location.href = url;
	return false;
}

function viewing(queryView)
{
	var url = updateURLParameter(window.location.href, "view", queryView);
	window.location.href = url;
	return false;
}


/**
 * http://stackoverflow.com/a/10997390/11236
 */
function updateURLParameter(url, param, paramVal)
{
	url = url.replace("#", "");
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function returnModalHtmlToShowForum(id){
    return "<!-- Button trigger modal -->\n" +
        "<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#exampleModal\">\n" +
        "    Launch demo modal\n" +
        "</button>\n" +
        "\n" +
        "<!-- Modal -->\n" +
        "<div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n" +
        "    <div class=\"modal-dialog\" role=\"document\">\n" +
        "        <div class=\"modal-content\">\n" +
        "            <div class=\"modal-header\">\n" +
        "                <h5 class=\"modal-title\" id=\"exampleModalLabel\">Modal title</h5>\n" +
        "                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\n" +
        "                    <span aria-hidden=\"true\">&times;</span>\n" +
        "                </button>\n" +
        "            </div>\n" +
        "            <div class=\"modal-body\">\n" +
        "                {{ render(controller('App\\\\Controller\\\\ForumController::showForum',{id: "+id+"}))|escape('js') }}\n" +
        "            </div>\n" +
        "            <div class=\"modal-footer\">\n" +
        "                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\n" +
        "                <button type=\"button\" class=\"btn btn-primary\">Supprimer</button>\n" +
        "            </div>\n" +
        "        </div>\n" +
        "    </div>\n" +
        "</div>";
}