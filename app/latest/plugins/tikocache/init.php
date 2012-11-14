<?php

    // Listen for events
    Event::instance('wi3.beforeinit')->callback(array('Wi3TikoCache','beforeInit'));
	Event::instance('wi3.afterexecution.processcontent')->callback(array('Wi3TikoCache','afterExecution'));

?>