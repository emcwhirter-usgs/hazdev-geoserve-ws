<?php

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';
  include_once '../lib/data/metadata.inc.php';
  include_once 'functions.inc.php';

  $format = param('format', 'php');

  if ($format === 'json') {
    // JSON output
    header('Content-Type: application/json');
    print str_replace("\\'", '"', json_encode($GEOSERVE_ENDPOINTS));
    exit(0);
  }

  // HTML output
  $TITLE = 'Geoserve Documentation';
  $NAVIGATION = true;

  include_once 'template.inc.php';
}
?>

<p>
  Geoserve consists of
  <?php print count($GEOSERVE_ENDPOINTS); ?> web service
  endpoint<?php print (count($GEOSERVE_ENDPOINTS)>1)?'s':''; ?>. Each end
  point has a slightly different API and different available data. For more
  details about each endpoint, please click a link below.
</p>

<ul class="endpoints">
  <li><?php print implode('</li><li>', $endpointLinks); ?></li>
</ul>
