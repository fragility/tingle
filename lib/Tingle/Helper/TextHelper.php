<?php
namespace Tingle\Helper;

use Tingle\Inflector;
use Tingle\Cycle;

class TextHelper
{
    static private $cycles = array();

    public static function pluralize($count, $singular, $number_format = null)
    {
        $word = ($count == 1) ? $singular : Inflector::pluralize($singular);

        return ($number_format === null) ? "$count $word" : sprintf($number_format, $count).' '.$word;
    }

    /**
     * Truncate a string to a certain length, if necessary.
     *
     * @param string $text Text to truncate
     * @param integer $length Maximum length before truncating
     * @param string $etc String to put at the end of truncated text
     * @param boolean $break_words Whether to break in the middle of a word
     * return string Truncated text
     */
    public static function truncate($text, $length, $etc = '...', $break_words = false)
    {
        // Shortcut for zero length
        if ($length == 0) return '';

        if (strlen($text) > $length)
        {
            $length -= min($length, strlen($etc));
            if (!$break_words)
            {
                $text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length+1));
            }

            return substr($text, 0, $length) . $etc;
        }
        else
        {
            return $text;
        }
    }

    /**
     * Cycle through a list of strings
     */
    public static function cycle()
    {
        $values = func_get_args();
        $last = array_pop($values);
        if (is_array($last))
        {
            $name = $last['name'];
        }
        else
        {
            $name = 'default';
            array_push($values, $last);
        }

        $cycle = self::get_cycle($name);
        if (!$cycle || $values != $cycle->values)
        {
            $cycle = new Cycle($values);
            self::set_cycle($name, $cycle);
        }

        return strval($cycle);
    }

    public static function current_cycle($name = 'default')
    {
        $cycle = self::get_cycle($name);
        return $cycle ? $cycle->current_value() : null;
    }

    public static function reset_cycle($name = 'default')
    {
        $cycle = self::get_cycle($name);
        if ($cycle) $cycle->reset();
    }

    private static function set_cycle($name, $cycle)
    {
        self::$cycles[$name] = $cycle;
    }

    private static function get_cycle($name)
    {
        return (isset(self::$cycles[$name]) ? self::$cycles[$name] : null);
    }
}
?>