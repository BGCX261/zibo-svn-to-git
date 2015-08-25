function joppaContentInitializeDetailProperties(fieldsAction) {
	$("#formContentPropertiesModel").change(function() {
		joppaContentUpdateFields(fieldsAction);
	});
	
	$('#formContentPropertiesTabs').tabs();	
}	

function joppaContentInitializeOverviewProperties(fieldsAction, orderFieldsAction) {
	$("#formContentPropertiesModel").change(function() {
		joppaContentUpdateFields(fieldsAction);
		joppaContentUpdateOrderFields(orderFieldsAction);
		$("#formContentPropertiesConditionExpression").val('');
		$("#formContentPropertiesOrderExpression").val('');
	});
	
	$("#formContentPropertiesRecursiveDepth").change(function() {
		joppaContentUpdateOrderFields(orderFieldsAction);
	});
	
	$("#formContentPropertiesOrderAdd").click(function() {
		var orderField = $("#formContentPropertiesOrderField").val();
		if (!orderField) {
			return false;
		}
		
		orderField = '{' + orderField + '} ' + $("#formContentPropertiesOrderDirection").val();
		
		var orderExpression = $("#formContentPropertiesOrderExpression").val();
		
		if (orderExpression) {
			orderExpression += ', ';
		}
		orderExpression += orderField;
		
		$("#formContentPropertiesOrderExpression").val(orderExpression);
		$("#formContentPropertiesOrderField").val('');
		
		return false;
	});
	
	$("#formContentPropertiesPaginationEnable").change(function() {
		var paginationValue = $("#formContentPropertiesPaginationEnable").val(); 
		
		var suffix = paginationValue != '1' ? ':visible' : ':hidden';
		$("#formContentProperties #tabQuery .paginationAttribute" + suffix).each(function() {			
			$(this).slideToggle('fast');
		});
		
		$("#formContentProperties #tabView .paginationAttribute").each(function() {
			var displayAttribute = $(this).css('display');
			if (paginationValue == '1' && displayAttribute == 'none') {
				$(this).show();
			} else if (paginationValue != '1' || displayAttribute != 'none') {
				$(this).hide();
			}
		});
		
		if ($('#formContentPropertiesPaginationShow').val() != '1') {
			$("#formContentProperties .paginationAjax").first().hide();
		} else {
			if (paginationValue == '1') {
				$("#formContentProperties .paginationAjax").first().show();
			} else {
				$("#formContentProperties .paginationAjax").first().hide();
			}
		}

		if ($('#formContentPropertiesMoreShow').val() != '1') {
			$("#formContentProperties .moreAttribute").each(function() {
				$(this).hide();
			});
		} else {
			$("#formContentProperties .moreAttribute").each(function() {
				if (paginationValue == '1') {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	});
	
	$("#formContentPropertiesPaginationShow").change(function() {
		var suffix = $("#formContentPropertiesPaginationShow").val() != '1' ? ':visible' : ':hidden';
		$("#formContentProperties .paginationAjax" + suffix).first().slideToggle('fast');
	});	
	
	$("#formContentPropertiesMoreShow").change(function() {
		var moreShow = $("#formContentPropertiesMoreShow").val(); 
		var suffix = moreShow != '1' ? ':visible' : ':hidden';
		$("#formContentProperties #tabView .moreAttribute" + suffix).each(function() {			
			$(this).slideToggle('fast');
		});
	});
	
	if ($("#formContentPropertiesPaginationEnable").val() != '1') {
		$("#formContentProperties .paginationAttribute").each(function() {
			$(this).hide();
		});
	}	

	if ($("#formContentPropertiesMoreShow").val() != '1') {
		$("#formContentProperties .moreAttribute").each(function() {
			$(this).hide();
		});
	}	

	if ($("#formContentPropertiesPaginationShow").val() != '1') {
		$("#formContentProperties .paginationAjax").first().hide();
	}
	
	$('#formContentPropertiesTabs').tabs();	
}

function joppaContentUpdateFields(fieldsAction) {
	var model = $("#formContentPropertiesModel").val();
	
	$.getJSON(fieldsAction + model, function(data) {
		var select = $("#formContentPropertiesFields");
		select.empty();
		for (var key in data.fields) {
			if (data.fields.hasOwnProperty(key)) {
				select.append('<option value="' + key + '">' + data.fields[key] + '</option>');
			}
		}
		select.val('');
	});	
}

function joppaContentUpdateOrderFields(orderFieldsAction) {
	var model = $("#formContentPropertiesModel").val();
	var recursiveDepth = $("#formContentPropertiesRecursiveDepth").val();
	
	$.getJSON(orderFieldsAction + model + '/' + recursiveDepth, function(data) {
		var select = $("#formContentPropertiesOrderField");
		select.empty();
		select.append('<option value="">---</option>');
		for (var key in data.fields) {
			if (data.fields.hasOwnProperty(key)) {
				select.append('<option value="' + key + '">' + data.fields[key] + '</option>');
			}
		}
		select.val('');
		$("#formContentProperties .orderDirection:visible").first().slideToggle('fast');
	});	
}