<?php 
namespace larvelfaucet\FaucetPay;

class ApiResponse {
    public $status;
    public $message;
    public $data;
    public $url;
    public $args;

    public function __construct($data, $url='', $args){
        $this->url = $url;
        $this->args = $args;
        $this->data = new \stdClass();
        $this->status = (int)$data->status;
        $this->message = $data->message;
        foreach($data as $key => $value){
            if(!in_array($key, ['status', 'message'])){
                $this->data->$key = $value;
            }
        }
    }
}