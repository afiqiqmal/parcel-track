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

    public function getSourceName() {
        return $this->source;
    }

    protected function distinguishProcess($process)
    {
        if (stripos($process, 'counter') !== false) {
            return "item_received";
        }

        if (stripos($process, 'dispatch') !== false) {
            return "dispatch";
        }

        if (stripos($process, 'facility') !== false) {
            return "arrived_facility";
        }

        if (stripos($process, 'delivery') !== false) {
            return "out_for_delivery";
        }

        if (stripos($process, 'delivered') !== false) {
            return "delivered";
        }

        return null;
    }
}