$(function(){
	$('#modalButton6').click(function(){
		$('#modal6').modal('show')
			.find('#modalContent6')
			.load($(this).attr('value'));
	});
});