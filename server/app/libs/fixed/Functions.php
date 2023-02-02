<?php

if (!function_exists('session_status')) {
    function session_status()
    {
        if (ini_get('session.auto_start')) {
            return 2;
        }
        return 1;
    }
}

if (!function_exists("xcache_get")) {
    function xcache_get($key)
    {
        return apcu_fetch($key);
    }

    function xcache_set($key, $value, $ttl = 3600)
    {
        return apcu_add($key, $value, $ttl);
    }

    function xcache_unset($key)
    {
        return apcu_delete($key);
    }

    function xcache_isset($key)
    {
        return apcu_exists($key);
    }

    function xcache_inc($key, $value)
    {
        return apcu_inc($key, $value);
    }

    function xcache_dec($key, $value)
    {
        return apcu_dec($key, $value);
    }

    function xcache_clear_cache($key, $a, $b)
    {
        return apcu_clear_cache($key);
    }
}

if (!function_exists("addTmpLog")) {
    function addTmpLog($content, $filename = '')
    {
        if (empty($filename)) {
            $filename = '/tmp/admin_' . date('Ymd') . '.log';
        }

        $content = is_scalar($content) ? $content : var_export($content, true);
        file_put_contents($filename, $content . PHP_EOL, FILE_APPEND);
    }
}