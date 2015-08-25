function ziboOrmInitializeModelAdvanced() {
	$("#wizardModel #advancedAnchor").toggle(
		function () {
			$('#wizardModel div.advanced').slideDown('fast');
			return false;
		},
		function () {
			$('#wizardModel div.advanced').slideUp('fast');
			return false;
		}
	);	
}

function ziboOrmInitializeBuilderWizardGeneral() {
	$("#wizardModelModelName").focus();
	
	if (!$("#wizardModelModelName").val()) {
		$("#wizardModelNext").attr("disabled", true);
	}
	
	$("#wizardModelModelName").bind('keyup change', function() {
		$("#wizardModelNext").attr("disabled", $("#wizardModelModelName").val() ? false : true);
	});
			
	ziboOrmInitializeModelAdvanced();
}

function ziboOrmInitializeBuilderWizardField(autoDisableNext, foreignKeyFieldsAction) {
	$("#wizardModelFieldName").focus();
	
	if (autoDisableNext) {
		if (!$("#wizardModelFieldName").val()) {
			$("#wizardModelNext").attr("disabled", true);
		}
	
		$("#wizardModelFieldName").bind('keyup change', function() {
			$("#wizardModelNext").attr("disabled", $("#wizardModelFieldName").val() ? false : true);
		});
	}
	
	if ($("#wizardModelFieldTypeProperty:checked").val() == 'property') {
		$('#wizardModel div.property:hidden').slideDown('fast');
	}
	$("#wizardModelFieldTypeProperty").change(function() {
		if ($("#wizardModelFieldTypeProperty:checked").val() == 'property') {
			$('#wizardModel div.relation:visible').slideUp('fast');
			$('#wizardModel div.property:hidden').slideDown('fast');
		} else {
			$('#wizardModel div.property:visible').slideUp('fast');
			$('#wizardModel div.relation:hidden').slideDown('fast');
		}
	});

	if ($("#wizardModelFieldTypeRelation:checked").val() == 'relation') {
		$('#wizardModel div.relation:hidden').slideDown('fast');
	}
	$("#wizardModelFieldTypeRelation").change(function() {
		if ($("#wizardModelFieldTypeProperty:checked").val() == 'property') {
			$('#wizardModel div.relation:visible').slideUp('fast');
			$('#wizardModel div.property:hidden').slideDown('fast');
		} else {
			$('#wizardModel div.property:visible').slideUp('fast');
			$('#wizardModel div.relation:hidden').slideDown('fast');
		}
	});
	
	if ($("#wizardModelRelationType").val() == '3') {
		$('#wizardModel div.relationLinkModel:hidden').slideDown('fast');
		$('#wizardModel div.relationForeignKey:hidden').slideDown('fast');
	}

	if ($("#wizardModelRelationType").val() == '4') {
		$('#wizardModel div.relationLinkModel:hidden').slideDown('fast');
		$('#wizardModel div.relationForeignKey:hidden').slideDown('fast');
		$('#wizardModel div.relationOrder:hidden').slideDown('fast');
	}

	$("#wizardModelRelationType").change(function() {
		if ($("#wizardModelRelationType").val() == '2') {
			$('#wizardModel div.relationAdvanced:visible').slideUp('fast');
			$('#wizardModel div.relationLinkModel:visible').slideUp('fast');
			$('#wizardModel div.relationForeignKey:visible').slideUp('fast');
			$('#wizardModel div.relationOrder:visible').slideUp('fast');
		} else if ($("#wizardModelRelationType").val() == '3') {
			$('#wizardModel div.relationAdvanced:hidden').slideDown('fast');
			$('#wizardModel div.relationLinkModel:hidden').slideDown('fast');
			$('#wizardModel div.relationForeignKey:hidden').slideDown('fast');
			$('#wizardModel div.relationOrder:visible').slideUp('fast');
		} else if ($("#wizardModelRelationType").val() == '4') {
			$('#wizardModel div.relationAdvanced:hidden').slideDown('fast');
			$('#wizardModel div.relationLinkModel:hidden').slideDown('fast');
			$('#wizardModel div.relationForeignKey:hidden').slideDown('fast');
			$('#wizardModel div.relationOrder:hidden').slideDown('fast');
		}
	});
	
	$("#wizardModelRelationModel").change(function() {
		$.getJSON(foreignKeyFieldsAction + $("#wizardModelRelationModel").val(), function(data) {
			var select = $("#wizardModelRelationForeignKey");
			select.empty();
			for (var key in data.fields) {
				if (data.fields.hasOwnProperty(key)) {
					select.append('<option value="' + key + '">' + data.fields[key] + '</option>');
				}
			}
			select.val('');
		});
	});
	
	ziboOrmInitializeModelAdvanced();
}

function ziboOrmInitializeBuilderWizardFormat() {
	$("#wizardModelPredefined").change(function() {
		var predefined = $("#wizardModelPredefined").val();
		if (predefined != '') {
			$("#wizardModelPredefined").val('');
			$("#wizardModelName").val(predefined);
			$("#wizardModelFormat").focus();
		}
	});
	
	$("#wizardModelName").focus();
}

function ziboOrmInitializeBuilderWizardIndex() {
	$("#wizardModelIndexName").focus();
}