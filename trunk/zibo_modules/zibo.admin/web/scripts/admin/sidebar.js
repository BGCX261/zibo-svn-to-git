function ziboAdminInitializeSidebar(isVisible, sidebarAction) {
	$('#content').addClass('sidebar');
	$("#sidebar #toggleButton").toggle(
		function() {
			ziboAdminToggleSidebar(isVisible, sidebarAction);
			return false;
		},
		function() {
			ziboAdminToggleSidebar(!isVisible, sidebarAction);
			return false;
		}
	);
	
	$(window).resize(ziboAdminResizeSidebar);
	ziboAdminResizeSidebar();
	
	if (!isVisible) {
		ziboAdminHideSidebar();
	}
}

function ziboAdminToggleSidebar(isVisible, sidebarAction) {
	if (isVisible) {
		ziboAdminHideSidebar();
		$.get(sidebarAction + '1');
	} else {
		ziboAdminShowSidebar();
		$.get(sidebarAction + '0');
	}		
}

function ziboAdminHideSidebar() {
	var content = $('#content');
	
	content.removeClass('sidebar');
	content.addClass('sidebarHidden');
	
	$('#sidebar').addClass('hidden');
}

function ziboAdminShowSidebar() {
	var content = $('#content');
	
	content.removeClass('sidebarHidden');
	content.addClass('sidebar');
	
	$('#sidebar').removeClass('hidden');
}

function ziboAdminResizeSidebar() {
	var sidebar = $('#sidebar');
	var content = $('#content');
	
	content.height('auto');
	sidebar.height('auto');
	
	var documentHeight = $(document).height();
	var windowHeight = $(window).height();
	var height = 0;
	
	if (documentHeight > windowHeight) {
		height = documentHeight;
	} else {
		height = windowHeight;
	}
	
	height = height - sidebar.offset().top;

	content.height(height - (parseInt(content.css('padding-top')) + parseInt(content.css('padding-bottom'))));
	sidebar.height(height - (parseInt(sidebar.css('padding-top')) + parseInt(sidebar.css('padding-bottom'))));		
}