zibo = function() {
	
	terminal = function() {
		
		var scrollOffset = 15;
		var scrollOutputHeight = 380;
		
		function init() {
			$('#formTerminal').submit(submitForm);
			$('#formTerminal').click(focus);
			
			updateCommandInput();
			focus();
		}
		
		function focus() {
			$('#formTerminalCommand').focus();
		}
		
		function submitForm() {
			var form = $('#formTerminal');
			var url = form.attr('action');
			var data = form.serialize();
			
			$.ajax({
				'type': 'POST',
				'url': url,
				'data': data,
				'dataType': 'json',
				'success': function(data) {
					var resultContainer = $("#formTerminal div.result");
					var scrollPosition = resultContainer.scrollTop();
					var scrollHeight = resultContainer[0].scrollHeight;
					
					var result = $('#formTerminal pre.result');
					var path = $('#formTerminal pre.bash span.path');

					var previousResult = result.html();
					if (previousResult) {
						previousResult += "\n";
					} else {
						result.css('margin-top', '0.3em');
						$('#formTerminal div.bash').css('margin-top', '-0.155em');
					}
					
					if (data.output) {
						if (data.error) {
							var output = "\n<span class=\"error\">" + data.output + "</span>";
						} else {
							var output = "\n" + data.output;
						}
					} else {
						var output = '';
					}
					
					result.html(previousResult + '<span class="path">' + path.html() + '</span> $ ' + data.command + output);
					path.html(data.path);
					$('#formTerminalCommand').val('');
					updateCommandInput();
					
					// if scrollbar was at the end, autoscroll the new messages
					if ((scrollHeight - scrollOffset) <= (scrollPosition + scrollOutputHeight)) {
						resultContainer.ready(function() {
							resultContainer.scrollTop(resultContainer[0].scrollHeight);
						});
					}					
				}
			});	
			
			return false;
		}
		
		function updateCommandInput() {
			var width = $('#formTerminal').width() - $('#formTerminal pre.bash').width() - 25;
			$('#formTerminalCommand').css('width', width + 'px');
		}
		
		return {
			init: init,
			focus: focus,
			submit: submitForm
		}
		
	}();
	
	return {
		terminal: terminal
	}
	
}();