<?php

namespace Coderjerk\Plugger\Utils;

class Html
{
    public static function wrap($string, $tag = "<div>", $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"{$value}\"";
        }
        return "<$tag $attrs>" . wp_kses_post($string) . "</$tag>";
    }
}
