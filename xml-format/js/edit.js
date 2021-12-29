$(document).ready(function(){
  	/***Genre Required Function***/
  	var genreCheck = $(":checkbox[name='gtags[]']");
  	genreCheck.change(function(){
      	if (genreCheck.is(':checked')) { genreCheck.prop('required', false); }
      	else { genreCheck.prop('required', true); }
    });
  	/***Add Alternative Name Function***/
  	$('#add_alt').on("click", function() {
      	var txt = "";
      	
      	if (!$(this).parent().has("span:contains('Alternative Manga Name(s)')").length) {
          	txt += "<span>Alternative Manga Name(s)</span><br>\n<label>Other searchable manga names.</label>";
        }
      	$(this).before(txt+"<input type='text' class='form-control form-control-sm space' name='altname[]' />\n");
    });
  	/***Add More Links Function***/
  	$('#add_link').on("click", function() {
      	$(this).before("<div class='link_pair'>\n<input type='text' class='form-control form-control-sm' name='sites[]' placeholder='Associated Manga Site' />\n<input type='url' class='form-control form-control-sm' name='links[]' placeholder='https://' />\n</div>\n");
    });
  	/***Toggle Cover Format Function***/
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
    /***Toggle Icon Format Function***/
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
  	/***Toggle Old Cover Function***/
  	$("[name='old']").change(function(){
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