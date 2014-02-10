$('.index a').on('click',function(e){
	e.preventDefault();
	$('input[name="name"]').focus();
	var $this=$(this);
	if($this.hasClass('on')){
		$('.users').html('');
		$this.removeClass('on');
		
	}else{
		$.ajax({
			url:$this.attr('href')
		})
		.success(function(data){
			$('.users').html(data);
			$('.index a.on').removeClass('on');
			$this.addClass('on');
		});
	}
	$('input[name="name"]').val('');
});
$('.users').on('click','a:first-of-type',function(e){
	e.preventDefault();
	$.ajax({
		url:$(this).attr('href')
	})
	.success(function(data){
		$('section + section').html(data);
	});
})
.on('click','a.fav',function(e){
	e.preventDefault();
	var $this=$(this);
	$.getJSON($this.attr('href'))
	.success(function(data){
		if(data.status==false){
			$this.removeClass('on');
		}else{
			$this.addClass('on');
		}
	});
});


$('input[name="name"]').on('keyup',function(){
	var $this=$(this);
	var $form=$this.parent('form');
	var datas={'name':$this.val()};
 	datas.filter=$('.index a.on').data('filter');
console.log(datas); 
	$.ajax({
		url:$form.attr('action'),
		method:$form.attr('method'),
		data:datas
	})
	.success(function(data){
		$('.users').html(data);
	})
});












