busy = false;
document.addEventListener("keypress", function(event){
	if (event.which == 117) {
		if (!busy) {
			busy = true;
			bootbox.dialog({
				title: "Upload Picture",
				message: '<form action="api.php" id="uploadForm" method="POST" enctype="multipart/form-data">' + 
					'<fieldset class="form-group">' +
					'<label for="key">Passwort</label>' +
					'<input type="password" class="form-control" id="key" name="key" placeholder="API Key">' +
					'</fieldset>' +
					'<fieldset class="form-group">' +
					'<label for="file">Bild</label>' +
					'<input type="file" class="form-control-file" id="file" name="file">' +
					'</fieldset>' + 
					'<input type="text" id="action" value="upload" name="action" style="display: none;">',
				buttons: {
					main: {
						label: "Upload",
						className: "btn-primary",
						callback: function() {
							busy = false;
							formdata = new FormData($("#uploadForm")[0]);
							$.ajax({
								type: "POST",
								url: "api.php",
								data: formdata,
								processData: false,
								contentType: false,
								success: function(res){
									console.log(res);
									console.log(!res.success);
									if (!res.success) {
										bootbox.alert(res.error);
									} else {
										bootbox.dialog({
											"title": "Erfolg",
											"message": "<a href='" + res.response.url + "'>" + res.response.url + "</a>"
										});
									}
								}
							});
						}
					}
				}
			});
		}
	}
});