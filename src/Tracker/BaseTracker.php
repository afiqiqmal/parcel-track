<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:23 PM
 */

namespace afiqiqmal\ParcelTrack\Tracker;

class BaseTracker
{
    protected $url = null;
    protected $source = "Parcel Tracker";

    public function getUrl()
    {
        return $this->url;
    }

    public function getSourceName() 
    {
        return $this->source;
    }

    protected function distinguishProcess($process)
    {
        $process = strtolower($process);
        if (preg_match('(counter|outbound)', $process)) {
            return "item_received";
        }

        if (preg_match('(dispatch|picked up)', $process)) {
            return "dispatch";
        }

        if (preg_match('(facility|transit|inbound)', $process)) {
            return "arrived_facility";
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