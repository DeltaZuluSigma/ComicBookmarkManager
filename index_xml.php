<?php
// Extract XML file
$xmldata = simplexml_load_file("files/bookmarks.xml") or die("Failed to load");

// FUNCTIONS *********************************************************************
/**switch_label Function*************************************************
   Desc: Makes the label/tag switch structure more accessible/modular
   Param: tag - Label/Tag name
   Return: respective HTML label/tag element
*/
function switch_label($tag,$opt) {
  if ($opt) {
    switch (strtolower($tag)) {
      case "casual":
      case "adventure":
      case "romance":
        return "<span class=\"genre_label\">" . $tag . "</span> ";
      case "suggestive":
      case "erotic":
        return "<span class=\"maturity_label\">" . $tag . "</span> ";
      case "paused":
      case "reading":
      case "reviewing":
      case "new":
        return "<span class=\"status_label\">" . $tag . "</span> ";
      case "mia":
        return "<span class=\"status_label\">" . strtoupper($tag) . "</span> ";
      default:
        return "<span class=\"maturity_label\">Error</span> ";
    }
  }
  else {
    switch (strtolower($tag)) {
      case "casual":
      case "adventure":
      case "romance":
        return "{\"category\":\"genre\",\"tag\":\"" . $tag . "\"}";
      case "suggestive":
      case "erotic":
        return "{\"category\":\"mature\",\"tag\":\"" . $tag . "\"}";
      case "paused":
      case "reading":
      case "reviewing":
      case "new":
        return "{\"category\":\"status\",\"tag\":\"" . $tag . "\"}";
      case "mia":
        return "{\"category\":\"status\",\"tag\":\"" . strtoupper($tag) . "\"}";
      default:
        return "{\"category\":\"mature\",\"tag\":\"Error\"}";
    }
  }
}
/**switch_link Function**************************************************
   Desc: Makes the link & favicon switch structure more accessible/modular
   Param: link - Link XML element
   Return: respective HTML link & favicon elements
*/
function switch_link($link) {
  switch ($link['site']) {
    case "MangaDex": return "css/images/md_favicon.ico";
    case "Manga Sushi": return "https://mangasushi.net/wp-content/uploads/2020/11/cropped-MS_LOGO-32x32.png";
    case "Luminous Scans": return "https://www.luminousscans.com/fypadsuh/2021/04/cropped-logo-32x32.png";
    case "MangaKakalot": return "https://mangakakalot.com/favicon.ico";
    case "Bato.To": return "https://static.animemark.com/img/batoto/favicon.ico?v0";
    case "WhimSubs": return "https://whimsubs.xyz/static/img/logo.png";
    case "LHTranslation": return "css/images/lht_favicon.ico";
    case "Manganato": return "https://readmanganato.com/favicon.png";
    case "ManhuaPlus": return "https://manhuaplus.com/wp-content/uploads/2020/07/cropped-manhua-vuong-den-1-32x32.jpg";
    case "Flame Scans": return "https://flamescans.org/eternalflame/2021/03/cropped-fds-1-32x32.png";
    case "Reaper Scans": return "css/images/rs_favicon.png";
    case "1st Kiss Manga": return "css/images/1km_favicon.png";
    case "Asura Scans": return "https://www.asurascans.com/wp-content/uploads/2021/03/cropped-Group_1-1-32x32.png";
    case "Nani? Scans": return "css/images/ns_favicon.png";
    case "Tritinia Scans": return "css/images/ts_favicon.png";
    case "247 Manga": return "https://247manga.com/wp-content/uploads/2021/05/cropped-247manga-32x32.png";
    case "Kun Manga": return "https://kunmanga.com/wp-content/uploads/2021/06/cropped-kun-favicon-32x32.png";
    case "Immortal Updates": return "https://immortalupdates.com/wp-content/uploads/2017/10/SMALL-LOGO.png";
    case "Gourmet Scans": return "css/images/gs_favicon.png";
    case "Reset Scans": return "css/images/res_favicon.png";
    case "Isekai Scan": return "css/images/is_favicon.png";
    case "Manga Great": return "css/images/mg_favicon.png";
    case "Galaxy Degen Scans": return "https://gdegenscans.xyz/wp-content/uploads/2021/03/cropped-favicon-32x32.png";
    case "Cat Manga": return "https://images.catmanga.org/favicon.png";
    case "WordPress": return "https://s0.wp.com/i/favicon.ico";
    default: return "css/images/not-available.png";
  }
}
/**search Function*******************************************************
   Desc: Apply the search filters accordingly to each comic/manga
   Param: A comic object
   Return: boolean
*/
function search($comic) {
  // Search conducted condition
  if (isset($_POST['gstatus']) && !empty($_POST['gstatus']) && isset($_POST['mia']) && !empty($_POST['mia']) && isset($_POST['pause']) && !empty($_POST['pause'])) {
    // Searched label variable
    $label = $_POST['gstatus'];
    // Searched MIA param variable
    $mia_opt = ($_POST['mia'] == "exc")?false:true;
    // Searched Paused param variable
    $pause_opt = ($_POST['pause'] == "inc")?true:false;
    // Manga's labels
    $all_lbls = explode(',',$comic->tags);
    
    // Manga name search conducted condition
    if (isset($_POST['mname']) && !empty($_POST['mname'])) {
      // Searched name variable
      $srch_name = strtolower($_POST['mname']);
      
      // Primary title condition
      if (strpos(strtolower($comic->name),$srch_name) !== false) { return true; }
      // Foreach Loop through alternative titles to check condition
      foreach ($comic->alt as $altname) {
        if (strpos(strtolower($altname),$srch_name) !== false) { return true; }
      }
      
      // Fail Catch Case
      return false;
    }
    
    // Label/Tag condition
    if ($label == "all" || in_array($label,$all_lbls)) {
      // MIA & paused conditions
      if ($mia_opt && $pause_opt) { return true; }
      else if (!$mia_opt && !in_array("mia",$all_lbls) && $pause_opt) { return true; }
      else if ($mia_opt && !$pause_opt && !in_array("paused",$all_lbls)) { return true; }
      else if (!$mia_opt && !in_array("mia",$all_lbls) && !$pause_opt && !in_array("paused",$all_lbls)) { return true; }
    }
    
    // Fail Catch Case
    return false;
  }
}
/**exp_json Function******************************************************
   Desc: Extract all comic/manga info into JSON string
   Param: A comic object
   Return: JSON string
*/
function exp_json($comic,$tags) {
  // Titles
  $all = "{\"name\":[\"" . $comic->name . "\"";
  foreach ($comic->alt as $alt) { $all .= ",\"" . $alt . "\""; }
  // Tags
  $all .= "],\"tag\":[";
  foreach ($tags as $t) { $all .= switch_label($t,false) . ","; }
  $all = substr($all,0,-1);
  // Chapter & Cover
  $all .= "],\"chapter\":" . $comic->chapter . ",\"cover\":\"" . $comic->cover . "\",\"links\":[";
  // Links
  foreach ($comic->link as $l) { $all .= "{\"icon\":\"" . switch_link($l) . "\",\"site\":\"" . $l['site'] . "\",\"link\":\"" . $l . "\"},"; }
  $all = substr($all,0,-1);
  $all .= "]}";
  
  return $all;
}
?>
<html>
  <head>
    <title>Manga Bookmarks</title>
	<link rel="icon" type="image/png" href="css/images/favicon.ico"/>
    <!--Bootstrap & RWD-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<!--Style Sheets & JS file-->
	<link rel="stylesheet" href="css/index.css">
    	<script src="js/index.js"></script>
    <?php
    // Full detail and manga ID style/initial check
    if (isset($_GET['full']) && isset($_GET['id']) && !empty($_GET['full']) && !empty($_GET['full'])) {
      $full = true;
      $id = $_GET['id'];
      echo <<<_END
      <style>
        #fm_catch, #full_manga {
          display: block;
        }
      </style>
_END;
    }
    ?>
  </head>
  <body>
    <!--Website Head-->
    <header class="jumbotron">
      <img src="css/images/favicon.ico" alt="logo" id="logo">
      <h1>Manga Bookmarks</h1>
    </header>
	<nav>
      <!--Bookmark Search Form-->
      <?php
      if (isset($_POST['gstatus']) && !empty($_POST['gstatus']) && isset($_POST['mia']) && !empty($_POST['mia']) && isset($_POST['pause']) && !empty($_POST['pause'])) {
        // Searched label variable
        $label = $_POST['gstatus'];
        // Searched MIA param variable
        $mia = ($_POST['mia'] == "exc")?false:true;
        // Searched Paused param variable
        $pause = ($_POST['pause'] == "inc")?false:true;
      }
      ?>
	  <form method="post">
	    <label for="mname">Manga Name:</label>
        <input type="text" id="mname" class="form-control form-control-sm" name="mname"/>
        <br>
        <table>
          <tr>
            <td>
              <?php
              switch ($label) {
                case "casual": $cckd = " checked"; break;
                case "adventure": $adckd = " checked"; break;
                case "romance": $rockd = " checked"; break;
                case "reviewing": $revckd = " checked"; break;
                case "reading": $reackd = " checked"; break;
                case "new": $nckd = " checked"; break;
                default: $alckd = " checked"; break;
              }
              echo <<<_END
              <span id="genre">
                <input type="radio" id="all" name="gstatus" value="all"{$alckd}/>
                <label for="all">All</label>
                <input type="radio" id="casual" name="gstatus" value="casual"{$cckd}/>
                <label for="casual">Casual</label>
                <input type="radio" id="adventure" name="gstatus" value="adventure"{$adckd}/>
                <label for="adventure">Adventure</label>
                <input type="radio" id="romance" name="gstatus" value="romance"{$rockd}/>
                <label for="romance">Romance</label>
              </span>
              <span id="status">
                <input type="radio" id="review" name="gstatus" value="reviewing"{$revckd}/>
                <label for="review"><em>Reviewing</em></label>
                <input type="radio" id="reading" name="gstatus" value="reading"{$reackd}/>
                <label for="reading"><em>Reading</em></label>
                <input type="radio" id="new" name="gstatus" value="new"{$nckd}/>
                <label for="new"><em>New</em></label>
              </span>
_END;
              ?>
            </td>
            <td>
              <span id="suspension">
                <select name="mia" id="mia">
                  <?php
                  if ($mia) {
                    echo <<<_END
                  <option value="inc" selected>Include MIA</option>
                  <option value="exc">Exclude MIA</option>
_END;
                  }
                  else {
                    echo <<<_END
                  <option value="inc">Include MIA</option>
                  <option value="exc" selected>Exclude MIA</option>
_END;
                  }
                  ?>
                </select>
                <select name="pause" id="pause">
                  <?php
                  if ($pause) {
                    echo <<<_END
                  <option value="inc">Include Paused</option>
                  <option value="exc" selected>Exclude Paused</option>
_END;
                  }
                  else {
                    echo <<<_END
                  <option value="inc" selected>Include Paused</option>
                  <option value="exc">Exclude Paused</option>
_END;
                  }
                  ?>
                </select>
              </span>
            </td>
            <td>
              <input type="submit" class="flabel" value="Search">
            </td>
          </tr>
        </table>
	  </form>
    </nav>
    <!--Website Body-->
    <section>
      <table>
        <tr>
          <?php
          // Empty table boolean
          $empty_tbl = true;
          // Single row boolean
          $srow = true;
          // Count manga per row
          $rownum = 0;
          // Count displayed manga
          $mcount = 0;

          // Foreach Loop through all manga to print/display
          foreach ($xmldata->comic as $comic) {
            if ($mcount < 24 && search($comic)) {
              $rownum++;
              // Manga line spacing conditions
              if ($rownum > 3) {
                echo "</tr>\n<tr>";
                $rownum = 1;
                $srow = false;
              }
              
              // Extract all labels
              $atags = explode(',',ucwords($comic->tags,","));
              // Extract all comic/info info into JSON
              $full = exp_json($comic,$atags);
              
              echo <<<_END
                <td class="ind_manga">
                <div class="clickarea" onclick="openFullManga(this.firstElementChild); return false;">
                  <article>{$full}</article>
                  <img src="{$comic->cover}" alt="{$comic->name}" class="poster"/>
                  <p>{$comic->name}</p>
                </div>
                <div class="lbl_btm">
_END;
              // Count number of labels
              $atags_count = count($atags);
              // Count labels per line
              $labelnum = 0;
              // Cumulative labels variable
              $tlabels = "";

              // Foreach Loop through all labels to cumulate
              foreach ($atags as $tag) {
                // Identify label
                $tlabels .= switch_label($tag,true);

                $labelnum++;
                // Labels line spacing condition
                if ($atags_count > 0 && $labelnum >= 3) {
                  $tlabels .= "</div>\n<div class=\"lbl_btm\">";
                  $atags_count -= 3;
                  $labelnum = 0;
                }
              }

              // Close/print/display 'tlabels'
              echo $tlabels . "</div>\n";
              echo "<p class=\"ch_num\"><strong>Chapter: </strong>" . $comic->chapter . "</p>\n<table>";

              // Cumulative list links variable
              $links = "";
              // Foreach Loop through all links to print/display
              foreach ($comic->link as $clink) { $links .= "<tr>\n<td>\n<img src=\"" . switch_link($clink) . "\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\" target=\"_blank\">" . $clink['site'] . "</a>\n</td>\n</tr>\n"; }
              // Close/print/display 'links'
              echo $links . "</table></td>";
              
              if (++$mcount >= 24) { $sid = $comic->attributes(); }
              $empty_tbl = false;
            }
          }
          
          // Close table list
          if ($rownum < 4 && !$empty_tbl) {
            if ($srow) {
              for (; $rownum < 3; $rownum++) { echo "<td class=\"ind_manga\"></td>\n"; }
            }
            echo "</tr>\n";
          }
          // No result catch
          if ($empty_tbl) { echo "<td id=\"empty\"><h3>No Results. Search for Something Else.</h3></td></tr>\n"; }
          ?>
      </table>
    </section>
    <!--Selected Manga-->
    <!--*Full detail view close catch*-->
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
        <?php
        // Full detail to-display check
        if (full) {
          echo "<tr><td id=\"fm_poster\">\n";
          // Foreach Loop through all manga to find specific manga and to print/display
          foreach ($xmldata->comic as $comic) {
            if ($comic['id'] == $id) {
              echo <<<_END
                <img src="{$comic->cover}" alt="{$comic->name}" class="poster"/>
              </td>
              <td colspan="2" id="fm_titles">
                <strong>Primary Title:</strong> {$comic->name} <br>
_END;
              // Foreach Loop through all alternative manga titles/names to print/display
              foreach ($comic->alt as $alts) { echo "<strong>Alternate Title:</strong> " . $alts . " <br>\n"; }
              
              // Extract all labels 
              $atags = explode(',',ucwords($comic->tags,","));
              // Cumulative labels variable
              $tlabels = "";
              // Foreach Loop through all labels to cumulate
              foreach ($atags as $tag) { $tlabels .= switch_label($tag); }
              echo <<<_END
              </td>
            </tr>
            <tr>
              <td colspan="3" id="fm_descriptors">
                <br>
                {$tlabels}
                <br><br>
                <p class="ch_num"><strong>Chapter: </strong>{$comic->chapter}</p>
                <br>
              </td>
            </tr>
_END;
              // Cumulative list links variable
              $links = "<tr class=\"fm_links\">";
              // Count number of links
              $link_count = $comic->link->count();
              // Count links per row
              $link_num = 0;
              
              // Foreach Loop through all links to print/display
        	  foreach ($comic->link as $clink) {
                // Identify link
                $links .= switch_link($clink);
                
                $link_num++;
                // Link line spacing conditions
                if ($link_count > 3 && $link_num >= 3) {
                  $links .= "</tr>\n<tr class=\"fm_links\">";
                  $link_count -= 3;
                  $link_num = 0;
                }
                else if ($link_count <= 3 && $link_count == $link_num) {
                  for (; $link_num < 3; $link_num++) { $links .= "<td></td>\n"; }
                  echo $links . "</tr>\n";
                }
              }
              break;
            }
          }
        }
        ?>
        </table>
    </div>
    <!--Website Footer-->
    <br>
    <footer>
      <p>Not for you :3</p>
    </footer>
  </body>
</html>
