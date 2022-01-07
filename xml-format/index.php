<?php
// Extract XML file
$manga_data = simplexml_load_file("files/mangabmks.xml") or die("Failed to load bookmarks.");
$sites_data = simplexml_load_file("files/mangasites.xml") or die("Failed to load sites.");

// Constants
define("DISPLAY",24);

// Search Query Array
$srch = ["tag" => "all","mia" => false,"pause" => false,"page" => 1];
if (isset($_POST['tagfilter']) && !empty($_POST['tagfilter'])) { $srch['tag'] = $_POST['tagfilter']; }
if (isset($_POST['mia']) && !empty($_POST['mia'])) { $srch['mia'] = ($_POST['mia'] == "exc")?false:true; }
if (isset($_POST['pause']) && !empty($_POST['pause'])) { $srch['pause'] = ($_POST['pause'] == "inc")?false:true; }
if (isset($_POST['manganame']) && !empty($_POST['manganame'])) { $srch['name'] = $_POST['manganame']; }
if (isset($_POST['page']) && !empty($_POST['page'])) { $srch['page'] = $_POST['page']; }
// Search Save Variables
$sltnchk = [];
switch ($srch['tag']) {
  case "casual": $sltnchk[1] = "checked "; break;
  case "adventure": $sltnchk[2] = "checked "; break;
  case "romance": $sltnchk[3] = "checked "; break;
  case "reviewing": $sltnchk[4] = "checked "; break;
  case "reading": $sltnchk[5] = "checked "; break;
  case "new": $sltnchk[6] = "checked "; break;
  default: $sltnchk[0] = "checked "; break;
}
if ($srch['mia']) { $sltnchk[7] = " selected"; }
else { $sltnchk[8] = " selected"; }
if ($srch['pause']) { $sltnchk[10] = " selected"; }
else { $sltnchk[9] = " selected"; }

// FUNCTIONS ************************************************************************************
/**search Function*******************************************************
   Desc: Apply the search filters accordingly to each comic
   Param: A comic object
   Return: boolean
*/
function search($comic) {
	global $srch;
    // Manga's labels
    $all_labels = explode(',',$comic->tags);
    
    // Manga name search conducted condition
    if ($srch['mname']) {
      if (strpos(strtolower($comic->name),$srch['name']) !== false) { return true; }
      foreach ($comic->alt as $altname) {
        if (strpos(strtolower($altname),$srch['name']) !== false) { return true; }
      }
      return false;
    }
    
    // Label/Tag condition
    if ($srch['tag'] == "all" || in_array($srch['tag'],$all_labels)) {
      if ($srch['mia'] && $srch['pause']) { return true; }
      else if (!$srch['mia'] && !in_array("mia",$all_labels) && $srch['pause']) { return true; }
      else if ($srch['mia'] && !$srch['pause'] && !in_array("paused",$all_labels)) { return true; }
      else if (!$srch['mia'] && !in_array("mia",$all_labels) && !$srch['pause'] && !in_array("paused",$all_labels)) { return true; }
    }
    return false;
}
/**formatLabel Function**************************************************
   Desc: Formats a tag into an appropriate label given their appropriate option
   Param:
     tag - Tag name
	 option - Format option
   Return: respective HTML label element
*/
function formatLabel($tag,$option) {
  switch (strtolower($tag)) {
    case "casual":
    case "adventure":
    case "romance":
	  return ($option)?"<span class=\"genre_label\">" . $tag . "</span> ":"{\"category\":\"genre\",\"tag\":\"" . $tag . "\"}";
    case "suggestive":
    case "erotic":
      return ($option)?"<span class=\"maturity_label\">" . $tag . "</span> ":"{\"category\":\"mature\",\"tag\":\"" . $tag . "\"}";
    case "paused":
    case "reading":
    case "reviewing":
    case "new":
      return ($option)?"<span class=\"status_label\">" . $tag . "</span> ":"{\"category\":\"status\",\"tag\":\"" . $tag . "\"}";
    case "mia":
      return ($option)?"<span class=\"status_label\">" . strtoupper($tag) . "</span> ":"{\"category\":\"status\",\"tag\":\"" . strtoupper($tag) . "\"}";
    default:
      return ($option)?"<span class=\"maturity_label\">Error</span> ":"{\"category\":\"mature\",\"tag\":\"Error\"}";
  }
}
/**validateFavicon Function**********************************************
   Desc: Validate manga sites and their favicon access
   Param: link - Link XML element
   Return: respective link pair
*/
function validateFavicon($link) {
  global $sites_data;
  for ($i = 1; $i < $sites_data->count(); $i++) {
    if ((string)$link['site'] == (string)$sites_data->site[$i]['name']) {
      return $sites_data->site[$i];
    }
  }
  return $sites_data->site[0];
}
/**buildJSON Function****************************************************
   Desc: Extract all manga info into a JSON string
   Param:
     idx - The manga's index within 'manga_data'
	 comic - The selectted manga's comic object
	 tags - An array of formatted tags of the select manga
   Return: JSON string
*/
function buildJSON($idx,$comic,$tags) {
  // Titles
  $all = "{\"index\":" . $idx . ",\"name\":[\"" . $comic->name . "\"";
  foreach ($comic->alt as $alt) { $all .= ",\"" . $alt . "\""; }
  // Tags
  $all .= "],\"tag\":[";
  foreach ($tags as $t) { $all .= formatLabel($t,0) . ","; }
  $all = substr($all,0,-1);
  // Chapter & Cover
  $all .= "],\"chapter\":" . $comic->chapter . ",\"cover\":\"" . $comic->cover . "\",\"links\":[";
  // Links
  foreach ($comic->link as $l) { $all .= "{\"icon\":\"" . validateFavicon($l) . "\",\"site\":\"" . $l['site'] . "\",\"link\":\"" . $l . "\"},"; }
  $all = substr($all,0,-1);
  $all .= "]}";
  
  return $all;
}
?>
<html>
  <head>
    <title>Manga Bookmarks</title>
	<link rel="icon" type="image/png" href="css/images/favicon.ico"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--Include Bootstrap-->
	<link rel="stylesheet" href="css/bootstrap.min.css" />
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<!--Style Sheet & JS File-->
	<link rel="stylesheet" href="css/index.css" />
    <script src="js/index.js"></script>
  </head>
  <body>
    <!--Website Head-->
    <header class="jumbotron">
      <img src="css/images/favicon.ico" alt="logo" id="logo">
      <h1>Manga Bookmarks</h1>
      <button onclick="location.href='html/edit_xml.php?index=-1'; return false;" class="btn-sm btn-warning">Add Manga</button>
    </header>
	<nav>
      <!--Bookmark Search Form-->
	  <form method="post">
	    <label>Manga Name:</label>
        <input type="text" class="form-control form-control-sm" name="manganame" <?php if ($srch['name']) { echo "value=\"" . $srch['name'] . "\" "; } ?>/>
        <br>
        <table>
          <tr>
            <td>
			  <div id="radios">
                <span title="Genre Tags">
                  <input type="radio" name="tagfilter" value="all" <?php echo $sltnchk[0]; ?>/>
                  <label>All</label>
                  <input type="radio" name="tagfilter" value="casual" <?php echo $sltnchk[1]; ?>/>
                  <label>Casual</label>
                  <input type="radio" name="tagfilter" value="adventure" <?php echo $sltnchk[2]; ?>/>
                  <label>Adventure</label>
                  <input type="radio" name="tagfilter" value="romance" <?php echo $sltnchk[3]; ?>/>
                  <label>Romance</label>
                </span>
                <span title="Status Tags">
                  <input type="radio" name="tagfilter" value="reviewing" <?php echo $sltnchk[4]; ?>/>
                  <label><em>Reviewing</em></label>
                  <input type="radio" name="tagfilter" value="reading" <?php echo $sltnchk[5]; ?>/>
                  <label><em>Reading</em></label>
                  <input type="radio" name="tagfilter" value="new" <?php echo $sltnchk[6]; ?>/>
                  <label><em>New</em></label>
                </span>
			  </div>
            </td>
            <td>
              <span title="Suspension Toggles">
                <select name="mia">
                  <option value="inc"<?php echo $sltnchk[7]; ?>>Include MIA</option>
                  <option value="exc"<?php echo $sltnchk[8]; ?>>Exclude MIA</option>
                </select>
                <select name="pause">
                  <option value="inc"<?php echo $sltnchk[9]; ?>>Include Paused</option>
                  <option value="exc"<?php echo $sltnchk[10]; ?>>Exclude Paused</option>
                </select>
              </span>
            </td>
            <td>
              <input type="submit" value="Search">
            </td>
          </tr>
          <tr>
          <?php
          // Searched manga index array
          $srch_results = [];
          for ($i = 0; $i < $manga_data->count(); $i++) { if (search($manga_data->comic[$i])) { $srch_results[] = $i; } }
          // Pagination condition and population
          if (count($srch_results) >= DISPLAY) {
            echo "<td colspan=\"3\"><div id=\"pages\">";
            for ($i = 1; $i <= ceil(count($srch_results) / DISPLAY); $i++) {
              // Identify current page
              $fill = ($srch['page'] == $i)?"id=\"current\" disabled ":"";
              echo "<input type=\"submit\" name=\"page\" value=\"" . $i . "\" " . $fill . "/> ";
            }
            echo "</div></td>";
          }
          ?>
          </tr>
        </table>
	  </form>
    </nav>
    <!--Website Body-->
    <section>
      <table>
        <tr>
          <?php
		  // Display Manga Operation **********************************************
          $rownum = 0;
          $loop_limit = (DISPLAY*$srch['page'] <= count($srch_results))?DISPLAY*$srch['page']:count($srch_results);
          
          for ($i = DISPLAY*($srch['page']-1); $i < $loop_limit; $i++) {
          	$comic = $manga_data->comic[$srch_results[$i]];
            $rownum++;
            
            if ($rownum > 3) {
              echo "</tr>\n<tr>";
              $rownum = 1;
            }
            
            $tags = explode(',',ucwords($comic->tags,","));
            $full = buildJSON($srch_results[$i],$comic,$tags);
            echo <<<_END
                <td class="ind_manga">
                <div class="click_area" onclick="openFullManga(this.firstElementChild); return false;">
                  <article>{$full}</article>
                  <img src="{$comic->cover}" alt="{$comic->name}" class="poster"/>
                  <p>{$comic->name}</p>
                </div>
                <div class="label_ctnr">
_END;
            
			// Label Labelling
            $tags_count = count($tags);
            $label_num = 0;
            $cum_labels = "";

            foreach ($tags as $tag) {
              $cum_labels .= formatLabel($tag,1);
              $label_num++;
              
              if ($tags_count > 0 && $label_num >= 3) {
                $cum_labels .= "</div>\n<div class=\"label_ctnr\">";
                $tags_count -= 3;
                $label_num = 0;
              }
            }
			
            echo $cum_labels . "</div>\n";
            echo "<p class=\"ch_num\"><strong>Chapter: </strong>" . $comic->chapter . "</p>\n<table>";

            // Link Tabling
            $links = "";
            foreach ($comic->link as $clink) { $links .= "<tr>\n<td>\n<img src=\"" . validateFavicon($clink) . "\" alt=\"" . $clink['site'] . "\" class=\"icon\"/>\n<a href=\"" . $clink . "\" target=\"_blank\">" . $clink['site'] . "</a>\n</td>\n</tr>\n"; }
            echo $links . "</table></td>";
          }
		  
          if ($rownum < 4 && !empty($srch_results)) {
            if (count($srch_results) <= 3) {
              for (; $rownum < 3; $rownum++) { echo "<td class=\"ind_manga\"></td>\n"; }
            }
            echo "</tr>\n";
          }
          
          if (empty($srch_results)) { echo "<td id=\"empty\"><h3>No Results. Search for Something Else.</h3></td></tr>\n"; }
          ?>
      </table>
    </section>
    <!--Selected Manga Focus View-->
    <div id="fm_catch" onclick="closeFullManga(); return false;"></div>
    <div id="full_manga">
      <table>
        <tr>
          <td id="fm_poster"></td>
          <td colspan="2" id="fm_titles"></td>
        </tr>
        <tr>
          <td colspan="3" id="fm_descriptors"></td>
        </tr>
        <tr class="fm_links"></tr>
        <tr class="fm_links"></tr>
        </table>
    </div>
    <!--Website Footer-->
    <br>
    <footer>
      <p>Not for you :3</p>
    </footer>
  </body>
</html>