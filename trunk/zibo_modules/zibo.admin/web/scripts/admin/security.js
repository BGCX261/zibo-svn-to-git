function ziboAdminInitializeSecurity() {
	$("#formSecurity > div.allowedRoutes ul li a").each(function(i) {
		$(this).click(function() {
			$('#' + this.id + "Field").slideToggle("fast");
			return false;
		});
	});
}