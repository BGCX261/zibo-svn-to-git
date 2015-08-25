function joppaContentInitializePagination(widgetId) {
	$("#widget" + widgetId + ' div.pagination a').click(function() {
		var widgetElement = $(this).parent().parent();
		
		$.get($(this).attr('href'), function(data) {
			widgetId = widgetElement.attr('id').replace('widget', '');
			widgetElement.before(data);
			widgetElement.remove();
			
			joppaContentInitializePagination(widgetId);
		});
		
		return false;
	});
}