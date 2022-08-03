<?php

namespace FKosmala\PHPHeTools;

use FKosmala\PHPHeTools\HeLayer;

/**
* HeApi contains every PHP functions to receive HiveEngine data.
**/
class HeApi
{
    /**
    * Needed layer for Hive Engine communication
    */
    private $HeLayer;

    /**
    * Constructor to apply the config array
    *
    * @param array $heConfig Configuration Array
    *
    * @return void
    */
    public function __construct($heConfig = null)
    {
        $this->HeLayer = new HeLayer($heConfig);
    }

    /**
    * Get the status of the selected heNode
    *
    * @return string $result JSON format : State of selected node
    **/
    public function getStatus()
    {
        $params = array();
        $result = $this->HeLayer->call('getStatus', $params, '/blockchain');
        return $result;
    }

    /**
    * Get the Latest block data : timestamp, txs, hash, ...
    *
    * @return string $result JSON format : Last block data
    **/
    public function getLatestBlockInfo()
    {
        $params = array();
        $result = $this->HeLayer->call('getLatestBlockInfo', $params, '/blockchain');
        return $result;
    }

    /**
    * Get selected block data : timestamp, txs, hash, ...
    *
    * @param string $block selected block ID
    *
    * @return string $result JSON format : selected block data
    **/
    public function getBlockInfo($block)
    {
        $params = array(
            "blockNumber" => $block
        );
        $result = $this->HeLayer->call('getBlockInfo', $params, '/blockchain');
        return $result;
    }

    /**
    * Get selected tx data : timestamp, hash, amount, ...
    *
    * @param string $txid selected block ID
    *
    * @return string $result JSON format: selected tx data
    **/
    public function getTransactionInfo($txid)
    {
        $params = array(
            "txid" => $txid
        );
        $result = $this->HeLayer->call('getTransactionInfo', $params, '/blockchain');
        return $result;
    }

    /**
    * Get all delegations from selected account. Optional : only one token
    *
    * @param string $account Selected HIVE account
    * @param string $token Optional : filter with only selected token
    *
    * @return string $result JSON format response
    **/
    public function getDelegationFrom($account, $token = null)
    {
        if ($token != null) {
            $token = strtoupper($token);
        }

        $params = array(
            "contract" => "tokens",
            "table" => "delegations",
            "query" => array(
                "from" => $account,
                "symbol" => $token
            ),
            "limit" => 1
        );
        $result = $this->HeLayer->call('find', $params);
        return $result;
    }

    /**
    * Get all delegations to selected account. Optional : only one token
    *
    * @param string $account Selected HIVE account
    * @param string $token Optional : filter with only selected token
    *
    * @return string $result JSON format response
    **/
    public function getDelegationTo($account, $token = null)
    {
        if ($token != null) {
            $token = strtoupper($token);
        }

        $params = array(
            "contract" => "tokens",
            "table" => "delegations",
            "query" => array(
                "to" => $account,
                "symbol" => $token
            ),
            "limit" => 1
        );

        $result = $this->HeLayer->call('find', $params);
        return $result;
    }

    /**
    * Get pending undelegations from selected account. Optional : only one token
    *
    * @param string $account Selected HIVE account
    * @param string $token Optional : filter with only selected token
    *
    * @return string $result JSON format response
    **/
    public function getPendingUndelegations($account, $token = null)
    {
        if ($token != null) {
            $params = array(
                "contract" => "tokens",
                "table" => "pendingUndelegations",
                "query" => array(
                    "account" => $account,
                    "symbol" => strtoupper($token)
                ),
                "limit" => 1
            );
        } else {
            $params = array(
                "contract" => "tokens",
                "table" => "pendingUndelegations",
                "query" => array(
                    "account" => $account
                ),
                "limit" => 1
            );
        }

        $result = $this->HeLayer->call('find', $params);
        return $result;
    }

    /**
    * Get selected account Hive Engine tokens balance
    *
    * @param string $account Selected HIVE account
    *
    * @return string $result JSON format response
    **/
    public function getAccountBalance($account)
    {
        $params = array(
            "contract" => "tokens",
            "table" => "balances",
            "query" => array(
                "account" => $account
            ),
            "limit" => 1
        );
        $result = $this->HeLayer->call('find', $params);
        return $result;
    }

    /**
    * Get selected account Hive Engine history
    *
    * @param string $account Selected HIVE account
    *
    * @return string $result JSON format response
    **/
    public function getAccountHistory($account, $token = null, $limit = 100)
    {
        if ($token != null) {
            $token = strtoupper($token);
        }

        $result = $this->HeLayer->callHistory($account, $token, $limit);
        return $result;
    }
}
