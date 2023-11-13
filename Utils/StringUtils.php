<?php

namespace Piwik\Plugins\IndexationViewer\Utils;

final class StringUtils
{
    public static function snakeCase(string $subject): string
    {
        $subject = preg_replace('/\s+/u', '', ucwords($subject));
        $subject = preg_replace('/(.)(?=[A-Z])/u', '$1_', $subject);

        return mb_strtolower($subject, 'UTF-8');
    }
}
