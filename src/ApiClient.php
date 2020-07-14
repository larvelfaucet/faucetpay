<?php 
namespace larvelfaucet\FaucetPay;
use larvelfaucet\FaucetPay\ApiResponse;
use GuzzleHttp\Client;

class ApiClient{

    private const _version = 1;
    private const _baseUrl = 'https://faucetpay.io/api/';
    private $apiKey = '';
    private $timeout = 30;

    private $availableMethods = [
        'getBalance' => 'balance',
        'getAvailableCurrencies' => 'currencies',
        'checkAddress' => 'checkaddress',
        'send' => 'send',
        'getRecentPayouts' => 'payouts',
        'faucets' => 'faucetlist',
        'faucetlist' => 'faucetlist',
    ];

    public function getTimeout(){
        return $this->timeout;
    }

    public function __construct(String $apiKey, int $timeout = 30) {
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
    }

    public function getAvailableMethods(): Array {
        return array_keys($this->availableMethods);
    }

    protected function getUrl(String $method): String {
        return self::_baseUrl . '/'. ($this->availableMethods[$method] == 'faucetlist' ? 'list' : '') .'v' . self::_version . '/' . $this->availableMethods[$method];
    }

    protected function _call($method, $params = []): ApiResponse{
        $client = new Client(['timeout' => $this->getTimeout()]);
        $args = $params;
        $args['api_key'] = $this->apiKey;
        $response = $client->request('POST', $this->getUrl($method),[
            'form_params' => $args
        ])->getBody();
        return new ApiResponse(json_decode($response, false), $this->getUrl($method), $args);
    }
    
    public function getBalance(String $currency = 'BTC'): ApiResponse {
        $params = [
            'currency' => $currency
        ];
        return $this->_call(__FUNCTION__, $params);
    }

    public function getAvailableCurrencies(): ApiResponse{
        return $this->_call(__FUNCTION__);
    }

    public function checkAddress(String $address, String $currency = 'BTC'): ApiResponse {
        return $this->_call(__FUNCTION__,[
            'currency' => $currency,
            'address' => $address
        ]);
    }

    public function send(String $address, int $amount, String $currency = 'BTC', bool $isReferral = false, string $ipAddress = ''){
        $data = [
            'to' => $address,
            'currency' => $currency,
            'amount' => $amount,
            'referral' => $isReferral,
        ];
        if(!empty($ipAddress) && filter_var($ipAddress, FILTER_VALIDATE_IP) !== FALSE){
            $data['ip_address'] = $ipAddress;
        }
        return $this->_call(__FUNCTION__, $data);
    }

    public function getRecentPayouts(int $count = 100, String $currency = 'BTC'): ApiResponse {
        return $this->_call(__FUNCTION__, [
            'count' => $count,
            'currency' => $currency
        ]);
    }

    public function faucets(): ApiResponse {
        return $this->_call(__FUNCTION__);
    }

    public function faucetlist(): ApiResponse{
        return $this->_call(__FUNCTION__);
    }
}