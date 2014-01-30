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
date = new Date;
moi = date.getMonth();
mois = new Array('Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');
j = date.getDate();
jour = date.getDay();
jours = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

date = jours[jour]+' <span>'+j+'</span> '+mois[moi];
$('.leftContent time').html(date);


});