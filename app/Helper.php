<?php

function curl($url)
{
	return new class($url){
        protected $_curl = [
            'url' => '',
			'data' => [],
			'headers' => [],
            'time_out' => 200,
            'cookie_file' => '',
            'cookie_jar' => '',
            'method' => 'GET',
            'ret' => true,
        ];
        protected $_result, $_errors;
		function __construct($url){
			$this->_curl['url'] = filter_var($url, FILTER_SANITIZE_URL);
        }
		public function setData(array $postdata){
            $this->_curl['data'] = json_encode($postdata);
			return $this;
		}
		public function setHeaders($headers)
		{
			$this->_curl['headers'] = $headers;
			return $this;	
		}
		public function setSession($cookiefile){
            $this->_curl['cookie_file'] = $cookiefile;
			return $this;
        }
		public function saveSession($cookiefile){
            $this->_curl['cookie_jar'] = $cookiefile;
			return $this;
        }
		public function setMethod(string $method){
            $this->_curl['method'] = strtoupper($method);
			return $this;
        }
		public function isReturnable(bool $val = true){
            $this->_curl['ret'] = $val;
			return $this;
        }
		public function setTimeOut($time = 200){
            $this->_curl['time_out'] = $time;
			return $this;
        }
		public function send(){
			array_push($this->_curl['headers'], 'Content-Type: application/json');
            $ch = curl_init($this->_curl['url']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->_curl['method']) );
            if ( !empty($this->_curl['data']) ) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_curl['data']);
            }
            if (!empty($this->_curl['cookie_jar']) && !empty($this->_curl['cookie_file']) ) {
                curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_curl['cookie_jar'] );
                curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_curl['cookie_file'] );
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->_curl['ret']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_curl['time_out'] );
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl['time_out'] );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_curl['headers']);
			$this->_result = curl_exec($ch);
            $this->_errors = curl_error($ch);
			curl_close($ch);
			if ( $this->_errors ) {
				return false;
			} else {
				return $this->_result;
			}
        }
		public function result()
		{
			return $this->_result;
        }
		public function errors()
		{
			return $this->_errors;
        }
	};
}