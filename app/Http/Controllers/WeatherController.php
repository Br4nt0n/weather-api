<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class WeatherController extends Controller
{
    use  ValidatesRequests;

    protected $weather;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(WeatherService $weather, Logger $logger)
    {
        $this->weather = $weather;
        $this->logger = $logger;
    }

    public function checkoutWeather(Request $request)
    {
        try {

            if ($request->cityId) {
                $this->weather->renderWeather($request->cityId);
            }else{
                throw new \Exception('city id is missing');
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return response($e->getMessage(), 500);
        }
    }
}
