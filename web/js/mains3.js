$(function(){
	$('#modalButton7').click(function(){
		$('#modal7').modal('show')
			.find('#modalContent7')
			.load($(this).attr('value'));
	});
});