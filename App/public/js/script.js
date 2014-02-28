$('.editEvent').on('click',function(e){
	e.preventDefault();
  var element = this;
	$.ajax({
		url:$(this).attr('href'),
    method:'get'
	})
	.success(function(data){
		$(element).parent().next('div.edit').html(data);
	});
});