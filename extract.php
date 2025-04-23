<?php
$zip = new ZipArchive;
$file = './vendor.zip'; 
if ($zip->open($file) === TRUE) {
    $zip->extractTo('./'); 
    $zip->close();
    echo 'Extraction successful!';
} else {
    echo 'Failed to extract the ZIP file.';
}
?>