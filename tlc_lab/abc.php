<?php

define("DATA_PATH","D:/files/tlc/data");
$courseId = 1;
echo filesize(DATA_PATH . "/coursepacket/" . $courseId . ".zip");