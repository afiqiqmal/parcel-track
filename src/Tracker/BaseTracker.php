<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:23 PM
 */

namespace Afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class BaseTracker
{
    protected $url = null;
    protected $code = null;
    protected $source = "Parcel Tracker";
    protected $tracking_number = null;
    protected $method = PARCEL_METHOD_GET;

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

    public function getMethodCall()
    {
        return $this->method;
    }

    public function getHeader()
    {
        return null;
    }

    public function rawOutput()
    {
        return true;
    }

    public function setTrackingNumber($refNum)
    {
        $this->tracking_number = $refNum;
        return null;
    }

    public function startCrawl($result)
    {
        return [];
    }

    protected function buildResponse($result, $data, $status_code = 200, $reverse = true)
    {
        $tracker['tracking_number'] = $this->getTrackingNumber();
        $tracker['provider'] = $this->getCode();
        $tracker['delivered'] = (array_filter($data, function($item) {
            return isset($item['type']) && $item['type'] == 'delivered';
        })) != null;
        $tracker['checkpoints'] = $reverse ? array_reverse($data) : $data;
        return [
            'code' => $status_code,
            'error' => $status_code >= 300 ? true : false,
            'tracker' => $tracker,
            'generated_at' => Carbon::now()->toDateTimeString(),
            'footer' => $result['footer']
        ];
    }

    protected function distinguishProcess($process, $isFirstPosition = false)
    {
        if ($isFirstPosition) {
            return "item_received";
        }

        $process = strtolower($process);

        if (preg_match('(counter|outbound|transhipment|collection|collected|picked up)', $process)) {
            return "item_received";
        }

        if (preg_match('(dispatch|depart)', $process)) {
            return "dispatch";
        }

        if (preg_match('(sort)', $process)) {
            return "sorting";
        }

        if (preg_match('(facility|transit|inbound)', $process)) {
            return "facility_process";
        }

        if (preg_match('(unsuccessful|failed)', $process)) {
            return "delivery_failed";
        }

        if (preg_match('(delivery|with courier)', $process)) {
            return "out_for_delivery";
        }

        if (preg_match('(delivered)', $process)) {
            return "delivered";
        }

        return null;
    }
}