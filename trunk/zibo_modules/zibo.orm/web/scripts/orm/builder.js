function ziboOrmInitializeModelDetail() {
	$("#formModelFieldOrderSubmit").attr("disabled", true);
	$("#tableModelField").sortable({
		items: 'tr',
		handle: "img.handle",
		stop: function(event, ui) {
			$("#formModelFieldOrderSubmit").attr("disabled", false);
			
			var fields = '';
			var rowClass = 'odd';

			$("#tableModelField tr").each(function() {
				var row = $(this);
				if (row.hasClass('odd')) {
					row.removeClass('odd');
				} else {
					row.removeClass('even');
				}
				
				row.addClass(rowClass);
				
				if (rowClass == 'odd') {
					rowClass = 'even';
				} else {
					rowClass = 'odd';
				}
				
				var fieldName = row.attr("id").replace('field_', '');
				fields += (fields == '' ? '' : ' ') + fieldName;
			});
			
			$("#formModelFieldOrderOrder").val(fields);
		}
	});
}