<?php

namespace Betfair;

class Client
{
    protected $_config;

    protected $_sessionId = false;

    protected $_globalSoapClient;
    protected $_ukExchangeSoapClient;
    protected $_auExchangeSoapClient;

    protected $_globalMethods = [];
    protected $_exchangeMethods = [];

    /**
     * URLs of Betfair's soap exchange WSDL documents
     */
    CONST GLOBALSERVICE   = 'https://api.betfair.com/global/v3/BFGlobalService.wsdl';
    CONST EXCHANGESERVICE = 'https://api.betfair.com/exchange/v5/BFExchangeService.wsdl';
    CONST AUEXCHANGESERVICE = 'https://api-au.betfair.com/exchange/v5/BFExchangeService.wsdl';

    public function __construct($config)
    {
        $this->_config = $config;
        $this->_globalSoapClient = new \SoapClient(self::GLOBALSERVICE, ['trace' => 1, 'exceptions' => 1]);
        $this->_ukExchangeSoapClient = new \SoapClient(self::EXCHANGESERVICE, ['trace' => 1, 'exceptions' => 1]);
        $this->_auExchangeSoapClient = new \SoapClient(self::AUEXCHANGESERVICE, ['trace' => 1, 'exceptions' => 1]);
        $this->getFunctionsFromWSDL();
    }

    public function logon()
    {
        $response = $this->_globalSoapClient->login(['request' =>[
            'username' => $this->_config['username'],
            'password' => $this->_config['password'],
            'productId' => 82,
            'vendorSoftwareId' => 0,
            'locationId' => 0,
            'ipAddress' =>0
        ]]);
        $this->_sessionId = $response->Result->header->sessionToken;
    }

    public function getFunctionsFromWSDL()
    {
        $funclist = $this->_globalSoapClient->__getFunctions();
        foreach ($funclist as $primative) {
            $elements              = explode(' ', $primative);
            $parts                 = explode('(', $elements[1]);
            $this->_globalMethods[] = $parts[0];
        }

        $funclist = $this->_ukExchangeSoapClient->__getFunctions();
        foreach ($funclist as $primative) {
            $elements                = explode(' ', $primative);
            $parts                   = explode('(', $elements[1]);
            $this->exchangeMethods[] = $parts[0];
        }
    }

    public function execute($operation, $parameters = [], $excahngeId = 1)
    {
        if (!$this->_sessionId) {
            $this->logon();
        }
        $client = $this->_ukExchangeSoapClient;
        if (in_array($operation, $this->_globalMethods)) {
            $client = $this->_globalSoapClient;
        } elseif ($excahngeId == 2) {
            $client = $this->_auExchangeSoapClient;
        }
        $requestData = [
            'request' => $parameters
        ];
        $requestData['request']['header'] = array('clientStamp' => 0, 'sessionToken' => $this->_sessionId);
        return $client->$operation($requestData);
    }

    public function getAllMarkets($typeId)
    {
        $params = null;
        if ($typeId) {
            $params = ['eventTypeIds' => [$typeId] ];
        }
        return $this->execute('getAllMarkets', $params);
    }

    public function getMarket($id, $exchangeId)
    {
        return $this->execute('getMarket', [
            'marketId' => $id,
            'currencyCode' => 'GBP',
            'includeCouponLinks' => 0,
        ], $exchangeId);
    }

    public function getMarketPricesCompressed($id, $exchangeId)
    {
        $response = $this->execute('getMarketPricesCompressed', ['marketId' => $id], $exchangeId);
        return $response;
    }

    public function getEvents($parentId)
    {
        $response = $this->execute('getEvents', ['eventParentId' => $parentId]);
        return $response;
    }
}