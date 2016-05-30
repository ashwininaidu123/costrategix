<?php

include('simple_html_dom.php');

$URL = $argv[1];

$totalSize = 0;

$totalNumResources = 0;

if (!check_if_html($URL)) {
    $totalSize = get_remote_file_size($URL);
    
    $totalNumResources += 1;
    
    return;
    
}

$totalNumResources += 1;

$html = file_get_html($URL);

// find all images:
foreach ($html->find('img') as $element) {
    
    $size = get_remote_file_size($element->src);
    
    $totalSize = $totalSize + $size;
    
    $totalNumResources += 1;
    
}

// Find all CSS:
foreach ($html->find('link') as $element) {
    
    if (strpos($element->href, '.css') !== false) {
        
        $size = get_remote_file_size($element->href);
        
        $totalSize = $totalSize + $size;
        
        $totalNumResources += 1;
        
    }
}

//find all javascript:
foreach ($html->find('script') as $element) {
    
    if (strpos($element->src, '.js') !== false) {
        
        $size = get_remote_file_size($element->src);
        
        $totalSize = $totalSize + $size;
        
        $totalNumResources += 1;
        
    }
}


function get_remote_file_size($URL)
{
    
    
    $headers = get_headers($URL, 1);
    
    if (isset($headers['Content-Length']))
        return $headers['Content-Length'];
    
    if (isset($headers['Content-length']))
        return $headers['Content-length'];
    
    $c = curl_init();
    
    curl_setopt_array($c, array(
        CURLOPT_URL => $URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3'
        )
    ));
    
    curl_exec($c);
    
    $size = curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
    
    return $size;
    
    curl_close($c);
    
}

function check_if_html($URL)
{
    $ch = curl_init($URL);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    
    $data        = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    
    curl_close($ch);
    
    if (strpos($contentType, 'text/html') !== false)
        return TRUE;
    else
        return FALSE;
}

echo "Total download size for all requests: " . $totalSize . " Bytes\n";
echo "Total number of HTTP requests: " . $totalNumResources . "\n";

?>
