/*
VALSIX TECHNOLOGY
*/
// wait for document to load
$(function(){
  
  // 2 jpgs under 100kb only

$('.multiupload').MultiFile({
	afterFileAppend: function(element, value, master_element) {
		
		
		var variable = master_element.E.data("variable");
		var total = ($("input[name="+variable+"]").length);

		$("input[name="+variable+"]").each(function( index ) {
			if((index) == total - 2)
			{
				 elementUpload = $(this);

				 if(elementUpload[0].files.length == 0)
				 	elementUpload = master_element.E;
				 	
				 var dokumenmanagerid = master_element.E.data("dokumenmanagerid");
				 var dokumenid = master_element.E.data("dokumenid");
				 var dataid = master_element.E.data("dataid");
				 var jenisid = master_element.E.data("jenisid");
				 var urutDokumen = master_element.E.data("urut");

				 var prefix = master_element.E.data("prefix");
				 var index  = master_element.E.data("id")+''+element.ke;
				 uploadProgress(elementUpload,dokumenmanagerid,urutDokumen,index,dokumenid,jenisid, prefix,variable,dataid);
			}
		});

	},
	afterFileRemove: function(element, value, master_element) {
		var elementId = master_element.E.attr("id");
		var elementIndex = master_element.E.data("id");
		var elementName = master_element.E.data("variable");
		if($("#"+elementId+"_list > div").length == 0)
		{
			$("#"+elementName+"Wajib"+elementIndex).val("");	
			$("#"+elementName+"Wajib").focus();
		}
			
	}
  });
});


function uploadProgress(element,dokumenManagerId,urutDokumen,index,jenisDokumen,jenisDokumenId,namaDokumen,variable,dataid)
{
	//configuration
	var allowed_file_types 		= ['application/pdf']; //allowed file types
	var result_output 			= '#output'+index; //ID of an element for response output
	var my_form_id 				= '#upload_form'+index; //ID of an element for response output
	var progress_bar_id 		= '#progress-wrp'+index; //ID of an element for response output
	//on form submit

	var proceed = true; //set proceed flag
	var error = [];	//errors
	var total_files_size = 0;
	
	//reset progressbar
	$("#progress-bar"+index).css("width", "0%");
	$("#status"+index).text("0%");

	if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
	
		error.push("Your browser does not support new File API! Please upgrade."); //push error text
	}else{
		
		$("#progressBar"+index).show();
		
		var formData = new FormData();
		formData.append('reqLinkFile', element[0].files[0]);
		formData.append('reqJenisDokumen', jenisDokumen);
		formData.append('reqDokumenId', jenisDokumenId);
		formData.append('reqNamaDokumen', namaDokumen);
		formData.append('reqUrutDokumen', urutDokumen);
		formData.append('reqId', dataid);
		formData.append('reqUnik', dokumenManagerId);

		
		var reqToken = $('#reqToken').val();

		//jQuery Ajax to Post form data
		$.ajax({
			url : "uploads_json/upload_temporary_rab/",
			type: "POST",
			data : formData,
			contentType: false,
			cache: false,
			processData:false,
			xhr: function(){
				//upload Progress
				var xhr = $.ajaxSettings.xhr();
				if (xhr.upload) {
					xhr.upload.addEventListener('progress', function(event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
						}
						//update progressbar
						$("#progress-bar"+index).css("width", + percent +"%");
						$("#status"+index).text(percent +"%");
					}, true);
				}
				return xhr;
			},
			mimeType:"multipart/form-data"
		}).done(function(res){ //
			var obj = JSON.parse(res); 
			if(obj.result == "failed")
			{
				$("#multiFileRemove"+index).click();
				alert(obj.message);
			}
			else
			{
				$("#reqLinkFileRABTemp"+namaDokumen).val(obj.file);				
				$("#divButton"+namaDokumen).html(`<button type="button" onclick="openAdd('uploads/penawaran/`+obj.file+`')">Lihat Dokumen</button>`);
				$("#progressBar"+index).hide();
			}
		});
	}
}
	