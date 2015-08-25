function ZiboTable(id, messages, defaultSearchValue) {
	id = '#' + id;
	
	var searchInput = $(id + ' div.search input:first');
	if (!searchInput.val()) {
		searchInput.val(defaultSearchValue);
	}
	searchInput.focus(function () {
		if (this.value == defaultSearchValue) {
			this.value = '';
		}
	});
	searchInput.blur(function () {
		if (!this.value) {
			this.value = defaultSearchValue;
		}
	});
	$(id + ' div.search a:first').click(function() {
		$(id).submit();
	});
	
	$(id).bind('submit', {input: searchInput, defaultValue: defaultSearchValue}, function (event) {
		if (event.data.input.val() == event.data.defaultValue) {
			event.data.input.val('');
		}
		return true;
	});

	$(id + 'OrderMethod').change(function () {
		this.readonly = true;
		$(this.form).trigger('submit');
	});

	$(id + 'PageRows').change(function () {
		this.readonly = true;
		$(this.form).trigger('submit');
	});	
	
	$(id + 'Action').bind('change', {messages: messages}, function (event) {
		var submit = true;
		
		for (var i in event.data.messages) {
			if (this.options[this.selectedIndex].text == i) {
				submit = confirm(event.data.messages[i]);
			}
		}

		if (submit) {
			this.readonly = true;
			$(this.form).trigger('submit');
			$(this).after('<span class="loading"></span>'); //.remove();
		} else {
			$(this).val('');
		}
	});

	$(id + 'ActionAll').change(function () {
		var checked = this.checked;
		$(id + ' :checkbox').each(function(i) {
			this.checked = checked;
		});
	});
	
}