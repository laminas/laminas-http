<?php


$clength = filesize(__FILE__);

header(sprintf('Content-length: %s', $clength));