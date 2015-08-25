function ZiboForm(formId, buttonTranslationLabel) {
	formId = '#' + formId;
	
	if (!buttonTranslationLabel) {
		buttonTranslationLabel = 'T';
	}
	
	$(formId + ' div ul.locales').each(function (i) {
		$(this).css('display', 'none');
		
		var label = $(this).parent().find('label').first();
		label.append(' (<a id="' + this.id + 'Anchor" href="#">' + buttonTranslationLabel + '</a>)');
		
		$('#' + this.id + 'Anchor').click(function() {
			var ul = this.id.substr(0, this.id.length - 6);

			$(formId + ' div ul.locales:not(#' + ul + ')').hide("fast");
			
			$('#' + ul).slideToggle("fast");
			return false;
		});
	});	
}