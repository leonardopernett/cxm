$(function(){
	$('#modalButton16').click(function(){
		$('#modal16').modal('show')
			.find('#modalContent16')
			.load($(this).attr('value'));
	});
});