<?php
$resqueDir = dirname(__FILE__);
echo $resqueDir;exit;
 
$includePath = array(
    $resqueDir . '/lib',
    $resqueDir . '/lib/Resque',
    $resqueDir . '/lib/Resque/Failure',
    $resqueDir . '/lib/Resque/Job',
    $resqueDir . '/demo',
);

array_push($includePath, get_include_path());
set_include_path(join(PATH_SEPARATOR, $includePath));


