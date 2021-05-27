$(function(){
	$('#modalButton8').click(function(){
		$('#modal8').modal('show')
			.find('#modalContent8')
			.load($(this).attr('value'));
	});
});