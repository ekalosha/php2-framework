<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains utils Class for String functions
 *
 * PHP version 5
 * @category   System Utilities
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\Utils;

/**
 * Utils class for string functions
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: string.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\Utils
 */
class PHP2_Utils_String
{
    /**
     * Returns random string with specified length
     *
     * @param   string  $strSize Random string size
     * @param   boolean $onlyWordSymbols Return only Word symbols
     * @return  string
     * @access  public
     * @static
     */
    public static function getRandomString($strSize = 8, $onlyWordSymbols = true)
    {
        $result = '';
        $symbolStr = '';

        for ($i = 0; $i < 255; $i++) $symbolStr .= chr($i);

        $eregPattern = ($onlyWordSymbols) ? '/[^0-9A-Za-z\_\-\.]/' : "/[^0-9A-Za-z\_\%\&\-\^]/";
        $symbolStr = preg_replace($eregPattern, '', $symbolStr);

        $symbolsCount = strlen($symbolStr);
        for ($i = 0; $i < $strSize; $i++)
        {
            mt_srand ((double) microtime()*($i + 13)*100000);
            $randomIndex = mt_rand(0, $symbolsCount - 1);
            $result .= $symbolStr{$randomIndex};
        }

        return $result;
    }

    /**
     * Returns substring
     *
     * @param   string  $str Initial String
     * @param   integer $from Position from
     * @param   integer $to Position to
     * @param   float $koefOfMaxLength Max increase koef
     * @return  string
     * @access  public
     * @static
     */
    public static function getSubstring($str, $from = 0, $to = 100, $koefOfMaxLength = 1)
    {
        if ((strlen($str) > ($to - $from)))
        {
            $spacePos = strpos($str, ' ', $to);
            $toPos = ($spacePos && ($spacePos < $koefOfMaxLength * $to)) ? $spacePos + 4 : $to;

            return substr($str, $from, $toPos - 4).' ...';
        }

        return $str;
    }

    /**
     * Returns text as HTML.
     *
     * @param   string $string Converted Text String
     * @return  string
     * @access  public
     * @static
     */
    public static function getTextAsHTML($string)
    {
        $result = nl2br($string);
        $result = str_replace("  ", "&nbsp; &nbsp; ", $result);

        return $result;
    }

    /**
     * Conversts IP address represented by string into Integer value
     *
     * @param   string $ipString String representation of the IP address
     * @return  integer
     * @access  public
     * @static
     */
    public static function convertIPToInt($ipString)
    {
        $ipParts = explode('.', $ipString);

        return (16777216 * $ipParts[0] + 65536 * $ipParts[1] + 256 * $ipParts[2] + $ipParts[3]);
    }

    /**
     * Return formatted float value
     *
     * @param   string $floatValue Converted Text String
     * @param   string $digits Float value Digits
     * @return  string
     * @access  public
     * @static
     */
    public static function floatToStr($floatValue, $digits = 2)
    {
        preg_match('/[\+\-\d]+[\.\,]{0,1}[\d]{0,'.$digits.'}/', $floatValue, $matches);

        return ((isset($matches[0])) ? $matches[0] : 0);
    }

    /**
     * Return validated string to use as XML Text node or Attribute
     *
     * This function automatically translate some entities to XML valid form.
     * A list of translated entities is below:
     *
     * # &amp; refers to an ampersand (&) - yes
     * # &lt; refers to a less-than symbol (<) - yes
     * # &gt; refers to a greater-than symbol (>) - yes
     * # &quot; refers to a double-quote mark (") - yes
     * # &apos; refers to an apostrophe (') - yes
     *
     * @param   string $xmlTextContent Text of the XML node or attribute
     * @return  string
     * @access  public
     * @static
     */
    public static function validateXMLText($xmlTextContent)
    {
        return str_replace(array('&', '"', '\'', '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $xmlTextContent);
    }

}
