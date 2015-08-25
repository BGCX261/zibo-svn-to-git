function joppaForumInitializeOrder(tableId) {
	$("#formForumOrderSubmit").attr("disabled", true);
	$("#" + tableId).sortable({
		items: 'tr',
		handle: "img.handle",
		stop: function(event, ui) {
			$("#formForumOrderSubmit").attr("disabled", false);
			
			var ids = '';
			var rowClass = 'odd';

			$("#" + tableId + " tr").each(function() {
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
				
				var id = row.attr("id").replace('data_', '');
				ids += (ids == '' ? '' : ' ') + id;
			});

			$("#formForumOrderOrder").val(ids);
		}
	});
}