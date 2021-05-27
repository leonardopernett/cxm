$(function(){
	$('#modalButton13').click(function(){
		$('#modal13').modal('show')
			.find('#modalContent13')
			.load($(this).attr('value'));
	});
});