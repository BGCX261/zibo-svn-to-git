var fileCounter = 1;

function updateUploadForm(inputUploadFile) {	
	if (inputUploadFile == null || inputUploadFile.value == '') {
		return;
	}
	
	var divUploadFile = $(inputUploadFile).parent();
	if (divUploadFile == null) {
		return;
	}
	
	inputUploadFile.onchange = '';
	divUploadFile.after('<div><input name="file[]" type="file" onchange="updateUploadForm(this)" /></div>');
	fileCounter++;
}
