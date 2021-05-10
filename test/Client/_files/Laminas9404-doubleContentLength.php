<?php

$clength = filesize(__FILE__);

header(sprintf('Content-length: %s', $clength));
header(sprintf('Content-length: %s', $clength), false);

readfile(__FILE__);
