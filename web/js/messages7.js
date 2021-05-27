$(function(){
	$('#modalButton11').click(function(){
		$('#modal11').modal('show')
			.find('#modalContent11')
			.load($(this).attr('value'));
	});
});