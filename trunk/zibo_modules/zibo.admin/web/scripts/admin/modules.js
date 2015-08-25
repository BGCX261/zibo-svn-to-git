function ziboAdminInitializeModules() {
	$("#formModulesTable div.info a").each(function(i) {
		$(this).click(function() {
			var id = this.id + "List";
			id = id.replace(/[:\[\]\.]/g,"\\$&");

			$('#' + id).slideToggle("fast");
			return false;
		});
	});
}