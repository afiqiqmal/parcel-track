<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace Afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class DHL extends BaseTracker
{
    protected $url = "https://www.logistics.dhl/shipmentTracking";
    protected $source = "DHL WorldWide Express";
    protected $code = "dhl_ww_express";

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'AWB' => $refNum,
            'countryCode' => 'MY',
            'languageCode' => 'en',
        ];
    }

    public function getHeader()
    {
        return [
            'User-Agent' => PARCEL_USER_AGENT,
            'Accept' => 'application/json'
        ];
    }

    public function rawOutput()
    {
        return false;
    }

    public function startCrawl($result)
    {
        //take the first
        if (isset($result['body']['errors'][0])) {
            return $this->buildResponse($result, []);
        } else {
            $finalOutput = [];
            //take the first
            $output = $result['body']['results'][0]['checkpoints'];
            $output = array_reverse($output);
            foreach ($output as $key => $item) {
                $data = [];
                $date = trim($item['date']);
                $time = trim($item['time']);
                $parcel = Carbon::createFromFormat("l, F d, Y H:i", $date." ".$time);

                $data['date'] = $parcel->toDateTimeString();
                $data['timestamp'] = $parcel->timestamp;
                $data['process'] = trim_spaces($item['description']);
                $data['type'] = $this->distinguishProcess(trim_spaces($item['description']), $item == reset($output));
                $data['event'] = trim_spaces($item['location']);

                $finalOutput[] = $data;
            }

            return $this->buildResponse($result, $finalOutput, 200, false);
        }
    }
}