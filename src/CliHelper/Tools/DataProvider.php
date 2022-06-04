<?php

namespace CliHelper\Tools;

class DataProvider
{
    /**
     * 递归或非递归扫描目录，可返回相对目录的文件列表或绝对目录的文件列表
     *
     * @param  string      $dir       目录
     * @param  bool        $recursive 是否递归扫描子目录
     * @param  bool|string $relative  是否返回相对目录，如果为true则返回相对目录，如果为false则返回绝对目录
     * @return array|false
     * @since 2.5
     */
    public static function scanDirFiles(string $dir, bool $recursive = true, $relative = false)
    {
        $dir = rtrim($dir, '/');
        if (!is_dir($dir)) {
            return false;
        }
        $r = scandir($dir);
        if ($r === false) {
            return false;
        }
        $list = [];
        if ($relative === true) {
            $relative = $dir;
        }
        foreach ($r as $v) {
            if ($v == '.' || $v == '..') {
                continue;
            }
            $sub_file = $dir . '/' . $v;
            if (is_dir($sub_file) && $recursive) {
                $list = array_merge($list, self::scanDirFiles($sub_file, $recursive, $relative));
            } elseif (is_file($sub_file)) {
                if (is_string($relative) && mb_strpos($sub_file, $relative) === 0) {
                    $list[] = ltrim(mb_substr($sub_file, mb_strlen($relative)), '/');
                } elseif ($relative === false) {
                    $list[] = $sub_file;
                } else {
                    echo ("Relative path is not generated: wrong base directory ({$relative})");
                    return false;
                }
            }
        }
        return $list;
    }

    /**
     * 检查路径是否为相对路径（根据第一个字符是否为"/"来判断）
     *
     * @param  string $path 路径
     * @return bool   返回结果
     * @since 2.5
     */
    public static function isRelativePath(string $path): bool
    {
        return strlen($path) > 0 && $path[0] !== '/';
    }
}