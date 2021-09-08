<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {

    /**
     * Consulta de voos via api
     */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://prova.123milhas.net/api/flights");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY  , false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $vFlight = json_decode(curl_exec($ch));
    $vReturn = [];
    $vGroup = [];

    /**
     * Criação de vetor com informações de todos os voos disponibilizados
     */
    foreach ($vFlight as $Flight){

        if ($Flight->outbound) {
            $vGroup[$Flight->fare]["outbound"]["id"][] = $Flight->id;
            $vGroup[$Flight->fare]["outbound"]["cia"][] = $Flight->cia;
            $vGroup[$Flight->fare]["outbound"]["flightNumber"][] = $Flight->flightNumber;
            $vGroup[$Flight->fare]["outbound"]["origin"][] = $Flight->origin;
            $vGroup[$Flight->fare]["outbound"]["destination"][] = $Flight->destination;
            $vGroup[$Flight->fare]["outbound"]["departureDate"][] = $Flight->departureDate;
            $vGroup[$Flight->fare]["outbound"]["arrivalDate"][] = $Flight->arrivalDate;
            $vGroup[$Flight->fare]["outbound"]["departureTime"][] = $Flight->departureTime;
            $vGroup[$Flight->fare]["outbound"]["arrivalTime"][] = $Flight->arrivalTime;
            $vGroup[$Flight->fare]["outbound"]["classService"][] = $Flight->classService;
            $vGroup[$Flight->fare]["outbound"]["price"][] = $Flight->price;
            $vGroup[$Flight->fare]["outbound"]["tax"][] = $Flight->tax;
            $vGroup[$Flight->fare]["outbound"]["duration"][] = $Flight->duration;
        }
        if ($Flight->inbound) {
            $vGroup[$Flight->fare]["inbound"]["id"][] = $Flight->id;
            $vGroup[$Flight->fare]["inbound"]["cia"][] = $Flight->cia;
            $vGroup[$Flight->fare]["inbound"]["flightNumber"][] = $Flight->flightNumber;
            $vGroup[$Flight->fare]["inbound"]["origin"][] = $Flight->origin;
            $vGroup[$Flight->fare]["inbound"]["destination"][] = $Flight->destination;
            $vGroup[$Flight->fare]["inbound"]["departureDate"][] = $Flight->departureDate;
            $vGroup[$Flight->fare]["inbound"]["arrivalDate"][] = $Flight->arrivalDate;
            $vGroup[$Flight->fare]["inbound"]["departureTime"][] = $Flight->departureTime;
            $vGroup[$Flight->fare]["inbound"]["arrivalTime"][] = $Flight->arrivalTime;
            $vGroup[$Flight->fare]["inbound"]["classService"][] = $Flight->classService;
            $vGroup[$Flight->fare]["inbound"]["price"][] = $Flight->price;
            $vGroup[$Flight->fare]["inbound"]["tax"][] = $Flight->tax;
            $vGroup[$Flight->fare]["inbound"]["duration"][] = $Flight->duration;
        }

    }

    $vFare = array_keys($vGroup);
    $vPrice = [];

    /**
     * Separação dos voos por taxa
     */
    foreach ($vFare as $Fare){
        foreach ($vGroup[$Fare]["outbound"]["price"] as $nKey=>$nPrice) {
            $vPrice[$Fare]["outbound"][$nPrice][] = $nKey;
        }
        foreach ($vGroup[$Fare]["inbound"]["price"] as $nKey=>$nPrice) {
            $vPrice[$Fare]["inbound"][$nPrice][] = $nKey;
        }
    }

    /**
     * Criação dos grupos de voos
     */
    $nCodGroup = 1;
    $vConsolidated = [];
    foreach ($vPrice as $sFare=>$vFare){
        foreach ($vFare["outbound"] as $nPriceOut=>$vOutbound) {
            foreach ($vFare["inbound"] as $nPriceIn=>$vInbound) {
                $vConsolidated[] = ["id"=>$nCodGroup,"price"=>$nPriceOut+$nPriceIn];

                $vReturn["groups"][$nCodGroup]["uniqueId"] = $nCodGroup;
                $vReturn["groups"][$nCodGroup]["totalPrice"] = $nPriceOut+$nPriceIn;

                foreach ($vOutbound as $nOutbound) {
                    $vReturn["groups"][$nCodGroup]["outbound"][] = [
                        "id" => $vGroup[$sFare]["outbound"]["id"][$nOutbound],
                        "cia" => $vGroup[$sFare]["outbound"]["cia"][$nOutbound],
                        "flightNumber" => $vGroup[$sFare]["outbound"]["flightNumber"][$nOutbound],
                        "origin" => $vGroup[$sFare]["outbound"]["origin"][$nOutbound],
                        "destination" => $vGroup[$sFare]["outbound"]["destination"][$nOutbound],
                        "departureDate" => $vGroup[$sFare]["outbound"]["departureDate"][$nOutbound],
                        "arrivalDate" => $vGroup[$sFare]["outbound"]["arrivalDate"][$nOutbound],
                        "departureTime" => $vGroup[$sFare]["outbound"]["departureTime"][$nOutbound],
                        "arrivalTime" => $vGroup[$sFare]["outbound"]["arrivalTime"][$nOutbound],
                        "price" => $vGroup[$sFare]["outbound"]["price"][$nOutbound],
                        "tax" => $vGroup[$sFare]["outbound"]["tax"][$nOutbound],
                        "duration" => $vGroup[$sFare]["outbound"]["duration"][$nOutbound]
                    ];
                }

                foreach ($vInbound as $Inbound) {
                    $vReturn["groups"][$nCodGroup]["inbound"][] = [
                        "id" => $vGroup[$sFare]["inbound"]["id"][$Inbound],
                        "cia" => $vGroup[$sFare]["inbound"]["cia"][$Inbound],
                        "flightNumber" => $vGroup[$sFare]["inbound"]["flightNumber"][$Inbound],
                        "origin" => $vGroup[$sFare]["inbound"]["origin"][$Inbound],
                        "destination" => $vGroup[$sFare]["inbound"]["destination"][$Inbound],
                        "departureDate" => $vGroup[$sFare]["inbound"]["departureDate"][$Inbound],
                        "arrivalDate" => $vGroup[$sFare]["inbound"]["arrivalDate"][$Inbound],
                        "departureTime" => $vGroup[$sFare]["inbound"]["departureTime"][$Inbound],
                        "arrivalTime" => $vGroup[$sFare]["inbound"]["arrivalTime"][$Inbound],
                        "price" => $vGroup[$sFare]["inbound"]["price"][$Inbound],
                        "tax" => $vGroup[$sFare]["inbound"]["tax"][$Inbound],
                        "duration" => $vGroup[$sFare]["inbound"]["duration"][$Inbound]
                    ];
                }

                $nCodGroup++;
            }
        }
    }

    $nMinValue = min(array_column($vConsolidated, 'price'));
    $nMinId = array_search($nMinValue, array_column($vConsolidated, 'price'));

    $vReturn["totalGroups"] = $nCodGroup-1;
    $vReturn["totalFlights"] = count($vFlight);
    $vReturn["cheapPrice"] = $nMinValue;
    $vReturn["cheapestGroup"] = $vConsolidated[$nMinId]["id"];

    /**
     * Resultado da criação dos grupos
     */
    echo json_encode($vReturn);
});
