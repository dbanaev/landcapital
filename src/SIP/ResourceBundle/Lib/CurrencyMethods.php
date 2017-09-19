<?php
/*
 * (c) Jack Davyd <lex9612007@rambler.ru>
 */
namespace SIP\ResourceBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class CurrencyMethods {

    public function __construct() {
        
    }
    
    /**
     *Exchange rates at the date
     *@param \DateTime $date 
     *@param array $currencies Currencies list example ['USD','EUR'... etc]
     */
    public function cursOnDateCbr(\DateTime $date = null, array $currencies = []) {
        if (!$date) {
            $date = new \DateTime;
        }
        try {
            $SoapClient = new \SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL', ["connection_timeout" => 5]);
            $lastdate = $SoapClient->GetLatestDateTime()->GetLatestDateTimeResult;
            $array = $SoapClient->GetCursOnDateXML(["On_date" => $date->format('c')])->GetCursOnDateXMLResult->any;
            $xml = simplexml_load_string($array);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $excerpt = [];
        if (!$currencies) {
            $xPath = "/ValuteData/ValuteCursOnDate";
            $excerpt = $xml->xpath($xPath);
        } else {
            foreach ($currencies as $currency) {
                $xPath = "/ValuteData/ValuteCursOnDate[VchCode='" . $currency . "']";
                $excerpt = array_merge($excerpt, $xml->xpath($xPath));
            }
        }

        foreach ($excerpt as $ex) {
            $ex->RealCurs = (float) $ex->Vcurs / (float) $ex->Vnom;
            $ex->RealLastDate = $lastdate;
        }

        return $excerpt;
    }

}
