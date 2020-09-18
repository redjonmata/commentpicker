<?php
if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('em_strtolower')) {
    function em_strtolower($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($str, 'utf-8');
        }
        return strtolower($str);
    }
}

if (!function_exists('em_strtoupper')) {
    function em_strtoupper($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($str, 'utf-8');
        }
        return strtoupper($str);
    }
}

if (!function_exists('em_strlen')) {
    function em_strlen($str, $encoding = 'utf-8')
    {
        if (is_array($str)) {
            return false;
        }
        $str = html_entity_decode($str, ENT_COMPAT, 'utf-8');
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, $encoding);
        }
        return strlen($str);
    }
}

if (!function_exists('em_strpos')) {
    function em_strpos($str, $find, $offset = 0, $encoding = 'utf-8')
    {
        if (function_exists('mb_strpos')) {
            return mb_strpos($str, $find, $offset, $encoding);
        }
        return strpos($str, $find, $offset);
    }
}

if (!function_exists('em_strrpos')) {
    function em_strrpos($str, $find, $offset = 0, $encoding = 'utf-8')
    {
        if (function_exists('mb_strrpos')) {
            return mb_strrpos($str, $find, $offset, $encoding);
        }
        return strrpos($str, $find, $offset);
    }
}

if (!function_exists('route_sorter')) {
    function route_sorter($routeNames, $except = null)
    {
        $routes = [];
        $except = isset($except) ? $except : [];

        foreach ($routeNames as $routeName) {
            $routeNameArray = explode('.', $routeName);
            if (!in_array($routeNameArray[0], $except)) {
                $prefix = $routeNameArray[0];
                // unset($routeNameArray[0]);
                $routes[$prefix][] = implode('.', $routeNameArray);
            }
        }
        return $routes;
    }
}

if (!function_exists('european_date_format')) {
    function european_date_format($date)
    {
        return isset($date)
            ? date('d/m/Y H:i:s', strtotime($date))
            : null;
    }
}

if (!function_exists('generate_random_string')) {
    function generate_random_string($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}

if (!function_exists('get_route_id')) {
    function get_route_id($route)
    {
        list($found, $routeInfo, $params) = $route;
        return $params['id'];
    }
}

if (!function_exists('public_path')) {
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

if (!function_exists('create_directory')) {
    function create_directory($targetDirectory, $newDirectory)
    {
        if (!is_dir($newDirectory)) {
            $foldersArray = explode('/', $newDirectory);

            # if nested path ("images/users"), create folders in sequence
            if (count($foldersArray) > 1) {
                foreach ($foldersArray as $folder) {
                    $targetDirectory = $targetDirectory . '/' . $folder;
                    if (!is_dir($targetDirectory)) {
                        mkdir($targetDirectory, 0775);
                    }
                }
            } else {
                mkdir($path, 0775);
            }
        }
    }
}

if (!function_exists('to_camel_case')) {
    function to_camel_case($string)
    {
        return str_replace(' ', '', ucwords(strtolower($string)));
    }
}

if (!function_exists('trim_slash')) {
    function trim_slash($string)
    {
        return rtrim($string, '/');
    }
}
