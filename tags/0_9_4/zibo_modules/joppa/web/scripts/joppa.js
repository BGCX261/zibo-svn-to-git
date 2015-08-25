function joppaInitializeActionMenus() {
    $("#createMenuAnchor").contextMenu(
    		{ 
    			menu: 'createMenuActions',
    			inSpeed: 25,
    			outSpeed: 25,
    			enableLeftClick: true
			}, 
    		function(action, el, pos) {
				document.location = 'h' + action;
			}
	);	
}

function joppaInitializeAdvanced() {
	$("#nodeSettingsLink").click(function() {
		$('#nodeSettings').slideToggle("fast");
		return false;
	});
}
	
function joppaInitializeContent(basePath, widgetDeleteMessage) {
	$('#widgets .widgetNamespace').each(function() {
		$(this).find('ul').each(function() {
			$(this).hide();
		});
		
		$(this).find('a.namespace').click(function() {
			$('#widgets .widgetNamespace ul').each(function() {
				$(this).slideUp();
			});
			$(this).parent().find('ul').first().slideToggle();
		});
	});
	$('#widgets .widgetNamespace:first ul').first().show();
	
	$('#widgets .widget').draggable({
		helper: 'clone',
		cursor: 'move',
		activeClass: 'ui-state-hover',
		connectToSortable: '.droppable'
	});
	
	contextMenuCallback = function(action, el, pos) {
		if (action.substr(0, 6) == 'delete') {
			if (confirm(widgetDeleteMessage)) {
				$.ajax({url: basePath + action});
				$(el).parent().parent().parent().remove();
			}
		} else {
			document.location = basePath + action;
		}
	};
 
    $("#content ul.droppable li.widget div.icon a.actions").each(function() {
    	$(this).contextMenu(
			{ 
				menu: $(this).parent().parent().find('ul.contextMenu').first().attr('id'),
				inSpeed: 0,
				outSpeed: 0,
				enableLeftClick: true
			},
			contextMenuCallback
		);
    });
	
	$('#content ul.droppable').droppable({
		helper: 'clone',
		drop: function (event, ui) {
			id = ui.draggable.context.id;
			id = id.split('_');
			if (id[0] == 'widget') {
				$.ajax({
					  url: basePath + 'add/' + id[1] + '/' + id[2],
					  success: function(data) {
					    $('ul.droppable').append(data);
					    var widget = $('#content ul.droppable li.widget:last div.icon a.actions').first();
					    widget.contextMenu(
					    		{ 
					    			menu: $(widget).parent().parent().find('ul.contextMenu').first().attr('id'),
					    			inSpeed: 0,
					    			outSpeed: 0,
					    			enableLeftClick: true
				    			}, 
					    		contextMenuCallback
			    		);
					  }
					});				
			}
		}
	});
	
	$('#content ul.droppable').sortable({
		containment: 'parent',
		handle: 'img.handle',
		update: function (event, ui) {
			id = ui.item.context.id;
			id = id.split('_');
			if (id[0] != 'pageWidget') {
				return;
			}
			var order = '';
			$('.droppable > li').each(function(i) {
				order += this.id.replace('pageWidget_', '') + ',';
			});
			
			$.ajax({ url: basePath + 'order/' + escape(order) });
	    } 			
	});
}