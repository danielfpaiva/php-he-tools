<?php

namespace FKosmala\PHPHeTools;

/**
* HeLayer class contains every functions for HiveEngine node communication.
* php-curl must be needed.
*/
class HeLayer
{
    /**
    * If true, display the query & the result. Default: false
    * @var bool $debug
    */
    private $debug = false;

    /**
    * Default node for query HiveEngine API
    * @var string $heNode
    */
    private $heNode = "api.hive-engine.com/rpc";

    /**
    * Throwing exception when error
    * @var bool $throw_exception
    */
    private $throw_exception = false;

    /**
    * Use HTTPS scheme by default
    * @var string $scheme
    */
    private $scheme = 'https://';

    /**
    * Constructor to use Hive Engine Api with config
    *
    * @param array $heConfig Configuration array
    * @return void
    */
    public function __construct($heConfig = array())
    {

        if (array_key_exists('debug', $heConfig)) {
            $this->debug = $heConfig['debug'];
        }

        if (array_key_exists('heNode', $heConfig)) {
            $this->heNode = $heConfig['heNode'];
        }

        if (array_key_exists('throw_exception', $heConfig)) {
            $this->throw_exception = $heConfig['throw_exception'];
        }
    }

    /**
    * Create the JSON object for normal request
    *
    * @param string $method Method to get data : find / find_one / get_contract, ...
    * @param string $contract Name of the selected contract
    * @param string $table Name of the table
    * @param string $query Executed query
    * @param integer $limit Maximum number of results
    * @return string $request_json JSON object ready to be send by curl() function
    */
    public function getRequest($method, $contract, $table, $query, $limit = 1)
    {
        $request = array(
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => $method,
            "params" => array(
                "contract" => $contract,
                "table" => $table,
                "query" => $query,
            ),
            "limit" => $limit
        );

        $request_json = json_encode($request);

        if ($this->debug) {
            echo "<pre>request_json<br/>" . $request_json . "\n</pre>";
        }

        return $request_json;
    }

    /**
    * Create the JSON object for RPC request
    *
    * @param string $method Used method to get data from HiveEngine
    * @param array $params Array of parameters
    *
    * @return string $request_json JSON object ready to use with curl() function
    */
    public function getRPCRequest($method, $params = array())
    {
        $request = array(
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => $method,
            "params" => $params
        );

        $request_json = json_encode($request);

        if ($this->debug) {
            echo "<pre>request_json<br/>" . $request_json . "\n</pre>";
        }

        return $request_json;
    }

    /**
    * Execute the cURL query and return the response JSON object
    *
    * @param string $data JSON object send to Hive Engine selected node
    * @param string $endpoint (optional) Selected endpoint to execute the query
    *
    * @return string $result result JSON object
    **/
    public function curl($data, $endpoint)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->scheme . $this->heNode . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);

        if ($this->debug) {
            echo "<pre><br>Result :<br>" . $result . "</pre>\n";
        }

        return $result;
    }

    /**
    * Function to generate JSON object for (RPC) request, execute the query with curl(), and return the results
    *
    * @param string $method Method
    * @param array $params Array of parameters
    * @param string $endpoint Endpoint where the query will be executed
    *
    * @return string $response JSON object from HiveEngine response
    */
    public function call($method, $params = array(), $endpoint = "/contracts")
    {
        if (!empty($params['contract'])) {
            $contract = $params['contract'];
            $table = $params['table'];
            $query = $params['query'];
            $limit = $params['limit'];
            $request = $this->getRequest($method, $contract, $table, $query, $limit);
        } else {
            $request = $this->getRPCRequest($method, $params);
        }
        $response = $this->curl($request, $endpoint);
        $response = json_decode($response, true);

        if (empty($response['result'])) {
            if ($this->throw_exception) {
                throw new Exception('Error retrieve HiveEngine API query');
            } else {
                return $response['result'];
            }
        }

        return $response['result'];
    }

    /**
    * Function to get Hive Engine history for an account or a token
    *
    * @param string $account Hive account
    * @param string $token Hive Engine token name
    * @param integer $limit MAximum number of result
    *
    * @return string $response JSON object of HiveEngine response
    */
    public function callHistory($account = null, $token = null, $limit = 100)
    {
        $heHistory = "https://accounts.hive-engine.com/accountHistory";
        $historyUrl = $heHistory . "?account=" . $account . "&symbol=" . $token . "&limit=" . $limit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $historyUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if ($this->debug) {
            echo "<pre><br>Result :<br>" . $result . "</pre>\n";
        }

        $response = json_decode($result, true);

        if (empty($response['result'])) {
            if ($this->throw_exception) {
                throw new Exception('Error retrieve HiveEngine API query');
            } else {
                return $response;
            }
        }

        return $response;
    }
}
