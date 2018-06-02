<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace Afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class DHLCommerce extends BaseTracker
{
    protected $url = "https://www.logistics.dhl/v1/mailitems/track";
    protected $source = "DHL E-Commerce";
    protected $code = "dhl_e_commerce";

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'number' => $refNum,
            'access_token' => 'nBv2wb9Uc6jW7x0EC22onGwvIA/lMR8k2Z6mem/jLNA+bQPTF80+nJiYoEWCdFQUQQW3wv4jQx8WNrGd2JEAEXYSrip77np4F7X2icSxAgorjRdabr7d1jjktOI1Z4487KpkdJes+I4byatWZRX7Uig7v/VTsztthTk8IUrWowttiEBQnw0/NjRe4drp3mFAzlzrOYtroRjZ13eqE6l+nQ==', // temporary
            'client_id' => '32152', //temporary
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
        if (!$result['error']) {
            if (isset($result['body']['data']['mailItems'][0])) {
                $data = $result['body']['data']['mailItems'][0];
                $events = $data['events'];
                $finalOutput = [];

                $output = array_reverse($events);

                foreach ($output as $item) {
                    $data = [];
                    $date = trim($item['date']);
                    $time = trim($item['time']);
                    $parcel = Carbon::createFromFormat("Y-m-d H:i:s", $date." ".$time);

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

        return $this->buildResponse($result, []);
    }
}