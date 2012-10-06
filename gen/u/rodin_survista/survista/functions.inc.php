<?php

/*
 * Survista global functions
 *
 * Copyright 2011 HTW Chur.
 */

/**
 * Method to savely debug variables to html output
 * 
 * @param <type> $in 
 */
function html_out($in) {
    echo htmlspecialchars(var_export($in, true)) . PHP_EOL;
}

/*
 * Caching functions
 */

function cacheHash($obj) {
    if (is_array($obj)) {
        $arr = array();
        foreach ($obj as $value) {
            $arr[] = mb_strtolower($value, 'UTF8');
        }
        asort($arr);
        $key = implode("_", $arr);
    } else {
        $key = mb_strtolower($obj, 'UTF8');
    }
    $hash = md5($key);
    return $hash;
}

function getCache($path, $hash) {
    $file = $path . $hash . '.json';
    if (file_exists($file)) {
        return file_get_contents($file);
    } else {
        return false;
    }
}

function setCache($path, $hash, $content) {
    if (is_dir($path)) {
        return file_put_contents($path . $hash . '.json', $content);
    } else {
        error_log("ERROR: cache path doesn't exist. Create dir or disable caching");
    }
}

function getCacheKey($result, $lang) {
    if (is_array($result)) {
        $cacheKey = $result;
    } else {
        $cacheKey = array($result);
    }
    $cacheKey[] = $lang;
    return $cacheKey;
}

function getCacheContent($result, $lang) {
    if (!SURVISTA_CACHES) {
        return false;
    }
    $cacheHash = cacheHash(getCacheKey($result, $lang));
    $cachePath = SURVISTA_CACHE_PATH;
    return getCache($cachePath, $cacheHash);
}

function setCacheContent($result, $lang, $data) {
    if (!SURVISTA_CACHES) {
        return;
    }
    $cacheHash = cacheHash(getCacheKey($result, $lang));
    $cachePath = SURVISTA_CACHE_PATH;
    setCache($cachePath, $cacheHash, $data);
}

?>