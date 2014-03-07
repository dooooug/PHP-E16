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
var allPanels = $('.leftContent nav > ul > li > .toggle').hide(); 
var firstLink = $('.leftContent nav > ul > li > a');
  
firstLink.click(function() {
  $this = $(this);
  $target =  $this.next();

  if(!$target.hasClass('active')){
    allPanels.removeClass('active').slideUp();
    firstLink.removeClass('down');

    $target.addClass('active').slideDown();
    $target.parent().find('a:first').addClass('down');
  }
  
return false;
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

/* API REDDIT */
$.getJSON("http://www.reddit.com/r/EarthPorn/new.json?sort=top&t=day&limit=30", function(json) {
	n=0;
	var table = [];
	var first;
	
	for(i=0; i<30 ;i++)
	{ 
		var img = new Image();
		img.onload = function() {
			if (this.width>1900 && this.width>this.height && this.height>1024){
			table[n]=(this.src);
			first=table[0];
			$('.rightContent').css('background', 'url("' + first + '") no-repeat center fixed');
			$('.rightContent').css('background-position', 'cover');
			n++
		}
		}
		
		img.src = json.data.children[i].data.url;
	}
	

});

/* POPIN */
$('.close').click(function() {
  $('#popin').hide();
});

