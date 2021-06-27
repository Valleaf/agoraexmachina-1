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

function filePreview(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#form_upload + embed').remove();
            $('#form_upload').after('<embed src="'+e.target.result+'" width="300" height="300"/>');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
//Script permettant l'ouverture et la fermeture de la sidebar
function openNav() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main-content").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main-content").style.marginLeft= "0";
}
//Script permettant l'ouverture et la fermeture de la sidebar droite
function openRightNav(id) {
    let proposalId = id;
    $('.proposal-sidebar').hide(500);
    $('#proposal'+id).show(500);
    document.getElementById("mySidebarProposal").style.width = "50vw";
    // document.getElementById("main-content").style.marginRight = "25vw";
}

function closeRightNav(id) {
    $('#proposal'+id).hide(500);
    document.getElementById("mySidebarProposal").style.width = "0";
    // document.getElementById("main-content").style.marginRight= "0";
}

function openAnswers(id) {
    $('.js-answer'+id).show(500);
    $('#js-chevron-close'+id).show(500);
    $('#js-chevron-close'+id).css('display','flex');
    $('#js-chevron-open'+id).hide(500);

}

function closeAnswers(id) {
    $('.js-answer'+id).hide(500);
    $('#js-chevron-close'+id).hide(500);
    $('#js-chevron-open'+id).show(500);
}