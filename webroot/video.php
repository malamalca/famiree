<?php
$video = fopen('http://192.168.88.9:9090/stream/video.mjpeg', "rb");

// Forward headers, $http_response_header is populated by fopen call
foreach ($http_response_header AS $header) {
    header($header);
}

// Output contents of flv
while (!feof($video)) {
    print (fgets($video));
}

fclose($video);