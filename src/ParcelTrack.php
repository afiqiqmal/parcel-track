<?php
/**
 * Description of ParcelTrack
 *
 * @author hafiz
 */
namespace Afiqiqmal\ParcelTrack;

use afiqiqmal\ParcelTrack\Contract\BaseParcelTrack;

class ParcelTrack extends BaseParcelTrack
{
    protected $trackingCode = [];

    /**
     * set tracking number
     * @param $refNum
     * @return $this
     */
    public function setTrackingNumber($refNum)
    {
        $this->trackingCode = $refNum;
        return $this;
    }

    public function fetch()
    {
        if ($this->source) {
            $request = $this->source->setTrackingNumber($this->trackingCode);
            $result = $this->execute($request);
            return $this->source->startCrawl($result);
        }

        return die_response("Source Must Be set first ex: ->postLaju()");
    }
}
