<?php

namespace TechTailor\CoinDCXApi;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use TechTailor\CoinDCXApi\Traits\HandlesResponseErrors;

class CoinDCXApi
{
    use HandlesResponseErrors;

    protected $api_key;             // API key
    protected $api_secret;          // API secret
    protected $api_url;             // API base URL
    protected $public_url;          // API public URL
    protected $synced = false;
    protected $response = null;
    protected $no_time_needed = [
        'exchange/ticker',
        'exchange/v1/markets',
        'exchange/v1/markets_details',
        'market_data/trade_history',
        'market_data/orderbook',
        'market_data/candles',
    ];

    /**
     * Constructor for CoinDCX.
     *
     * @param string $key     API key
     * @param string $secret  API secret
     * @param string $api_url API base URL (see config for example)
     * @param int    $timing  CoinDCX API timing setting (default 10000)
     */
    public function __construct($api_key = null, $api_secret = null, $api_url = null, $timing = 10000)
    {
        $this->api_key = (!empty($api_key)) ? $api_key : config('coindcx-api.auth.key');
        $this->api_secret = (!empty($api_secret)) ? $api_secret : config('coindcx-api.auth.secret');
        $this->api_url = (!empty($api_url)) ? $api_url : config('coindcx-api.urls.api');
        $this->public_url = (!empty($api_url)) ? $api_url : config('coindcx-api.urls.public');
    }

    /**
     * API Key and Secret Key setter function.
     * It's required for all Authenticated endpoints.
     * https://docs.coindcx.com/#authentication.
     *
     * @param string $key    API Key
     * @param string $secret API Secret
     */
    public function setAPI($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    //------ PUBLIC API CALLS --------
    //---- Security Type : NONE ------
    /*
    * getTicker
    * getMarkets
    * getMarketsDetails
    * getMarketTrades
    */

    /**
     * Get CoinDCX Exchange Ticker.
     *
     * @return mixed
     */
    public function getTicker()
    {
        return $this->publicRequest('exchange/ticker');
    }

    /**
     * Get CoinDCX Markets Data.
     *
     * @return mixed List of all markets (coin-pair) available on coindcx.
     */
    public function getMarkets()
    {
        return $this->publicRequest('exchange/v1/markets');
    }

    /**
     * Get CoinDCX Markets Details.
     *
     * @return mixed
     */
    public function getMarketsDetails()
    {
        return $this->publicRequest('exchange/v1/markets_details');
    }

    /**
     * Get CoinDCX Market Trades History.
     * This API provides with a sorted list of most recent 30 trades
     * by default if limit parameter is not passed.
     *
     * @param string $pair  (pair from Market Details API)
     * @param int    $limit Default: 30; Max: 500
     *
     * @return mixed
     */
    public function getMarketTrades($pair, $limit = 30)
    {
        $data = [
            'pair'  => $pair,
            'limit' => $limit,
        ];

        $this->api_url = $this->public_url;

        return $this->publicRequest('market_data/trade_history', $data);
    }

    /**
     * Get CoinDCX Order Book.
     * This API provides with a sorted list (in descending order)
     * of bids and asks for a particular pair.
     *
     * @param string $pair (pair from Market Details API)
     *
     * @return mixed
     */
    public function getOrderBook($pair)
    {
        $data = [
            'pair' => $pair,
        ];

        $this->api_url = $this->public_url;

        return $this->publicRequest('market_data/orderbook', $data);
    }

    /**
     * Get CoinDCX Candles.
     * This API provides with a sorted list (in descending order according to time key)
     * of candlestick bars for given pair. Candles are uniquely identified by their time.
     *
     * @param string $pair     (pair from Market Details API)
     * @param string $interval 1m,5m,15m,30m,1h,2h,4h,6h,8h,1d,3d,1w,1M
     *
     * @return mixed
     */
    public function getCandles($pair, $interval = '5m', $startTime = null, $endTime = null)
    {
        $data = [
            'pair'     => $pair,
            'interval' => $interval,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];

        $this->api_url = $this->public_url;

        return $this->publicRequest('market_data/candles', $data);
    }

    //------ PRIVATE API CALLS ----------
    //--- Security Type : AUTHENTICATED -----
    /*
    * getAccountInfo
    * getBalances
    * getTradeHistory
    * getActiveOrders
    */

    /**
     * Get current account information.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAccountInfo()
    {
        $response = $this->privateRequest('exchange/v1/users/info');

        return $response;
    }

    /**
     * Get current account balances.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getBalances()
    {
        $response = $this->privateRequest('exchange/v1/users/balances');

        return $response;
    }

    /**
     * Get current account trade history.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getTradeHistory()
    {
        $response = $this->privateRequest('exchange/v1/orders/trade_history');

        return $response;
    }

    /**
     * Get current account active orders.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getActiveOrders()
    {
        $response = $this->privateRequest('exchange/v1/orders/active_orders');

        return $response;
    }

    /**
     * Make public requests (Security Type: NONE).
     *
     * @param string $url    URL Endpoint
     * @param array  $params Required and optional parameters
     * @param string $method GET, POST, PUT, DELETE
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function publicRequest($url, $params = [], $method = 'GET')
    {
        $url = $this->api_url.$url;

        // Adding parameters to the url.
        $url = $url . '?' . http_build_query($params);

        return $this->sendApiRequest($url, $method);
    }

    /**
     * Make private requests (Security Type: USER_DATA).
     *
     * @param string $url    URL Endpoint
     * @param array  $params Required and optional parameters.
     */
    private function privateRequest($url, $params = [], $method = 'POST')
    {
        // Build the POST data string
        if (!in_array($url, $this->no_time_needed)) {
            $params['timestamp'] = $this->milliseconds();
        }

        // Set API key and sign the message
        $signature = hash_hmac('sha256', json_encode($params), $this->api_secret);

        $url = $this->api_url.$url;

        return $this->sendApiRequest($url, $method, $params['timestamp'], $signature);
    }

    /**
     * Send request to CoinDCX API for Public or Authenticated Requests.
     *
     * @param string $url    URL Endpoint with Query & Signature
     * @param string $method GET, POST, PUT, DELETE
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function sendApiRequest($url, $method, $timestamp = null, $signature = null)
    {
        try {
            if ($method == 'POST') {
                $response = Http::withHeaders([
                    'X-AUTH-APIKEY'    => $this->api_key,
                    'X-AUTH-SIGNATURE' => $signature,
                ])->post($url, [
                    'timestamp' => $timestamp,
                ]);
            } elseif ($method == 'GET') {
                $response = Http::withHeaders([
                    'X-AUTH-APIKEY'    => $this->api_key,
                    'X-AUTH-SIGNATURE' => $signature,
                ])->get($url);
            }
        } catch (ConnectionException $e) {
            return $error = [
                'code'    => $e->getCode(),
                'error'   => 'Host Not Found',
                'message' => 'Could not resolve host: '.$this->api_url,
            ];
        } catch (Exception $e) {
            return $error = [
                'code'    => $e->getCode(),
                'error'   => 'cUrl Error',
                'message' => $e->getMessage(),
            ];
        }

        // If response if Ok. Return collection.
        if ($response->ok()) {
            return $response->collect();
        } else {
            return $this->handleError($response);
        }
    }

    /**
     * Get the milliseconds from the system clock.
     *
     * @return int
     */
    private function milliseconds()
    {
        list($msec, $sec) = explode(' ', microtime());

        return $sec.substr($msec, 2, 3);
    }
}
