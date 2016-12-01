<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 11/23/16
 */
namespace whack\phrases;
#require_once $_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php';

/**
 * Grabs a certain configuration from the configuration file
 * @param string $configuration - configuration to grab
 * @return string - json representation of the configuration.
 */
function grab_config( string $configuration ) : string
{
    $configPath = dirname(__FILE__) . "/../../conf/conf.json";
    $configFile = json_decode(file_get_contents($configPath), true);
    $configVal = "";

    if ( array_key_exists($configuration, $configFile) )
    {
        $configVal = json_encode($configFile[$configuration]);
    }

    return $configVal;
}

/**
 * Grab the json file for the country either specified by the config or
 * provided as a param.
 * @param string $country - the keyboard to pick
 * @return string - the string representing the json for the keyboard
 */
function get_board( string $country = null ) : string
{
    $countryJson = "";
    $keyboardPath = dirname(__FILE__) . "/../../conf/keyboard/";
    $jsonPath = $keyboardPath . $country . ".json";

    if ( $country === null )
    {
        $country = grab_config("currentCountry");
    }

    # grab json array of keyboard from configuration
    $keyboards = json_decode(grab_config("boards"), true);

    if ( array_key_exists($country, $keyboards) && file_exists($jsonPath) )
    {
        $countryJson = file_get_contents($jsonPath);
    }
    else
    {
        # default to us keyboard if keyboard not found
        $countryJson = file_get_contents($keyboardPath . "us.json");
    }

    return $countryJson;
}

//if ( $_SERVER['REQUEST_METHOD'] === "GET" )
//{
//    header('Content-Type: application/json');
//    json_encode(get_board());
//}

