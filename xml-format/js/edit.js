$(document).ready(function(){
  	/** Genre Check Function
		Desc: Checks if a genre tag is checked, otherwise requires a genre tag to be checked
		Object: genreCheck - The genre tag checkboxes to be checked
		Output: 'genreCheck' not required and checked, otherwise required and not checked
	*/
  	var genreCheck = $(":checkbox[name='genretags[]']");
  	genreCheck.change(function(){
      	if (genreCheck.is(':checked')) { genreCheck.prop('required', false); }
      	else { genreCheck.prop('required', true); }
    });
  	/** Add Alt Function
		Desc: Adds text input elements for more "alternative names"
		Object: add_alt - The link element indicating the end of the "alternative names"
		Output: New elements made above the respective link element
	*/
  	$('#add_alt').on("click", function() {
      	var txt = "";
      	
      	if (!$(this).parent().has("span:contains('Alternative Manga Name(s)')").length) {
          	txt += "<span>Alternative Manga Name(s)</span><br>\n<label>Other searchable manga names.</label>";
        }
      	$(this).before(txt+"<input type='text' class='form-control form-control-sm space' name='altname[]' />\n");
    });
  	/** Add Link Function
		Desc: Adds the contents of a "link_pair" set of elements
		Object: add_link - The link element indicating the end of the link pairs
		Output: New elements made above the respective link element
	*/
  	$('#add_link').on("click", function() {
      	$(this).before("<div class='link_pair'>\n<input type='text' class='form-control form-control-sm' name='sites[]' placeholder='Associated Manga Site' />\n<input type='url' class='form-control form-control-sm' name='links[]' placeholder='https://' />\n</div>\n");
    });
  	/** Cover Container Function
		Desc: Toggles the contained input element's type and format
		Object: cvr_ctnr - The container containing elements associated with the manga's cover
		Output: The appropriate type and format for the input element
	*/
  	$('#cvr_ctnr').on("click", "a", function() {
      	var slt = $('#cvr_ctnr input[name="cover"]');
      	if(slt.attr('type') == 'file') {
          	slt.attr('placeholder', 'https://');
          	slt.attr('class', 'form-control form-control-sm');
          	slt.attr('type', 'url');
          	$(this).parent().html("link / <a href='#'>image</a>");
        }
      	else {
          	slt.removeAttr('placeholder');
          	slt.attr('class', 'form-control-file');
          	slt.attr('type', 'file');
          	$(this).parent().html("<a href='#'>link</a> / image");
        }
    });
    /** Icon Container Function
		Desc: Toggles the contained input element's type and format
		Object: icon_ctnr - The container containing elements associated with the manga site's icon
		Output: The appropriate type and format for the input element
	*/
  	$('#icon_ctnr').on("click", "a", function() {
      	var slt = $('#icon_ctnr input');
      	if(slt.attr('type') == 'file') {
          	slt.attr('placeholder', 'https://');
          	slt.attr('class', 'form-control form-control-sm');
          	slt.attr('type', 'url');
          	$(this).parent().html("link / <a href='#'>image</a>");
        }
      	else {
          	slt.removeAttr('placeholder');
          	slt.attr('class', 'form-control-file');
          	slt.attr('type', 'file');
          	$(this).parent().html("<a href='#'>link</a> / image");
        }
    });
  	/** Old Cover Function
		Desc: Toggles the input "cover" element's properties
		Object: oldcover - The checkbox to be checked
		Output: The "cover" element disabled, otherwise required
	*/
  	$("[name='oldcover']").change(function(){
      	var f = $("[name='cover']");
      	if ($(this).is(':checked')) {
        	f.prop("required",false);
        	f.prop("disabled",true);
      	}
      	else {
        	f.prop("required",true);
        	f.prop("disabled",false);
      	}
    });
});