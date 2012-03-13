
    <--
    
        In the top-right a big button with "Download & Install in 3 minutes"
        
        At the very bottom a dark-grey area with copyrights etc. This should be in the main template.
    
    -->
    
    <div id='topdefinition'>
        <?php
            $this->image("wi3_logo_blue on white_100x70.png");
        ?>
        <p><strong>Wi3</strong> is a modern and flexible <strong>Open Source HTML5 CMS</strong>.</p>
        <p style='font-size: 16px;'>It focuses on a great experience for end-users, designers and developeres alike.</p>
        <p style='font-size: 16px;'>With its easy setup you can <a href="<?php echo Wi3::inst()->urlof->page("Download and install"); ?>">Download and install Wi3 in 3 minutes</a>!</p>
        <div class='floatfix'>.</div>
    </div>
    
    <div id='targetgroups'>
    
        <div class='targetgroup'>

            <cms type='editableblock' name='group1'>
            </cms>
            
        </div>
        
        <div class='targetgroup'>

            <cms type='editableblock' name='group2'>
            </cms>
            
        </div>
        
        <div class='targetgroup'>

            <cms type='editableblock' name='group3'>
            </cms>
            
        </div>
        
    </div>
