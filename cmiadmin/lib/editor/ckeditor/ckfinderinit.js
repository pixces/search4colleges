//Inits the ckfinder and ckeditor
function init_ckfinder(textarea){
	var area = textarea;
	CKEDITOR.replace(area,
				{
					filebrowserBrowseUrl : 'lib/editor/ckeditor/ckfinder/ckfinder.html',
					filebrowserImageBrowseUrl : 'lib/editor/ckeditor/ckfinder/ckfinder.html?Type=Images',
					filebrowserFlashBrowseUrl : 'lib/editor/ckeditor/ckfinder/ckfinder.html?Type=Flash',
					filebrowserUploadUrl : 'lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
					filebrowserImageUploadUrl : 'lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
					filebrowserFlashUploadUrl : 'lib/editor/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
				});
}