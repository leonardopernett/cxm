$(function(){
	$('#modalButton15').click(function(){
		$('#modal15').modal('show')
			.find('#modalContent15')
			.load($(this).attr('value'));
	});
});