$(function(){
	$('#modalButton9').click(function(){
		$('#modal9').modal('show')
			.find('#modalContent9')
			.load($(this).attr('value'));
	});
});