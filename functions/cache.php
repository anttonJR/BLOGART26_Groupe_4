<?php
function getCached($key, $expiration = 3600) {
    $file = __DIR__ . '/../cache/' . md5($key) . '.cache';
    
    if (file_exists($file) && (time() - filemtime($file)) < $expiration) {
        return unserialize(file_get_contents($file));
    }
    
    return null;
}

function setCache($key, $data) {
    $file = __DIR__ . '/../cache/' . md5($key) . '.cache';
    file_put_contents($file, serialize($data));
}
?>