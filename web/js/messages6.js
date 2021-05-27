$(function(){
	$('#modalButton10').click(function(){
		$('#modal10').modal('show')
			.find('#modalContent10')
			.load($(this).attr('value'));
	});
});