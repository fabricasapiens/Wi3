<?php

    // echo "<h1>" . $page->longtitle . "</h1>";

    // Baseurl is known from main template
    
?>

    <div id='left'>
            <img src='<?php echo $baseurl . "static/images/links_logo.png"; ?>'/>
            <p  style='margin-top: 50px;'>
                Websites van Fabrica Sapiens zijn prachtig om te zien en een plezier om bij te houden.
            </p>
            <p  style='margin-top: 25px;'>
                Klik rechts voor een aantal sites uit ons portfolio.
            </p>
    </div>
    
    <div id='right'>
            <div class='sampleproject'>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/laudatedeum_detail1.png"; ?>'/>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/laudatedeum_detail2.png"; ?>'/>
                <div class='sampleproject_link'><a href='<?php echo Wi3::inst()->urlof->site . "portfolio#laudatedeum"; ?>'>Laudate Deum</a></div>
            </div>
            <div class='sampleproject'>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/amstelodamense_detail1.png"; ?>'/>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/amstelodamense_detail2.png"; ?>'/>
                <div class='sampleproject_link'><a href='<?php echo Wi3::inst()->urlof->site . "portfolio#amstelodamense"; ?>'>Am.St.E.Lo.D.A.M.E.N.S.E.</a></div>
            </div>
            <div class='sampleproject'>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/vsnm_detail1.png"; ?>'/>
                <img src='<?php echo $baseurl . "static/images/portfolio/90/vsnm_detail2.png"; ?>'/>
                <div class='sampleproject_link'><a href='<?php echo Wi3::inst()->urlof->site . "portfolio#vsnm"; ?>'>VSNM</a></div>
            </div>
    </div>
    
    <div style='clear:both; visibility:hidden; font-size: 1px;'>.</div>
