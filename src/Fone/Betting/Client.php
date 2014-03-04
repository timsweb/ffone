<?php

namespace Fone\Betting;
/**
 *
 */
class Client extends \Betfair\Client
{

    public function getRaces()
    {
        $response = $this->getEvents(27107413); //F1 eventId
        $events =[];
        foreach ($response->Result->eventItems as $event) {
            $events[$event->eventId] = $event->eventName;
        }
        return $events;
    }

    public function findMarketId($eventId, $marketName)
    {
        $response = $this->getEvents($eventId);
        foreach ($response->Result->marketItems->MarketSummary as $market) {
            if ($market->marketName === $marketName) {
                return [$market->marketId, $market->exchangeId];
            }
        }
        return null;
    }

    public function getRunnerPrices($marketId, $exchangeId)
    {
        $response = $this->getMarketPricesCompressed($marketId, $exchangeId);
        $rows = explode(":", $response->Result->marketPrices);
        array_shift($rows);
        $data = [];
        foreach ($rows as $r) {
            $nuggets = explode('|', $r);
            $info = array_shift($nuggets);
            $info = explode('~', $info);
            $prices = [];
            foreach ($nuggets as $n) {
                $prices[] = explode('~', $n);
            }
            $data[$info[0]] = $prices;
        }
        return $data;
    }

    public function getRunners($marketId, $excahngeId)
    {
        $response = $this->getMarket($marketId, $excahngeId);
        $runners = [];
        if (!isset($response->Result->market->runners->Runner)) {
            die('<pre>' . print_r([$marketId, $response], true) . '</pre>');/** @todo DEBUGGING */
        }
        foreach ($response->Result->market->runners->Runner as $r) {
            $runners[$r->selectionId] = $r->name;
        }
        return $runners;
    }

    public function getCurrentDriverOdds()
    {
        $championshipRunner = $this->getRunners(112028598, 1);
        $championshipPrices = $this->_normalisePrices($this->getRunnerPrices(112028598, 1));

        //TODO: get next race Id
        $races = $this->getRaces();
        $nextRaceEventId = null;
        foreach ($races as $id => $race) {
            echo 'next race is ' . $race . PHP_EOL;
            $nextRaceEventId = $id;
            break;
        }
        list ($nextRaceMarketId, $exchangeId) = $this->findMarketId($nextRaceEventId, 'Winner');
        $raceRunners = $this->getRunners($nextRaceMarketId, $exchangeId);
        $racePrices = $this->_normalisePrices($this->getRunnerPrices($nextRaceMarketId, $exchangeId));

        $totalPrices = [];
        foreach ($raceRunners as $id => $price) {
            $total = (isset($championshipPrices[$id]))? $championshipPrices[$id] : 0;
            $total += $price * 3;
            $totalPrices[$id] = $total;
        }
        $return = $this->_matchRunnersToPrices($raceRunners, $totalPrices);
        asort($return);
        return $return;
    }

    public function getCurrentTeamOdds()
    {
        $championshipRunner = $this->getRunners(112028612, 1);
        $championshipPrices = $this->_normalisePrices($this->getRunnerPrices(112028612, 1));

        //TODO: get next race Id
        $races = $this->getRaces();
        $nextRaceEventId = null;
        foreach ($races as $id => $race) {
            echo 'next race is ' . $race . PHP_EOL;
            $nextRaceEventId = $id;
            break;
        }
        list ($nextRaceMarketId, $exchangeId) = $this->findMarketId($nextRaceEventId, 'Winning Car');
        $raceRunners = $this->getRunners($nextRaceMarketId, $exchangeId);
        $racePrices = $this->_normalisePrices($this->getRunnerPrices($nextRaceMarketId, $exchangeId));

        $totalPrices = [];
        foreach ($raceRunners as $id => $price) {
            $total = (isset($championshipPrices[$id]))? $championshipPrices[$id] : 0;
            $total += $price * 3;
            $totalPrices[$id] = $total;
        }
        $return = $this->_matchRunnersToPrices($raceRunners, $totalPrices);
        asort($return);
        return $return;
    }

    protected function _normalisePrices($prices)
    {
        $data = [];
        foreach ($prices as $runnerId => $priceData) {
            $lays = array_chunk($priceData[0], 4);
            $backs = array_chunk($priceData[1], 4);
            $odds = [];
            foreach ($lays as $l) {
                if ($l[0]) {
                    $odds[] = $l[0];
                }
            }
            if (empty($odds)) {
                $odd = 1000;
            } else {
                $odd = array_sum($odds) / count($odds);
            }
            $data[$runnerId] = $odd;
        }
        return $data;
    }

    protected function _matchRunnersToPrices($runners, $prices)
    {
        $matched = [];
        $ids = array_intersect(array_keys($runners), array_keys($prices));
        foreach ($ids as $id) {
            $matched[$runners[$id]] = $prices[$id];
        }
        return $matched;
    }
}