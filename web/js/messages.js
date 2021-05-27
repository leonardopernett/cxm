$(function(){
	$('#modalButton1').click(function(){
		$('#modal1').modal('show')
			.find('#modalContent1')
			.load($(this).attr('value'));
	});
});
