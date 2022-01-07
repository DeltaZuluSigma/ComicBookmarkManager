<?php
// Extract XML file
$manga_data = simplexml_load_file("../files/mangabmks.xml") or die("Failed to load bookmarks.");
$sites_data = simplexml_load_file("../files/mangasites.xml") or die("Failed to load sites.");

// XML file size conditions
clearstatcache();
$mlock = (filesize("../files/mangabmks.xml") < 116000)?"":"title='Too many bookmarks.' disabled";
$slock = (filesize("../files/mangasites.xml") < 116000)?"":"title='Too many sites.' disabled";

// Form Data Conditions **************************************************
if (isset($_GET['index']) && ($idx=intval($_GET['index'])) >= 0) { $comic = $manga_data->comic[$idx]; }
if (isset($_POST['header']) && !empty($_POST['header'])) {
  $head = $_POST['header'];
  if ($head != "editmanga" && $head != "removemanga" && $head != "addmanga" && $head != "addsite" && $head != "removesite") {
    echo "Could not identify header.";
  }
}
if (isset($_POST['manganame']) && !empty($_POST['manganame'])) { $primary_name = $_POST['manganame']; }
if (isset($_POST['altname'])) {
  // Remove empty alternative entries
  $temp = count($_POST['altname']);
  for ($i = 0; $i < $temp; $i++) {
    if (empty($_POST['altname'][$i])) { unset($_POST['altname'][$i]); }
  }
  if (!empty($_POST['altname'])) { $alt_names = array_values($_POST['altname']); }
}
if (isset($_POST['genretags']) && !empty($_POST['genretags'])) { $gtags = $_POST['genretags']; }
if (isset($_POST['maturitytag']) && !empty($_POST['maturitytag']) && $_POST['maturitytag'] != "none") { $mtag = $_POST['maturitytag']; }
if (isset($_POST['reviewtag']) && !empty($_POST['reviewtag'])) { $rtag = true; }
if (isset($_POST['statustag']) && !empty($_POST['statustag']) && $_POST['statustag'] != "none") { $stag = $_POST['statustag']; }
if (isset($_POST['chapter']) && !empty($_POST['chapter'])) { $chapter = $_POST['chapter']; }
if (isset($_POST['oldcover']) && !empty($_POST['oldcover'])) { $old_cover = true; }
if (isset($_POST['cover']) && !empty($_POST['cover'])) { $url_cover = $_POST['cover']; }
else if ($_FILES) { $file_cover = buildfile('cover'); }
if (isset($_POST['sites']) && isset($_POST['links'])) {
  // Remove incomplete link/site pairs
  $temp = count($_POST['sites']);
  for ($i = 0; $i < $temp; $i++) {
    if (empty($_POST['sites'][$i]) || empty($_POST['links'][$i])) {
	  unset($_POST['sites'][$i]);
	  unset($_POST['links'][$i]);
	}
  }
  $site_pair = array_values($_POST['sites']);
  $link_pair = array_values($_POST['links']);
}
if (isset($_POST['assossite']) && !empty($_POST['assossite'])) { $asc_site = $_POST['assossite']; }
if (isset($_POST['icon']) && !empty($_POST['icon'])) { $url_icon = $_POST['icon']; }
else if ($_FILES) { $file_icon = buildfile('icon'); }
if (isset($_POST['removesite']) && !empty($_POST['removesite'])) { $remove_name = strtolower($_POST['removesite']); }

// Form Data Operations **************************************************
switch ($head) {
  case "editmanga":
    /* Edit Manga Operation (editmanga)
	   Desc: Process form data such that it saves the amended manga details, otherwise throwing an appropriate error alert
	*/
    if (!isset($old_cover) && !(isset($url_cover) || (isset($file_cover) && $file_cover != "error"))) {
      $alert = -1;
    }
    else if (isset($old_cover) || isset($url_cover) || (isset($file_cover) && $file_cover != "error")) {
      $comic->name = $primary_name;
      for ($i = 0; $i < count($alt_names); $i++) {
        if (isset($comic->alt[$i])) { $comic->alt[$i] = $alt_names[$i]; }
        else { $comic->addChild('alt',$alt_names[$i]); }
      }
      $cum_tags = "";
      foreach ($gtags as $tag) { $cum_tags .= $tag . ","; }
      if ($mtag !== null) { $cum_tags .= $mtag . ","; }
      if ($rtag) { $cum_tags .= "reviewing,"; }
      $cum_tags .= $stag;
      $comic->tags = $cum_tags;
      $comic->chapter = $chapter;
      if (!$old_cover && isset($url_cover)) { $comic->cover = $url_cover; }
      else if (!$old_cover && isset($file_cover)) { $comic->cover = $file_cover; }
      for ($i = 0; $i < count($site_pair); $i++) {
        if (isset($comic->link[$i])) {
          $comic->link[$i] = $link_pair[$i];
          $comic->link[$i]['site'] = $site_pair[$i];
        }
        else {
          $templink = $comic->addChild('link',$link_pair[$i]);
          $templink->addAttribute('site',$site_pair[$i]);
        }
        
        $alert = 4;
        $manga_data->asXML('../files/mangabmks.xml');
        unset($cum_tags);
        unset($templink);
      }
    }
    else {
      $alert = -1;
    }
    break;
  case "removemanga":
	/* Remove Manga Operation (removemanga)
	   Desc: Process form data such that it removes the manga and saves the new 'manga_data' object, otherwise throwing an appropriate error alert
	*/
    unset($manga_data->comic[$idx]);
    $manga_data->asXML('../files/mangabmks.xml');
    $alert = 5;
    break;
  case "addmanga":
	/* Add Manga Operation (addmanga)
	   Desc: Process form data such that it appends the new manga's details, otherwise throwing an appropriate error alert
	*/
    if (isset($url_cover) || (isset($file_cover) && $file_cover != "error")) {
      $comic = $manga_data->addChild('comic');
      $comic->addChild('name',$primary_name);
      foreach ($alt_names as $alt) { $comic->addChild('alt',$alt); }
      $cum_tags = "";
      foreach ($gtags as $tag) { $cum_tags .= $tag . ","; }
      if ($mtag !== null) { $cum_tags .= $mtag . ","; }
      if ($rtag) { $cum_tags .= "reviewing,"; }
      $cum_tags .= $stag;
      $comic->addChild('tags',$cum_tags);
      $comic->addChild('chapter',$chapter);
      $comic->addChild('cover',(isset($url_cover))?$url_cover:$file_cover);
      for ($i = 0; $i < count($site_pair); $i++) {
        $templink = $comic->addChild('link',$link_pair[$i]);
        $templink->addAttribute('site',$site_pair[$i]);
      }
      
      $alert = 1;
      $manga_data->asXML('../files/mangabmks.xml');
      unset($comic);
      unset($cum_tags);
      unset($templink);
    }
    else {
      $alert = -1;
    }
    break;
  case "addsite":
	/* Add Site Operation (addsite)
	   Desc: Process form data such that it appends the new site's pair, otherwise throwing an appropriate error alert
	*/
    if ($sites_data->xpath("site[@name=\"".$asc_site."\"]") != false) {
      $alert = -2;
    }
    else if (isset($url_icon) || (isset($file_icon) && $file_icon != "error")) {
      $tempsite = $sites_data->addChild('site',(isset($url_icon))?$url_icon:$file_icon);
      $tempsite->addAttribute('name',$asc_site);
      
      $alert = 2;
      $sites_data->asXML('../files/mangasites.xml');
      unset($tempsite);
    }
    else {
      $alert = -3;
    }
    break;
  case "removesite":
	/* Remove Site Operation (removesite)
	   Desc: Process form data such that it removes the site and saves the new 'sites_data' object, otherwise throwing an appropriate error alert
	*/
    $count = $sites_data->count();
    $suc = false;
    for ($i = 0; $i < $count; $i++) {
      if(strtolower($sites_data->site[$i]['name']) == $remove_name) {
        unset($sites_data->site[$i]);
        $sites_data->asXML('../files/mangasites.xml');
        $suc = true;
        break;
      }
    }
    $alert = ($suc)?3:-4;
    
    unset($count);
    unset($suc);
    break;
}

/**buildFile Function****************************************************
   Desc: Index and store the file
   Param: form name
   Return: relative file location
*/
function buildFile($form_name) {
  //Check the file image type
  switch($_FILES[$form_name]['type']) {
    case 'image/jpeg': $ext = 'jpg'; break;
    case 'image/gif':  $ext = 'gif'; break;
    case 'image/png':  $ext = 'png'; break;
    case 'image/tiff': $ext = 'tif'; break;
    case 'image/vnd.microsoft.icon': $ext = 'ico'; break;
    case 'image/svg+xml': $ext = 'svg'; break;
    case 'image/webp': $ext = 'webp'; break;
    default: $ext = ''; break;
  }
  //Check for an appropriate extention
  if ($ext) {
    //Appropriately location of the file
    $ref = "css/images/" . $form_name . "" . date("Ymd.His") . "." . $ext;
    //Upload file
    move_uploaded_file($_FILES[$form_name]['tmp_name'], "../" . $ref);
    return $ref;
  }
  else {
	  return 'error';
  }
}
?>
<html>
  <head>
    <title>Editing Bookmarks</title>
	<link rel="icon" type="image/png" href="../css/images/favicon.ico"/>
    <!--Bootstrap & RWD-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://zhou16g.myweb.cs.uwindsor.ca/bootstrap.min.css" />
    <script src="https://zhou16g.myweb.cs.uwindsor.ca/jquery-3.5.1.min.js"></script>
    <script src="https://zhou16g.myweb.cs.uwindsor.ca/bootstrap.min.js"></script>
	<!--Style Sheets & JS file-->
	<link rel="stylesheet" href="../css/edit.css" />
    <script src="../js/edit.js"></script>
  </head>
  <body>
    <!--Feedback Alert-->
    <?php
    switch ($alert) {
      case 1: $alert_contents = "Manga successfully added and saved."; break;
      case 2: $alert_contents = "Manga site successfully added and saved."; break;
      case 3: $alert_contents = "Manga site successfully removed and saved."; break;
      case 4: $alert_contents = "Manga successfully edited and saved."; break;
      case 5: $alert_contents = "Manga successfully removed and saved."; break;
      case -1: $alert_contents = "<h5>File Error</h5>A file with an invalid file type was uploaded as a manga cover."; break;
      case -2: $alert_contents = "<h5>Duplicate Manga Site</h5>An existing manga site name was used, please use a unique manga site name. Delete and readd a manga site to edit an manga site."; break;
      case -3: $alert_contents = "<h5>File Error</h5>A file with an invalid file type was uploaded as a manga site's icon."; break;
      case -4: $alert_contents = "<h5>Invalid Manga Site</h5>That manga site isn't on file."; break;
    }
    
	if ($alert < 0) { echo "\n<article class='alert alert-danger'>\n" . $alert_contents . "</article>\n"; }
    else if ($alert > 0) { echo "\n<article class='alert alert-success'>\n" . $alert_contents . "</article>\n"; }
    ?>
    <!--Website Head-->
    <header class="jumbotron">
      <a href="../index_xml.php" class="lhide"><table><tr>
		<td>
      	  <img src="../css/images/favicon.ico" alt="logo" id="logo">
        </td>
        <td>
          <h1>Manga Bookmark Editor</h1>
		</td>
	  </tr></table></a>
    </header>
    <!--Website Body • Form Editor-->
    <section>
      <?php
      if ($idx !== null && $idx >= 0) {
		/* Edit Manga Conditional
		   Desc: Alters the webpage contents for editing a specified manga (Edit Manga Form, Remove Manga Form)
		*/
        // Set 'present_cum_alts'
        if ($comic->alt->count() > 0) {
          $present_cum_alts = "<span>Alternative Manga Name(s)</span><br>\n<label>Other searchable manga names.</label>\n";
          foreach ($comic->alt as $copy_calt) {
            $present_cum_alts .= "<input type='text' class='form-control form-control-sm space' name='altname[]' value='" . $copy_calt . "' />\n";
          }
        }
        // Populate 'chk_tags'
        $tags = explode(',',$comic->tags);
        $chk_tags = [3 => "checked",7 => "checked"];
        foreach ($tags as $t) {
          switch ($t) {
            case "casual": $chk_tags[0] = "checked"; break;
            case "adventure": $chk_tags[1] = "checked"; break;
            case "romance": $chk_tags[2] = "checked"; break;
            case "suggestive":
              $chk_tags[3] = $chk_tags[5] = "";
              $chk_tags[4] = "checked";
              break;
            case "erotic":
              $chk_tags[3] = $chk_tags[4] = "";
              $chk_tags[5] = "checked";
              break;
            case "reviewing": $chk_tags[6] = "checked"; break;
            case "new":
              $chk_tags[7] = $chk_tags[9] = $chk_tags[10] = $chk_tags[11] = "";
              $chk_tags[8] = "checked";
              break;
            case "reading":
              $chk_tags[7] = $chk_tags[8] = $chk_tags[10] = $chk_tags[11] = "";
              $chk_tags[9] = "checked";
              break;
            case "paused":
              $chk_tags[7] = $chk_tags[9] = $chk_tags[8] = $chk_tags[11] = "";
              $chk_tags[10] = "checked";
              break;
            case "mia":
              $chk_tags[7] = $chk_tags[9] = $chk_tags[10] = $chk_tags[8] = "";
              $chk_tags[11] = "checked";
              break;
          }
        }
        // Set 'present_cum_links'
        if ($comic->link->count() > 1) {
          $present_cum_links = "";
          for ($i = 1; $i < $comic->link->count(); $i++) {
            $present_cum_links .= "<div class='link_pair'>\n<input type='text' class='form-control form-control-sm' name='sites[]' placeholder='Associated Manga Site' value='" .$comic->link[$i]['site']. "' />\n<input type='url' class='form-control form-control-sm' name='links[]' placeholder='https://' value='" .$comic->link[$i]. "' />\n</div>\n";
          }
        }
        echo <<<_END
        <h5>Edit Manga</h5>
        <div class="ctnr">
          <form method="post" enctype='multipart/form-data'>
            <input type="hidden" name="header" value="editmanga" />
            <div>
              <h6>Manga Titles</h6>
              <span>Primary Manga Name</span>
              <input type="text" id="primary_mname" class="form-control form-control-sm" name="manganame" value="{$comic->name}" required />
              <label for="primary_mname">The displayed name for the manga.</label><br>
              {$present_cum_alts}
              <a href="#" id="add_alt">Add Alternative Manga Name</a>
            </div>
            <br>
            <div>
              <h6>Tags</h6>
              <label>How the manga will be organized.</label>
              <div id="ctr_ctnr">
              	<div>
                  <span>Genre Tags:</span>
                  <input type="checkbox" name="genretags[]" value="casual" {$chk_tags[0]} /> <span class="genre_label">Casual</span>
                  <input type="checkbox" name="genretags[]" value="adventure" {$chk_tags[1]} /> <span class="genre_label">Adventure</span>
                  <input type="checkbox" name="genretags[]" value="romance" {$chk_tags[2]} /> <span class="genre_label">Romance</span>
                </div>
                <div>
                  <span>Maturity Tags:</span>
                  <input type="radio" name="maturitytag" value="none" {$chk_tags[3]} />  <span>None</span>
                  <input type="radio" name="maturitytag" value="suggestive" {$chk_tags[4]} /> <span class="maturity_label">Suggestive</span>
                  <input type="radio" name="maturitytag" value="erotic" {$chk_tags[5]} /> <span class="maturity_label">Erotic</span>
                </div>
                <span>Status Tags:</span>
                <input type="checkbox" name="reviewtag" value="reviewing" {$chk_tags[6]} /> <span class="status_label">Reviewing</span>
                <input type="radio" name="statustag" value="none" {$chk_tags[7]} /> <span>None</span>
                <input type="radio" name="statustag" value="new" {$chk_tags[8]} /> <span class="status_label">New</span>
                <input type="radio" name="statustag" value="reading" {$chk_tags[9]} /> <span class="status_label">Reading</span>
                <input type="radio" name="statustag" value="paused" {$chk_tags[10]} /> <span class="status_label">Paused</span>
                <input type="radio" name="statustag" value="mia" {$chk_tags[11]} /> <span class="status_label">MIA</span>
              </div>
            </div>
            <br>
            <div>
              <h6>Chapter Number</h6>
              <input type="number" class="form-control form-control-sm" name="chapter" value="{$comic->chapter}" min="1" required />
              <label>The latest chapter read.</label>
            </div>
            <br>
            <div id="cvr_ctnr">
              <h6>Manga Cover</h6>
              <input type="url" class="form-control form-control-sm" name="cover" placeholder="https://" disabled />
              <label>The cover / poster image for the manga. Do you want to upload a: <span>link / <a href="#">image</a></span></label>
              <div id="cvropt">
              	<code>{$comic->cover}</code><br>
                <img src="{$comic->cover}" alt="{$comic->name}" /><br>
                <input type="checkbox" name="oldcover" value="yes" checked /> <span>Use the old cover.</span>
              </div>
            </div>
            <br>
            <div>
              <h6>Manga Links</h6>
              <div class="link_pair">
                <input type="text" class="form-control form-control-sm" name="sites[]" value="{$comic->link[0]['site']}" required />
                <input type="url" class="form-control form-control-sm" name="links[]" value="{$comic->link[0]}" required />
              </div>
              {$present_cum_links}
              <a href="#" id="add_link">Add More Links</a>
            </div>
            <br>
            <input type="submit" value="Edit Manga" />
          </form>
        </div>
        <br>
        <h5>Remove Selected Manga</h5>
        <div class="ctnr trim">
          <form method="post">
            <input type="hidden" name="header" value="removemanga" />
            <input type="submit" class="remove" value="Remove Manga" />
          </form>
        </div>
_END;
      }
      else if ($idx < 0) {
	    /* Add New Manga Conditional
		   Desc: Alters the webpage contents for editing a specified manga
		*/
        echo <<<_END
        <h5>Add New Manga</h5>
        <div class="ctnr">
          <form method="post" enctype='multipart/form-data'>
            <input type="hidden" name="header" value="addmanga" />
            <div>
              <h6>Manga Titles</h6>
              <span>Primary Manga Name</span>
              <input type="text" id="primary_mname" class="form-control form-control-sm" name="manganame" required />
              <label for="primary_mname">The displayed name for the manga.</label><br>
              <a href="#" id="add_alt">Add Alternative Manga Name</a>
            </div>
            <br>
            <div>
              <h6>Tags</h6>
              <label>How the manga will be organized.</label>
              <div id="ctr_ctnr">
              	<div>
                  <span>Genre Tags:</span>
                  <input type="checkbox" name="genretags[]" value="casual" required /> <span class="genre_label">Casual</span>
                  <input type="checkbox" name="genretags[]" value="adventure" required /> <span class="genre_label">Adventure</span>
                  <input type="checkbox" name="genretags[]" value="romance" required /> <span class="genre_label">Romance</span>
                </div>
                <div>
                  <span>Maturity Tags:</span>
                  <input type="radio" name="maturitytag" value="none" checked />  <span>None</span>
                  <input type="radio" name="maturitytag" value="suggestive" /> <span class="maturity_label">Suggestive</span>
                  <input type="radio" name="maturitytag" value="erotic" /> <span class="maturity_label">Erotic</span>
                </div>
                <span>Status Tags:</span>
                <input type="checkbox" name="reviewtag" value="reviewing" /> <span class="status_label">Reviewing</span>
				<input type="radio" name="statustag" value="none" checked /> <span>None</span>
                <input type="radio" name="statustag" value="new" /> <span class="status_label">New</span>
                <input type="radio" name="statustag" value="reading" /> <span class="status_label">Reading</span>
                <input type="radio" name="statustag" value="paused" /> <span class="status_label">Paused</span>
                <input type="radio" name="statustag" value="mia" /> <span class="status_label">MIA</span>
              </div>
            </div>
            <br>
            <div>
              <h6>Chapter Number</h6>
              <input type="number" class="form-control form-control-sm" name="chapter" value="1" min="1" required />
              <label>The latest chapter read.</label>
            </div>
            <br>
            <div id="cvr_ctnr">
              <h6>Manga Cover</h6>
              <input type="url" class="form-control form-control-sm" name="cover" placeholder="https://" required />
              <label>The cover / poster image for the manga. Do you want to upload a: <span>link / <a href="#">image</a></span></label>
            </div>
            <br>
            <div>
              <h6>Manga Links</h6>
              <div class="link_pair">
                <input type="text" class="form-control form-control-sm" name="sites[]" placeholder="Associated Manga Site" required />
                <input type="url" class="form-control form-control-sm" name="links[]" placeholder="https://" required />
              </div>
              <a href="#" id="add_link">Add More Links</a>
            </div>
            <br>
            <input type="submit" value="Add Manga"{$mlock} />
          </form>
        </div>
        <br>
        <h5>Add New Site</h5>
        <div class="ctnr">
          <form method="post" enctype='multipart/form-data'>
          	<input type="hidden" name="header" value="addsite" />
            <div>
              <span>Manga Site</span>
              <input type="text" class="form-control form-control-sm" name="assossite" required />
              <label>This will be used to associate links with their favicons.</label>
            </div>
            <div id="icon_ctnr">
              <span>Favicon</span>
              <input type="url" class="form-control form-control-sm" name="icon" placeholder="https://" required />
              <label>An icon to better associate links with their respective manga site. Do you want to upload a: <span>link / <a href="#">image</a></span></label>
            </div>
            <input type="submit" value="Add Manga Site"{$slock} />
          </form>
        </div>
        <br>
        <h5>Remove A Site</h5>
        <div class="ctnr">
          <form method="post">
          	<input type="hidden" name="header" value="removesite" />
            <span>Manga Site</span>
            <input type="text" class="form-control form-control-sm" name="removesite" required />
            <label>The respective manga site name to be removed. Note that this is <u>case-sensitive</u>.</label>
            <br>
            <input type="submit" value="Remove Manga Site" class="remove" />
          </form>
        </div>
_END;
      }
      else { // Invalid Entry Condition
        echo "<table id=\"empty\"><tr><td><h3>Invalid Edit Option.</h3></td></tr></table>";
      }
      ?>
    </section>
    <!--Website Footer-->
    <br>
    <footer>
      <table>
        <tr>
          <td>Not for you :3</td>
          <td id="exit"><button onclick="location.href='../index_xml.php'; return false;" class="btn-sm btn-warning">Back</button></td>
        </tr>
      </table>
    </footer>
  </body>
</html>