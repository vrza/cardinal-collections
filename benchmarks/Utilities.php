<?php

/**
 * @return int
 * @throws Exception
 */
function linuxRssUsage()
{
    if (PHP_OS != 'Linux') {
        throw new Exception('linuxRssUsage can only run on Linux');
    }
    $RSS_F = 23;
    $FS = ' ';
    $PAGE_SIZE_B = 4 * 1024;
    $file = fopen('/proc/self/stat', 'r');
    $line = fgets($file);
    fclose($file);
    $stats = explode($FS, $line);
    $rss = $PAGE_SIZE_B * (int)$stats[$RSS_F];
    return $rss;
}
