function ziboAdminInitializeSystem() {
	$("#ziboConfigurationLink").click(function() {
		$("#ziboConfigurationData").slideToggle("fast");
		return false;
	});
	$("#system ul.phpExtensions a").each(function(i) {
		$(this).click(function() {
			$('#' + this.id + "Settings").slideToggle("fast");
			return false;
		});
	});
}
