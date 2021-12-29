function openFullManga(ele) {
  	var hold = "";
  	const data = JSON.parse(ele.innerHTML);
  	// Cover
  	var popupedit = document.getElementById("fm_poster");
  	popupedit.innerHTML = "<img src=" + data.cover + " alt=" + data.name[0] + " class=\"poster\"/>";
  	// Titles
  	popupedit = document.getElementById("fm_titles");
  	hold = "<strong>Primary Title:</strong> " + data.name[0] + " <br>";
  	for (let i = 1; i < data.name.length; i++) { hold += "<strong>Alternative Title:</strong> " + data.name[i] + " <br>"; }
  	popupedit.innerHTML = hold;
  	// Tags & Chapter Number
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
  	// Links
  	hold = "";
  	const row = 3;
  	// <3 Links Condition
  	if (data.links.length <= row) {
      popupedit = document.getElementsByClassName("fm_links")[0];
      let count = 0;
      for (let l of data.links) {
        hold += "<td><img src=\"" + l.icon + "\" alt=\"" + l.site + "\" class=\"sicon\">\n<a href=\"" + l.link + "\" target=\"_blank\">" + l.site + "</a></td>";
        count++;
      }
      if (count < row) { for (; count <= row; count++) { hold += "<td></td>"; } }
      popupedit.innerHTML = hold;
    }
  	// >3 Links Condition
 	else {
      popupedit = document.getElementsByClassName("fm_links")[0];
      for (let i = 0; i < row; i++) {
        hold += "<td><img src=\"" + data.links[i].icon + "\" alt=\"" + data.links[i].site + "\" class=\"sicon\">\n<a href=\"" + data.links[i].link + "\" target=\"_blank\">" + data.links[i].site + "</a></td>";
      }
      popupedit.innerHTML = hold;
      popupedit = document.getElementByClassName("fm_links")[1];
      let count = 0;
      for (let l of data.links) {
        hold += "<td><img src=\"" + l.icon + "\" alt=\"" + l.site + "\" class=\"sicon\">\n<a href=\"" + l.link + "\" target=\"_blank\">" + l.site + "</a></td>";
        count++;
      }
      if (count < row) { for (; count <= row; count++) { hold += "<td></td>"; } }
      popupedit.innerHTML = hold;
    }
  	// Display Full Manga
  	var popupmain = document.getElementById("full_manga");
  	var popupcomp = document.getElementById("fm_catch");
    popupmain.style.display = "block";
  	popupcomp.style.display = "block";
}
function closeFullManga() {
  	// Hide Full Manga
    var popupmain = document.getElementById("full_manga");
  	var popupcomp = document.getElementById("fm_catch");
    popupmain.style.display = "none";
  	popupcomp.style.display = "none";
}