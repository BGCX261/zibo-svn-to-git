function ziboAdminInitializeLocales(localesSortAction) {
	$("#listLocales").sortable({
		axis: 'y',
		handle: '.handle',
		helper: 'clone',
		opacity: 0.5,
		update: function() {
			$.post(localesSortAction, $(this).sortable('serialize'));
		}
	});
	$("#listLocales").disableSelection();
}