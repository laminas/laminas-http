<?php

// intentional use of case-insensitive header name
header('Transfer-encoding: chunked');
header('content-encoding: gzip');
flush();

// terminate chunked transfer properly
echo "00\r\n\r\n";
