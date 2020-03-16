<?php

namespace App\Services;

use Illuminate\View\Factory;
use Illuminate\Cache\CacheManager;
use League\Flysystem\Exception;

class WeatherService
{
    /**
     * Factory view.
     *
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * Weather config.
     *
     * @var array
     */
    protected $config;

    /**
     * API key
     * @var string
     */
    private $apiKey = '397c666c920f961dde141cbadecc5748';

    /**
     * Base API request path
     * @var string
     */
    private $baseUrl = 'http://api.openweathermap.org/data/2.5/';

    /**
     * Create a new service instance.
     *
     * @param \Illuminate\Cache\CacheManager $cache
     * @param \Illuminate\View\Factory $view
     * @param array $config
     */
    public function __construct(CacheManager $cache, Factory $view, $config)
    {
        $this->cache = $cache;
        $this->view = $view;
        $this->config = $config;
    }

    /**
     * Render weather widget.
     *
     * @param string $cityId
     * @return string
     * @throws Exception
     * @internal param array $options
     */
    public function renderWeather(string $cityId): string
    {
        $options = $this->config;

        $cacheKey = 'Weather.' . md5(implode($options));

        if ($this->config['cache'] && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $current = $this->getWeather($cityId);
        //TODO: change schema - forecast by days is available for paid accounts only
        $forecast = $this->getWeather($cityId, 'forecast', $options['days']);

        if ($current['cod'] !== 200) {
            throw new Exception('Unable to load weather');
        }
        $html = $this->view->make("{$this->config['views']}{$this->config['style']}", [
            'current' => $current,
            'forecast' => $forecast
        ])->render();

        if ($this->config['cache']) {
            $this->cache->put($cacheKey, $html, $this->config['cache']);
        }

        return $html;
    }

    /**
     * Build & send request
     * @param string $cityId
     * @param string $type
     * @param int $days
     * @param string $lang
     * @return array|string
     */
    private function getWeather(string $cityId = '542420', string $type = 'weather', int $days = 1, string $lang = 'ru'): array
    {
        //TODO: perform distant method for options query
        return $this->sendRequest("{$this->baseUrl}{$type}?id={$cityId}&appid={$this->apiKey}&cnt={$days}&units=metric&mode=json&lang={$lang}");
    }


    /**
     * @param string $url
     * @return mixed
     */
    private function sendRequest(string $url): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_MAXCONNECTS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true, JSON_THROW_ON_ERROR);
    }

    /**
     *  Wind direction
     * @param string $deg
     * @return string
     */
    public static function getWindDirection(string $deg): string
    {
        switch ($deg) {
            case ($deg >= 360 && $deg < 90):
                return 'Северный';
            case ($deg >= 90 && $deg < 180):
                return 'Восточный';
            case ($deg >= 180 && $deg < 270):
                return 'Южный';
            case ($deg >= 270 && $deg < 360):
                return 'Западный';
            default:
                return '';
        }
    }
}