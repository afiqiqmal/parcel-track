<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:23 PM
 */

namespace afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class BaseTracker
{
    protected $url = null;
    protected $code = null;
    protected $source = "Parcel Tracker";
    protected $tracking_number = null;

    public function getUrl()
    {
        return $this->url;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getSourceName() 
    {
        return $this->source;
    }

    public function getTrackingNumber()
    {
        return $this->tracking_number;
    }

    public function setTrackingNumber($refNum)
    {
        $this->tracking_number = $refNum;
    }

    protected function buildResponse($result, $data, $reverse = true)
    {
        $tracker['tracking_number'] = $this->getTrackingNumber();
        $tracker['provider'] = $this->getCode();
        $tracker['delivered'] = (array_filter($data, function($item) {
            return isset($item['type']) && $item['type'] == 'delivered';
        })) != null;
        $tracker['checkpoints'] = $reverse ? array_reverse($data) : $data;

        return [
            'code' => $result['status_code'],
            'error' => false,
            'tracker' => $tracker,
            'generated_at' => Carbon::now()->toDateTimeString(),
            'footer' => $result['footer']
        ];
    }

    protected function distinguishProcess($process)
    {
        $process = strtolower($process);
        if (preg_match('(counter|outbound|transhipment)', $process)) {
            return "item_received";
        }

        if (preg_match('(dispatch|picked up)', $process)) {
            return "dispatch";
        }

        if (preg_match('(sort)', $process)) {
            return "sorting";
        }

        if (preg_match('(facility|transit|inbound)', $process)) {
            return "facility_process";
        }

        if (preg_match('(delivery)', $process)) {
            return "out_for_delivery";
        }

        if (preg_match('(delivered)', $process)) {
            return "delivered";
        }

        return null;
    }
}