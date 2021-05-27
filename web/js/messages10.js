$(function(){
	$('#modalButton14').click(function(){
		$('#modal14').modal('show')
			.find('#modalContent14')
			.load($(this).attr('value'));
	});
});