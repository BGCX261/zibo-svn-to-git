function bbcodeInsertAtCursor(fieldId, value) {
	field = $('#' + fieldId);
	if (document.selection) {
		//IE support
		field.focus();
		sel = document.selection.createRange();
		sel.text = value;
	} else if (field.selectionStart || field.selectionStart == '0') {
		//MOZILLA/NETSCAPE support
		var startPos = field.selectionStart;
		var endPos = field.selectionEnd;
		field.value = field.value.substring(0, startPos) + value + field.value.substring(endPos, field.value.length);
	} else {
		field.value += value;
	}
	
	return false;
}

var isIE = (document.attachEvent)? true : false;

function bbcodeAddColor(color) { 
    var color = (color) ? color : "";
    bbcodeAdd("[color=", "[/color]", color);
}

function bbcodeAdd(idField, open, end) { 
	var tArea = document.getElementById(idField);
	
    var sct = tArea.scrollTop;
    var open = (open) ? open : "";
    var end = (end) ? end : "";
    var sl;
    
    if (isIE) { 
        tArea.focus();
        var curSelect = document.selection.createRange();
        if (arguments[3]) { 
            curSelect.text = open + arguments[2] + "]" + curSelect.text + end;
        } else { 
            curSelect.text = open + curSelect.text + end;
        }
    } else if (!isIE && typeof tArea.selectionStart != "undefined") { 
        var selStart = tArea.value.substr(0, tArea.selectionStart);
        var selEnd = tArea.value.substr(tArea.selectionEnd, tArea.value.length);
        var curSelection = tArea.value.replace(selStart, "").replace(selEnd, "");
        if (arguments[3]) { 
            sl = selStart + open + arguments[2] + "]" + curSelection + end;
            tArea.value = sl + selEnd;
        } else {
            sl = selStart + open + curSelection + end;
            tArea.value = sl + selEnd;
        }
        tArea.setSelectionRange(sl.length, sl.length);
        tArea.focus();
        tArea.scrollTop = sct;
    } else {
        tArea.value += (arguments[2])? open + arguments[2] + "]" + end : open + end;
    }
    
    return false;
}