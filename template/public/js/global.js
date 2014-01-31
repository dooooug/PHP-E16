$(document).ready(function() {

/* WINDOW HEIGHT */

/*
resizeContent();
	
$(window).resize(function() {
        resizeContent();
    });

function resizeContent() {
    $height = $(window).height();
    $('.rightContent').height($height);
}
*/

/* DATE */
var date  = new Date;
var moi   = date.getMonth();
var mois  = new Array('Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
var j     = date.getDate();
var jour  = date.getDay();
var jours = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
var date  = jours[jour]+' <span>'+j+'</span> '+mois[moi];

$('.leftContent time').html(date);

/* DAY ACTIVE */
var day = jours[jour].toLowerCase();
$('.' + day).addClass('selected');




});