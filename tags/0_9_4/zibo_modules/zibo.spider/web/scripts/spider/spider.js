function ziboSpiderInitialize(statusUrl, reportUrl) {
	if ($('#formSpiderUrl').val()) {
			setTimeout(function() {
				ziboSpiderUpdateStatus(statusUrl, reportUrl);
			}, 
			500
		);
	} else {
		$('#formSpiderCancel').attr('disabled', 'disabled');
	}
	
	$('div.advancedButton a').click(function() {
		$("div.advanced").slideToggle();
	});
	
	$('#formSpiderUrl').focus();

	$('#formSpiderSubmit').click(function() {
		$('#spider .loading:hidden').show();

		$('#spider div.advanced:visible').hide();
		$('#spider div.report').html('');
		
		$.ajax({
			type: "POST",
			url: $('#formSpider').attr('action'),
			data: '__submitformSpider=1&url=' + $('#formSpiderUrl').val() + '&delay=' + $('#formSpiderDelay').val() + '&ignore=' + $('#formSpiderIgnore').val() + '&submit=1',
			success: function() {
				$('#formSpiderUrl').attr('disabled', 'disabled');
				$('#formSpiderSubmit').attr('disabled', 'disabled');
				$('#formSpiderCancel').attr('disabled', '');
				
				setTimeout(function() {
						ziboSpiderUpdateStatus(statusUrl, reportUrl);
					}, 
					1500
				);
			}
		});
		return false;
	});

	$('#formSpiderCancel').click(function() {
		$.ajax({
			type: "POST",
		    url: $('#formSpider').attr('action'),
		    data: '__submitformSpider=1&cancel=1'
		});
		return false;
	});
}

function ziboSpiderUpdateStatus(statusUrl, reportUrl) {
//	$('#spider p.url:visible').hide();
	
	$.getJSON(statusUrl, function(data) {
		if (!data.empty) {
			$('#spider div.status:hidden').show();
			
			if (data.current) {
				$('#spider div.status .url').html(data.current);
				$('#spider div.status p.current:hidden').show();
			} else {
				$('#spider div.status p.current:visible').hide();
			}
			
			$('#spider div.status .visited').html(data.visited);
			$('#spider div.status .gathered').html(data.gathered);
			$('#spider div.status .elapsed').html(data.elapsed);
			
			if (data.finished == "1") {
				$('#spider div.report').load(reportUrl, function() {
					$('#spider .loading:hidden').show();
					$('#formSpiderUrl').attr('disabled', '');
					$('#formSpiderSubmit').attr('disabled', '');
					$('#formSpiderCancel').attr('disabled', 'disabled');
					
					$('#spiderReport').tabs();
					
					$('#spiderReport table tr.detail:visible').hide();
					$('#spiderReport table tr td.url a').click(function() {
						var url = $(this).attr('href');
						var dialog = $('<div></div>');

						dialog.load(reportUrl + '/?url=' + encodeURIComponent(url), function() {
							dialog.dialog({ title: url, width: '80%', height: 600});
							$('#spider .loading:visible').hide();
						});
						
						return false;
					});
					
					$('#spider .loading:visible').hide();
				});
			} else {
				setTimeout(function() {
						ziboSpiderUpdateStatus(statusUrl, reportUrl);
					}, 
					900
				);
			}
		} else {
//			$('#spider p.url:hidden').show();
			
			$('#formSpiderUrl').attr('disabled', '');
			$('#formSpiderSubmit').attr('disabled', '');
			$('#formSpiderCancel').attr('disabled', 'disabled');
			
			$('#spider div.status:visible').hide();
			
			$('#spider .loading:visible').hide();
		}
	});
}