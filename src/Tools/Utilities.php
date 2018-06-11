<?php
/** Vahi\Tools\Utilities class */
namespace Vahi\Tools;

/**
 * The Utilities class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2018 Joost De Cock
 * @license MIT
 */
class Utilities
{
    /**
     * Returns input as a float
     *
     * The frontend allows both decimals (6.5) or fractions (6 1/2)
     * This normalizes that input to a float (6.5)
     *
     * @param string $value The user input
     *
     * @return float The input as float
     */
    static public function asFloat($value) 
    {
        // Do we have a fraction?
        if(!strpos($value,'/')) return floatval(rtrim($value));

        $parts = self::asScrubbedArray($value,'/');

        $divider = $parts[1];
        $parts = self::asScrubbedArray($parts[0],' ');
        $inches = $parts[0];
        $fraction = $parts[1];

        return $inches + ($fraction/$divider);
    }

    static public function asScrubbedArray($data, $separator = ' ')
    {
        $return = false;
        $array = explode($separator, $data);
        foreach ($array as $value) {
            if (rtrim($value) != '') $return[] = rtrim($value);
        }

        return $return;
    }

    /**
     * Helper function to format response and send CORS headers
     *
     * @param $data The data to return
     */
    static public function prepResponse($response, $data, $status=200, $cors='*')
    {
        return $response
            ->withStatus($status)
            ->withHeader('Access-Control-Allow-Origin', $cors)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * Helper function to scrub input clean
     */
    static public function scrub($request, $key, $type='string')
    {
        switch($type) {
            case 'integer':
                $filter = FILTER_SANITIZE_NUMBER_INT;
            break;
            case 'email':
                $filter = FILTER_SANITIZE_EMAIL;
            break;
            default:
                $filter = FILTER_SANITIZE_STRING;
        }

        if(isset($request->getParsedBody()[$key])) return filter_var($request->getParsedBody()[$key], $filter);
        else return false;
    }
}
