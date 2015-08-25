function ziboAdminInitializeLocalizePanel() {
	$('#formLocalizeLocale').change(function () {
		this.form.submit();
	});
}