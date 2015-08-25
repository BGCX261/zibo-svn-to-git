function ziboOrmInitializeLog(showLabel, hideLabel, modelLogUrl) {
	$('#modelLogToggle').toggle(
		function() {
			var modelLog = $('#modelLog');

			if (modelLog.has('img')) {			
				modelLog.load(modelLogUrl, function() {
					$("#modelLog a").each(function(i) {
						$(this).toggle(
								function () {
									$(this).next().slideDown('fast');
									return false;
								},
								function () {
									$(this).next().slideUp('fast');
									return false;
								}
						);
					});
				});
			}				
			
			modelLog.slideDown('fast');
			
			$(this).html(hideLabel);
			
			return false;
		},
		function () {
			$('#modelLog').slideUp('fast');
			
			$(this).html(showLabel);
			
			return false;
		}
	);
}