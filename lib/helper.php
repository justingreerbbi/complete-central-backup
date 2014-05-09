<?php

function filesize_formatted( $size ) {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

function flatToTree($flat) {
    $res = array();
    foreach ($flat as $e) {
        $parent =& $res;
        $path = explode('/', $e['filename']);
        for ($i=0;$i<count($path)-1;$i++) {
            if (! array_key_exists($path[$i], $parent)) {
                $parent[$path[$i]] = array( 'file_name'	=>$path[$i],
                                            'children'	=>array());
            }
            $parent =& $parent[$path[$i]]['children'];
        }
        $basename = $path[count($path)-1];
        if ($basename != '') { // A file, not a directory
            $parent[$basename] = array('file_name' => $path[$i]);
        }
    }
    return $res;
}

function listDirectoryByFolder($path) {
    $path = rtrim($path, '/') . '/*';
    $dirs = glob($path, GLOB_ONLYDIR);
    $files = glob($path);
    return array_unique(array_merge($dirs, $files));
}

function delTree($dir) { 
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
  } 
