$('a.edit').on('click',function(e){
	e.preventDefault();
	$.ajax({
		url:$(this).attr('href'),
    method:'get'
	})
	.success(function(data){
		$("section#popin div#edit").html(data);
    $("section#popin").toggle();
	});
});