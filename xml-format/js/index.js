/** openFullManga Function
	Desc: Opens the specific manga into "focused view"
	Param: ele - The element holding the JSON string to be parsed
	Output: Display populated "focused view"
*/
function openFullManga(ele) {
  	var hold = "";
  	const data = JSON.parse(ele.innerHTML);
  	// Cover Operation
  	var popupedit = document.getElementById("fm_poster");
  	popupedit.innerHTML = "<img src=" + data.cover + " alt=" + data.name[0] + " class=\"poster\"/>";
  	// Titles Operation
  	popupedit = document.getElementById("fm_titles");
  	hold = "<strong>Primary Title:</strong> " + data.name[0] + " <br>";
  	for (let i = 1; i < data.name.length; i++) { hold += "<strong>Alternative Title:</strong> " + data.name[i] + " <br>"; }
  	popupedit.innerHTML = hold;
  	// Descriptors Operation (Tags & Chapter Number)
  	hold = "";
  	popupedit = document.getElementById("fm_descriptors");
  	for (let t of data.tag) {
      switch (t.category) {
        case "genre": hold += "<span class=\"genre_label\">" + t.tag + "</span> "; break;
        case "mature": hold += "<span class=\"maturity_label\">" + t.tag + "</span> "; break;
        case "status": hold += "<span class=\"status_label\">" + t.tag + "</span> "; break;
        default: hold += "<span class=\"maturity_label\">" + t.tag + "</span> "; break;
      }
    }
  	popupedit.innerHTML = "<br>" + hold + "<br><button onclick='location.href=\"html/edit_xml.php?index=" + data.index + "\"; return false;' class='btn-sm btn-warning' style='float:right;'>Edit Manga</button><br><p><strong>Chapter: </strong>" + data.chapter + "</p><br>";
  	// Links Operation
  	hold = "";
  	const row = 3;
  	if (data.links.length <= row) {
      popupedit = document.getElementsByClassName("fm_links")[0];
      let count = 0;
      for (let l of data.links) {
        hold += "<td><img src=\"" + l.icon + "\" alt=\"" + l.site + "\" class=\"icon\">\n<a href=\"" + l.link + "\" target=\"_blank\">" + l.site + "</a></td>";
        count++;
      }
      if (count < row) { for (; count <= row; count++) { hold += "<td></td>"; } }
      popupedit.innerHTML = hold;
    }
 	else {
      popupedit = document.getElementsByClassName("fm_links")[0];
      for (let i = 0; i < row; i++) {
        hold += "<td><img src=\"" + data.links[i].icon + "\" alt=\"" + data.links[i].site + "\" class=\"icon\">\n<a href=\"" + data.links[i].link + "\" target=\"_blank\">" + data.links[i].site + "</a></td>";
      }
      popupedit.innerHTML = hold;
      popupedit = document.getElementByClassName("fm_links")[1];
      let count = 0;
      for (let l of data.links) {
        hold += "<td><img src=\"" + l.icon + "\" alt=\"" + l.site + "\" class=\"icon\">\n<a href=\"" + l.link + "\" target=\"_blank\">" + l.site + "</a></td>";
        count++;
      }
      if (count < row) { for (; count <= row; count++) { hold += "<td></td>"; } }
      popupedit.innerHTML = hold;
    }
  	// Display Full Manga
  	document.getElementById("full_manga").style.display = "block";
  	document.getElementById("fm_catch").style.display = "block";
}
/** closeFullManga Function
	Desc: Closes the "focused view"
	Param: None
	Output: Hide "focused view"
*/
function closeFullManga() {
	document.getElementById("full_manga").style.display = "none";
  	document.getElementById("fm_catch").style.display = "none";
}
/** validateImage Function
	Desc: Checks if the image has loaded without errors
	Param: img - The image element
	Output: 'True' if a valid image, otherwise 'False'
*/
function validateImage(img) {
    if (!img.complete) {
        return false;
    }
    if (typeof img.naturalWidth != "undefined" && img.naturalWidth == 0) {
        return false;
    }
    return true;
}
/** Window Load Event
	Desc: Checks all covers and icons for image errors on the window loading
*/
window.addEventListener("load",(event) => {
  	var icons = document.getElementsByClassName("icon");
  	var covers = document.getElementsByClassName("poster");
  	for (var i = 0; i < icons.length; i++) {
        if (!validateImage(icons[i])) {
            icons[i].src = "css/images/no-favicon.png";
        }
    }
  	for (var i = 0; i < covers.length; i++) {
        if (!validateImage(covers[i])) {
            covers[i].src = "css/images/no-cover.png";
        }
    }
});