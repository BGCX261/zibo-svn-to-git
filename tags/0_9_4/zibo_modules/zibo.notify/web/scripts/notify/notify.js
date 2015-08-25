var ziboNotifyIsClicked = false;
var ziboNotifyUnloadMessage = null;

function ziboNotifyInitialize(unloadMessage, limitUnloadMessage) {
	if ($('#notifyIcon').hasClass('active')) {		
		ziboNotifyIconFadeOut();
		
		if (unloadMessage) {
			ziboNotifyUnloadMessage = unloadMessage;			

			$(window).bind('beforeunload', ziboNotifyUnloadBody);
			$('#userPanel a.logout').click(ziboNotifyUnloadLogout);
			
			if (limitUnloadMessage) {
				$('a').click(function() {
					ziboNotifyUnloadMessage = null;
					return true;
				});
				
				$('form').submit(function() {
					ziboNotifyUnloadMessage = null;
					return true;
				});
			}
		}
	}
		
	$("#notifyIcon").click(function () {
		if (!ziboNotifyIsClicked) {
			ziboNotifyUnloadMessage = null
		}
		
		ziboNotifyIsClicked = true;

		// control positioning and add show/hide control through click
		var notifyContainer = $("#notifyContainer");
		if (notifyContainer.is(":visible")) {
			$(document).unbind('click', ziboNotifyCleanup);
		} else {
			$(document).bind('click', ziboNotifyCleanup);
		}
		notifyContainer.slideToggle("fast");

		var notifyPosition = notifyContainer.position();
		var windowWidth = $(window).width();
		if (windowWidth - (notifyPosition.left + 284 + 12) < 12) {
			notifyContainer.css('left', windowWidth - (284 + 12));
		}
		
		return false;
	});
}

function ziboNotifyIconFadeIn() {	
	$('#notifyIcon').fadeTo(1000, 1, ziboNotifyIconFadeOut);
}

function ziboNotifyIconFadeOut() {
	if (!ziboNotifyIsClicked) {
		$('#notifyIcon').fadeTo(1000, 0.50, ziboNotifyIconFadeIn);
	}
}

function ziboNotifyCleanup() {
	var notifyContainer = $("#notifyContainer");
	
	if (!notifyContainer.is(":visible")) {
		return;
	}
	
	notifyContainer.slideToggle("fast");

	$(document).unbind('click', ziboNotifyCleanup);
}

function ziboNotifyUnloadBody(event) {
	if (ziboNotifyUnloadMessage) {
		return ziboNotifyUnloadMessage;
	}
}

function ziboNotifyUnloadLogout(event) {
	if (!ziboNotifyUnloadMessage) {
		return true;
	}
	
	return confirm(ziboNotifyUnloadMessage);
}