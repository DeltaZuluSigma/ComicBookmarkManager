<?php
// Extract XML file
$xmldata = simplexml_load_file("../files/bookmarks.xml") or die("Failed to load");
$sitesdata = simplexml_load_file("../files/sites.xml") or die("Failed to load sites.");

// Get XML bookmarks file size
clearstatcache();
$mlock = (filesize("../files/bookmarks.xml") < 116000)?"":"title='Too many bookmarks.' disabled";
$slock = (filesize("../files/sites.xml") < 116000)?"":"title='Too many sites.' disabled";
// SET/VALID FORM DATA CONDITIONS **************************************************
// Set edit condition
if (isset($_GET['index']) && !empty($_GET['index']) && ($idx=$_GET['index']) >= 0) { $comic = $xmldata->comic[$idx]; }
// Add/edit information conditions
if (isset($_POST['header']) && !empty($_POST['header'])) {
  $head = $_POST['header'];
  switch($head) {
    case "editmanga":			// "Edit Manga" Form Data
      break;
    case "removemanga":			// "Remove Manga" Form Data
      break;
    case "addmanga":			// "Add Manga" Form Data
      // Primary Name Condition
      if (isset($_POST['mname']) && !empty($_POST['mname'])) { $primaryname = $_POST['mname']; }
      // Alternative Names Condition
      if (isset($_POST['altname'])) {
        $temp = count($_POST['altname']);
        for ($i = 0; $i < $temp; $i++) {
          if (empty($_POST['altname'][$i])) { unset($_POST['altname'][$i]); }
        }
        if (!empty($_POST['altname'])) { $altnames = array_values($_POST['altname']); }
      }
      // Genre Tags Condition
      if (isset($_POST['gtags']) && !empty($_POST['gtags'])) { $gtags = $_POST['gtags']; }
      // Maturity Tag Condition
      if (isset($_POST['mtag']) && !empty($_POST['mtag']) && $_POST['mtag'] != "none") { $mtag = $_POST['mtag']; }
      // Status Tags Condition
      if (isset($_POST['stags_opt']) && !empty($_POST['stags_opt'])) { $review = true; }
      if (isset($_POST['stags']) && !empty($_POST['stags'])) { $stag = $_POST['stags']; }
      // Chapter Number Condition
      if (isset($_POST['chapter']) && !empty($_POST['chapter'])) { $chapter = $_POST['chapter']; }
      // Cover Conditions
      if (isset($_POST['cover']) && !empty($_POST['cover'])) { $urlcover = $_POST['cover']; }
      else if ($_FILES) { $fcrslt = buildfile('cover'); }
      // Sites & Links Conditions
      if (isset($_POST['sites']) && isset($_POST['links'])) {
        $temp = count($_POST['sites']);
        for ($i = 0; $i < $temp; $i++) {
          if (empty($_POST['sites'][$i]) || empty($_POST['links'][$i])) {
            unset($_POST['sites'][$i]);
            unset($_POST['links'][$i]);
          }
        }
        $sitepair = array_values($_POST['sites']);
        $linkpair = array_values($_POST['links']);
      }
      break;
    case "addsite":			// "Add Site" Form Data
      // Manga Site Name Condition
      if (isset($_POST['assos_site']) && !empty($_POST['assos_site'])) { $ascsite = $_POST['assos_site']; }
      // Favicon Conditions
      if (isset($_POST['icon']) && !empty($_POST['icon'])) { $urlicon = $_POST['icon']; }
      else if ($_FILES) { $firslt = buildfile('icon'); }
      break;
    case "removesite":		// "Remove Site" Form Data
      // Manga Site Name (To-Be-Removed) Condition
      if (isset($_POST['remove']) && !empty($_POST['remove'])) { $rmname = strtolower($_POST['remove']); }
      break;
    default: echo "Cannot identify form header."; break;
  }
}
//if (isset($_POST['']) && !empty($_POST[''])) {  }

// FUNCTIONS & OPERATIONS ************************************************************
/**buildfile Function*******************************************************
   Desc: Index and store the file
   Param: form name
   Return: relative file location
*/
function buildfile($name) {
  //Check the file image type
  switch($_FILES[$name]['type']) {
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
    $ref = "css/images/" . $name . "" . date("Ymd.His") . "." . $ext;
    //Upload file
    move_uploaded_file($_FILES[$name]['tmp_name'], "../" . $ref);
    return $ref;
  }
  else { return 'error'; }
}
/**Manga/Site Storing & Saving Operation************************************
   Desc: Generate and save appropriate manga/site entry
*/
switch ($head) {
  case "editmanga":			// "Edit Manga" Operation
    break;
  case "removemanga":		// "Remove Manga" Operation
    break;
  case "addmanga":			// "Add Manga" Operation
    if (isset($urlcover) || (isset($fcrslt) && $fcrslt != "error")) {
      $comic = $xmldata->addChild('comic');
      $comic->addChild('name',$primaryname);
      foreach ($altnames as $alt) { $comic->addChild('alt',$alt); }
      $ctags = "";
      foreach ($gtags as $tag) { $ctags .= $tag . ","; }
      if ($mtag !== null) { $ctags .= $mtag . ","; }
      if ($review) { $ctags .= "reviewing,"; }
      $ctags .= $stag;
      $comic->addChild('tags',$ctags);
      $comic->addChild('chapter',$chapter);
      $comic->addChild('cover',(isset($urlcover))?$urlcover:$fcrslt);
      for ($i = 0; $i < count($sitepair); $i++) {
        $templink = $comic->addChild('link',$linkpair[$i]);
        $templink->addAttribute('site',$sitepair[$i]);
      }
      
      $alert = 1;
      $xmldata->asXML('../files/xtemp.xml');
      unset($comic);
      unset($ctags);
      unset($templink);
    }
    else {
      $alert = -1;
    }
    break;
  case "addsite":			// "Add Site" Operation
    if ($sitesdata->xpath("site[@name=\"".$ascsite."\"]") != false) {
      $alert = -2;
    }
    else if (isset($urlicon) || (isset($firslt) && $firslt != "error")) {
      $tempsite = $sitesdata->addChild('site',(isset($urlicon))?$urlicon:$firslt);
      $tempsite->addAttribute('name',$ascsite);
      
      $alert = 2;
      $sitesdata->asXML('../files/stemp.xml');
      unset($tempsite);
    }
    else {
      $alert = -3;
    }
    break;
  case "removesite":		// "Remove Site" Operation
    $count = $sitesdata->count();
    $suc = false;
    for ($i = 0; $i < $count; $i++) {
      if(strtolower($sitesdata->site[$i]['name']) == $rmname) {
        unset($sitesdata->site[$i]);
        $sitesdata->asXML('../files/stemp.xml');
        $suc = true;
        break;
      }
    }
    $alert = ($suc)?3:-4;
    
    unset($count);
    unset($suc);
    break;
}
?>
<html>
  <head>
    <title>Editing Bookmarks</title>
	<link rel="icon" type="image/png" href="../css/images/favicon.ico"/>
    <!--Bootstrap & RWD-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../css/bootstrap.min.css" />
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
	<!--Style Sheets & JS file-->
	<link rel="stylesheet" href="../css/edit.css" />
    <script src="../js/edit.js"></script>
  </head>
  <body>
    <!--Feedback Alert-->
    <?php
    if ($alert < 0) { $alertele = "<article class='alert alert-danger'>\n"; }
    else if ($alert > 0) { $alertele = "<article class='alert alert-success'>\n"; }
    
    switch ($alert) {
      case 1: $alertele .= "Manga successfully added and saved."; break;
      case 2: $alertele .= "Manga site successfully added and saved."; break;
      case 3: $alertele .= "Manga site successfully removed and saved."; break;
      case -1: $alertele .= "<h5>File Error</h5>A file with an invalid file type was uploaded as a manga cover."; break;
      case -2: $alertele .= "<h5>Duplicate Manga Site</h5>An existing manga site name was used, please use a unique manga site name. Delete and readd a manga site to edit an manga site."; break;
      case -3: $alertele .= "<h5>File Error</h5>A file with an invalid file type was uploaded as a manga site's icon."; break;
      case -4: $alertele .= "<h5>Invalid Manga Site</h5>That manga site isn't on file."; break;
    }
    
    echo "\n" . $alertele . "</article>\n";
    ?>
    <!--Website Head-->
    <header class="jumbotron">
      <table><tr><td>
      		<a href="../index_xml.php"><img src="../css/images/favicon.ico" alt="logo" id="logo"></a>
          </td>
          <td>
            <h1>Manga Bookmark Editor</h1>
      </td></tr></table>
    </header>
    <!--Website Body • Form Editor-->
    <section>
      <?php
      if ($idx !== null && $idx >= 0) { // Edit Manga Condition
        echo "edit";
      }
      else if ($idx < 0) { // Add New Manga Condition
        echo <<<_END
        <h5>Add New Manga / Comic</h5>
        <div class="ctnr">
          <form method="post" enctype='multipart/form-data'>
            <input type="hidden" name="header" value="addmanga" />
            <div>
              <h6>Manga Titles</h6>
              <span>Primary Manga Name</span>
              <input type="text" id="primary_mname" class="form-control form-control-sm" name="mname" required />
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
                  <input type="checkbox" name="gtags[]" value="casual" required /> <span class="genre_label">Casual</span>
                  <input type="checkbox" name="gtags[]" value="adventure" required /> <span class="genre_label">Adventure</span>
                  <input type="checkbox" name="gtags[]" value="romance" required /> <span class="genre_label">Romance</span>
                </div>
                <div>
                  <span>Maturity Tags:</span>
                  <input type="radio" name="mtag" value="none" checked />  <span>None</span>
                  <input type="radio" name="mtag" value="suggestive" /> <span class="maturity_label">Suggestive</span>
                  <input type="radio" name="mtag" value="erotic" /> <span class="maturity_label">Erotic</span>
                </div>
                <span>Status Tags:</span>
                <input type="checkbox" name="stags_opt" value="reviewing" /> <span class="status_label">Reviewing</span>
                <input type="radio" name="stags" value="new" checked /> <span class="status_label">New</span>
                <input type="radio" name="stags" value="reading" /> <span class="status_label">Reading</span>
                <input type="radio" name="stags" value="paused" /> <span class="status_label">Paused</span>
                <input type="radio" name="stags" value="mia" /> <span class="status_label">MIA</span>
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
              <input type="text" class="form-control form-control-sm" name="assos_site" required />
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
            <input type="text" class="form-control form-control-sm" name="remove" required />
            <label>The respective manga site name to be removed. Note that this is <u>case-sensitive</u>.</label>
            <br>
            <input type="submit" value="Remove Manga Site" class="rmasp" />
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