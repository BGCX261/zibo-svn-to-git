var dashboardCloseAction = null;
var dashboardMinimizeMaximizeAction = null;
var dashboardOrderAction = null;
var dashboardSaveAction = null;
var dashboardSaveMessage = null;

function dashboardInitialize() {
	var columnMargin = 2;
	var columnCount = $('#dashboard .column').size();
	var columnWidth = (100 - ((columnCount - 1) * columnMargin)) / columnCount;
	var columnIndex = 1;
	$('#dashboard .column').each(function() {
		$(this).width(columnWidth + '%');
		if (columnIndex != columnCount) {
			$(this).css('margin-right', columnMargin + '%');
		} else {
			$(this).css('margin-right', 0);
		}
			
		columnIndex++;
	});
	$('#dashboard .column').sortable({  
		connectWith: '.column',  
		handle: 'div.titleBar',  
		cursor: 'move',  
		placeholder: 'placeholder',  
		forcePlaceholderSize: true,  
		opacity: 0.4,  
		stop: function(event, ui){  
			ui.item.css({'top':'0','left':'0'}); //Opera fix
			dashboardSaveWidgetOrder();	
		}  
	})  
	.disableSelection(); 	
}

function dashboardSaveWidgetOrder(){
	var data = '';
	$('.column').each(function() {
		data += $(this).attr('id') + ': ';
		$('.widget', this).each(function(i) {
			data += $(this).attr('id') + ',';
		});
		data = data.substr(0, data.length - 1);
		data += ';';
	});

	$.post(dashboardOrderAction, 'order=' + data);

	//Pass sortorder variable to server using ajax to save state
//	$.post('updatePanels.php', 'data='+$.toJSON(sortorder), function(response){
//		if(response=="success")
//			$("#console").html('<div class="success">Saved</div>').hide().fadeIn(1000);
//		setTimeout(function(){
//			$('#console').fadeOut(1000);
//		}, 2000);
//	});
}

function dashboardAddWidget(namespace, name, aElement) {
	if (!dashboardSaveAction) {
		alert("Could not add the widget: no save action assigned");
		return;
	}
	
	$.get(dashboardSaveAction + namespace + '/' + name, {}, function(response) {
		if (response == "OK") {
			$(aElement).after('<div class="success">' + dashboardSaveMessage + '</div>');
			$(aElement).parent().find('div.success').hide().fadeIn(1000);
			setTimeout(function() {
				$(aElement).parent().find('div.success').fadeOut(1000).remove();
			}, 2000);
		}
	});
}

function dashboardCloseWidget(widgetId) {
	if (!dashboardCloseAction) {
		alert("Could not close the widget: no close action assigned");
		return false;
	}
	
	$('#widget' + widgetId).remove();
	$.get(dashboardCloseAction + widgetId);
	
	return false;
}

function dashboardMinimizeMaximizeWidget(widgetId) {
	if (!dashboardMinimizeMaximizeAction) {
		alert("Could not add the widget: no minimize/maximize action assigned");
		return false;
	}
	
	if ($('#widget' + widgetId + " div.content").is(":visible")) {
		var aElement = $('#widget' + widgetId + " div.titleBar div.buttons a.minimize");
		aElement.removeClass('minimize');
		aElement.addClass('maximize');
	} else {
		var aElement = $('#widget' + widgetId + " div.titleBar div.buttons a.maximize");
		aElement.removeClass('maximize');
		aElement.addClass('minimize');
	}
	
	$('#widget' + widgetId + " div.content").toggle();	
	$.get(dashboardMinimizeMaximizeAction + widgetId);
	
	return false;
}