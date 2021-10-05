<?php
$xmldata = simplexml_load_file("files/bookmarks.xml") or die("Failed to load");
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
  </head>
  <body>
    <!--Website Head-->
    <header class="jumbotron">
      <img src="css/images/favicon.ico" alt="logo" id="logo">
      <h1>Manga Bookmarks</h1>
    </header>
	<nav>
	  <form method="get">
	    <label for="mname">Manga Name:</label>
        <input type="text" id="mname" class="form-control form-control-sm" name="mname"/>
        <br>
        <table>
          <tr>
            <td>
              <span id="genre">
                <input type="radio" id="all" name="gstatus" value="all" checked/>
                <label for="all">All</label>
                <input type="radio" id="casual" name="gstatus" value="casual"/>
                <label for="casual">Casual</label>
                <input type="radio" id="adventure" name="gstatus" value="adventure"/>
                <label for="adventure">Adventure</label>
                <input type="radio" id="romance" name="gstatus" value="romance"/>
                <label for="romance">Romance</label>
              </span>
              <span id="status">
                <input type="radio" id="review" name="gstatus" value="review"/>
                <label for="review"><em>Reviewing</em></label>
                <input type="radio" id="reading" name="gstatus" value="reading"/>
                <label for="reading"><em>Reading</em></label>
              </span>
            </td>
            <td>
              <span id="suspension">
                <select name="mia" id="mia">
                  <option value="inc">Include MIA</option>
                  <option value="exc" selected>Exclude MIA</option>
                </select>
                <select name="pause" id="pause">
                  <option value="inc" selected>Include Paused</option>
                  <option value="exc">Exclude Paused</option>
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
          $mcount = $xmldata->count();
          $rownum = 0;
          
          foreach ($xmldata->comic as $comic) {
            echo <<<_END
            <td class="ind_manga">
            <a href="?full=1&id={$comic->attributes()}" class="sltctr">
              <img src="{$comic->cover}" alt="{$comic->name}" class="poster"/>
              <p>{$comic->name}</p>
            </a>
            <div class="lbl_btm">
_END;
            $atags = explode(',',ucwords($comic->tags,","));
            $atags_count = count($atags);
            $labelnum = 0;
            $tlabels = "";
            
            foreach ($atags as $tag) {
              switch (strtolower($tag)) {
                case "casual":
                case "adventure":
                case "romance":
                  $tlabels .= "<span class=\"genre_label\">" . $tag . "</span> ";
                  break;
                case "suggestive":
                case "erotic":
                  $tlabels .= "<span class=\"maturity_label\">" . $tag . "</span> ";
                  break;
                case "paused":
                case "reading":
                case "reviewing":
                case "new":
                  $tlabels .= "<span class=\"status_label\">" . $tag . "</span> ";
                  break;
                case "mia":
                  $tlabels .= "<span class=\"status_label\">" . strtoupper($tag) . "</span> ";
                  break;
                default:
                  $tlabels .= "<span class=\"maturity_label\">Error</span> ";
                  break;
              }
              
              $labelnum++;
              if ($atags_count > 0 && $labelnum >= 3) {
                $tlabels .= "</div>\n<div class=\"lbl_btm\">";
                $atags_count -= 3;
                $labelnum = 0;
              }
              else if ($atags_count <= 0) {
                echo "</div>\n";
              }
            }
            
            echo $tlabels . "</div>\n";
            echo "<p class=\"ch_num\"><strong>Chapter: </strong>" . $comic->chapter . "</p>\n<table>";
            
            foreach ($comic->link as $clink) {
              switch ($clink['site']) {
                case "MangaDex":
                  echo "<tr><td>\n<img src=\"https://mangadex.org/favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Manga Sushi":
                  echo "<tr><td>\n<img src=\"https://mangasushi.net/wp-content/uploads/2020/11/cropped-MS_LOGO-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Luminous Scans":
                  echo "<tr><td>\n<img src=\"https://www.luminousscans.com/fypadsuh/2021/04/cropped-logo-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Setsu Scans":
                  echo "<tr><td>\n<img src=\"https://i2.wp.com/setsuscans.com/wp-content/uploads/2021/04/cropped-no.png?fit=32%2C32&\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "MangaKakalot":
                  echo "<tr><td>\n<img src=\"https://mangakakalot.com/favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Bato.To":
                  echo "<tr><td>\n<img src=\"https://static.animemark.com/img/batoto/favicon.ico?v0\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "WhimSubs":
                  echo "<tr><td>\n<img src=\"https://whimsubs.xyz/static/img/logo.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "LHTranslation":
                  echo "<tr><td>\n<img src=\"css/images/lht_favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Manganato":
                  echo "<tr><td>\n<img src=\"https://readmanganato.com/favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "ManhuaPlus":
                  echo "<tr><td>\n<img src=\"https://manhuaplus.com/wp-content/uploads/2020/07/cropped-manhua-vuong-den-1-32x32.jpg\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Flame Scans":
                  echo "<tr><td>\n<img src=\"https://flamescans.org/eternalflame/2021/03/cropped-fds-1-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Reaper Scans":
                  echo "<tr><td>\n<img src=\"css/images/rs_favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "1st Kiss Manga":
                  echo "<tr><td>\n<img src=\"https://1stkissmanga.io/favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Leviatan Scans":
                  echo "<tr><td>\n<img src=\"css/images/ls_favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Asura Scans":
                  echo "<tr><td>\n<img src=\"https://www.asurascans.com/wp-content/uploads/2021/03/cropped-Group_1-1-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Nani? Scans":
                  echo "<tr><td>\n<img src=\"https://naniscans.com/assets/icon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Tritinia Scans":
                  echo "<tr><td>\n<img src=\"https://tritinia.com/favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "247 Manga":
                  echo "<tr><td>\n<img src=\"https://247manga.com/wp-content/uploads/2021/05/cropped-247manga-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Kun Manga":
                  echo "<tr><td>\n<img src=\"https://kunmanga.com/wp-content/uploads/2021/06/cropped-kun-favicon-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Immortal Updates":
                  echo "<tr><td>\n<img src=\"https://immortalupdates.com/wp-content/uploads/2017/10/SMALL-LOGO.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Gourmet Scans":
                  echo "<tr><td>\n<img src=\"css/images/gs_favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Reset Scans":
                  echo "<tr><td>\n<img src=\"css/images/res_favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Isekai Scan":
                  echo "<tr><td>\n<img src=\"css/images/is_favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Manga Great":
                  echo "<tr><td>\n<img src=\"https://mangagreat.com/wp-content/uploads/2017/10/cropped-cropped-mangagreat-e1610206927661-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Galaxy Degen Scans":
                  echo "<tr><td>\n<img src=\"https://gdegenscans.xyz/wp-content/uploads/2021/03/cropped-favicon-32x32.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "Cat Manga":
                  echo "<tr><td>\n<img src=\"https://images.catmanga.org/favicon.png\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                case "WordPress":
                  echo "<tr><td>\n<img src=\"https://s0.wp.com/i/favicon.ico\" alt=\"" . $clink['site'] . "\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
                default:
                  echo "<tr><td>\n<img src=\"css/images/not-available.png\" alt=\"None\" class=\"sicon\"/>\n<a href=\"" . $clink . "\">" . $clink['site'] . "</a>\n</td></tr>\n";
                  break;
              }
            }
            
            echo "</table></td>";
            
            $rownum++;
            if ($mcount > 0 && $rownum >= 3) {
              echo "</tr>\n<tr>";
              $mcount -= 3;
              $rownum = 0;
            }
            else if ($mcount <= 0) {
              echo "</tr>\n";
            }
          }
          ?>
      </table>
    </section>
    <!--Selected Manga-->
    <div id="fm_catch" onclick="closeManga(); return false;"></div>
    <div id="full_manga">
      <table>
        <tr>
          <td id="fm_poster">
      		<img src="https://uploads.mangadex.org/covers/1b0c80ec-c8db-43c1-840c-6d126c78d09f/14f185d6-7782-473c-97ea-9c2ddfedbde9.jpg" alt="Shijou Saikyou no Mahou Kenshi, F Rank Boukensha ni Tensei Suru" class="poster"/>
          </td>
          <td colspan="2" id="fm_titles">
            <strong>Primary Title:</strong> Shijou Saikyou no Mahou Kenshi, F Rank Boukensha ni Tensei Suru <br>
            <strong>Alternate Title:</strong> The Strongest Magical Swordsman Ever Reborn as an F-Rank Adventurer
          </td>
        </tr>
        <tr>
          <td colspan="3" id="fm_descriptors">
            <br>
            <span class="genre_label">Casual</span> <span class="genre_label">Adventure</span>
            <br><br>
            <p class="ch_num"><strong>Chapter: </strong>47</p>
            <br>
          </td>
        </tr>
        <tr id="fm_links">
          <td>
            <img src="https://mangadex.org/favicon.ico" alt="MangaDex" class="sicon"/>
            <a href="https://mangadex.org/title/1b0c80ec-c8db-43c1-840c-6d126c78d09f">MangaDex</a>
          </td>
          <td>
            <img src="https://mangasushi.net/wp-content/uploads/2020/11/cropped-MS_LOGO-32x32.png" alt="Manga Sushi" class="sicon"/>
            <a href="https://mangasushi.net/manga/shijou-saikyou-no-mahou-kenshi-f-rank-boukensha-ni-tensei-suru-1879/">Manga Sushi</a>
          </td>
          <td>
            
          </td>
        </tr>
      </table>
    </div>
    <!--Website Footer-->
    <br>
    <footer>
      <p>Not for you :3</p>
    </footer>
  </body>
</html>