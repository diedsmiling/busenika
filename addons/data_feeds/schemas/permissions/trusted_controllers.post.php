<?php

//
// $Id: trusted_controllers.post.php 0 2009-12-28 00:00:00Z 2tl $
//

// if allow == true all modes in controller are allowed, no changes needed.
if (!(isset($schema['exim']['allow']) && $schema['exim']['allow'] === true)) {
	$schema['exim']['allow']['cron_export'] = true;
}

?>