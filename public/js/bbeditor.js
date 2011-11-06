function insertCode(id, code)
{
	var elementText = document.getElementById(id);
	var orgText = elementText.value;

	var insText = new String(code);
	
	if(insertCode.arguments.length > 2) {
		for(var i = 2; i < insertCode.arguments.length; i++) {
			eval("insText = insText.replace(/\\$" + (i - 1) + "/g, insertCode.arguments[i]);");
		}
	}
	
	if(elementText.caretPos) {
		var sel = elementText.caretPos;
		insText = insText.replace(/\$0/g, sel.text);
		document.selection.createRange().text = insText;
	} else if (elementText.selectionStart && elementText.selectionEnd) {
		var selStart = elementText.selectionStart;
		var selLength = elementText.selectionEnd - elementText.selectionStart; 
		var selEnd = elementText.selectionEnd;
		var selText = elementText.value.substring(selStart, selEnd);

		insText = insText.replace(/\$0/g, selText);
		
		elementText.value = orgText.substr(0, selStart) + insText + orgText.substr(selEnd);
	} else {
		insText = insText.replace(/\$0/g, "");
		elementText.value += insText;
	}
}