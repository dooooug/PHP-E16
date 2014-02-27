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
var date  = jours[jour]+' '+j+' '+mois[moi];

$('.leftContent time').html(date);

/* DAY ACTIVE */
var day = jours[jour].toLowerCase();
$('.' + day).addClass('selected');

$('html, body').animate({
	scrollTop: $('.' + day).offset().top
}, 1000);

/* LEFT UL */
$('.leftContent ul li').click(function() {
	$(this).children('.toggle').slideToggle(400);
	$(this).siblings().children('.toggle').slideUp(400);
});

/* WEATHER */
$.simpleWeather({
	location: 'Montreuil, Paris',
	woeid: '',
	unit: 'c',
	success: function(weather) {
	html = '<h2><i class="icon-'+weather.code+'"></i></h2>';

	$("#weather").html(html);
	},

	error: function(error) {
	$("#weather").html('<p>'+error+'</p>');
}


});

});