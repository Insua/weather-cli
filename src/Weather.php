<?php

namespace Insua\WeatherCli;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Sabre\Xml\Service;

class Weather
{
    private $city;

    /**
     * Weather constructor.
     * @param $city
     * @throws \Sabre\Xml\ParseException
     */
    public function __construct($city)
    {
        $this->city = $city;
        $this->getXml();
    }

    /**
     * @throws \Sabre\Xml\ParseException
     */
    public function getXml()
    {
        $client = new Client();
        try {
            $response = $client->get('http://rss.accuweather.com/rss/liveweather_rss.asp?metric=1&locCode='.$this->city,
                ['timeout' => 5]);
            $body = $response->getBody();
            $this->getCurrentWeather((string)$body);
        } catch (ConnectException $exception) {
            echo '';
        }
    }

    /**
     * @param $body
     * @throws \Sabre\Xml\ParseException
     */
    public function getCurrentWeather($body)
    {
        $service = new Service();
        $array = $service->parse($body);
        $this->getWeather($array[0]['value'][8]['value'][0]['value']);
    }

    public function getWeather($current)
    {
        $array = explode(':', $current);
        $icon = $this->getIcon(trim($array[1]));
        echo $icon.str_replace('C', '°C', $array[2]);
    }

    public function getIcon($weather)
    {
        if($weather == 'Cloudy' || $weather == 'Mostly Cloudy' || $weather == 'Dreary (Overcast)' || $weather == 'Fog') {
            return "";
        } elseif ($weather == 'Sunny' || $weather == 'Intermittent Clouds' || $weather == 'Mostly Sunny' || $weather == 'Partly Sunny' || $weather == 'Hazy Sunshine' || $weather == 'Hot' ) {
            return '';
        } elseif (strpos($weather, 'Snow') !== false) {
            return '';
        } elseif ($weather == 'Showers' || strpos($weather, 'T-Storms') !== false || strpos($weather, 'Rain') !== false) {
            return '';
        } elseif (strpos($weather, 'Windy') !== false) {
            return '';
        } elseif (strpos($weather, 'Flurries') !== false || strpos($weather, 'Ice') !== false || strpos($weather, 'Sleet') !== false || strpos($weather, 'Cold') !== false) {
            return '';
        } elseif (strpos($weather, 'Clear') !== false || strpos($weather, 'Moonlight') !== false) {
            return '';
        } elseif (strpos($weather, 'Thunderstorms') !== false) {
            return '';
        }
        return '';
    }
}
