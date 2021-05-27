$(function(){
	$('#modalButton12').click(function(){
		$('#modal12').modal('show')
			.find('#modalContent12')
			.load($(this).attr('value'));
	});
});