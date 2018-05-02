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
    protected $refNum = [];

    public function setTrackingNumber($refNum)
    {
        $this->refNum = $refNum;
        return $this;
    }

    public function fetch()
    {
        if ($this->source) {
            $request = $this->source->setTrackingNumber($this->refNum);
            $result = $this->execute($request);
            return $this->source->startCrawl($result);
        }

        return die_response("Source Must Be set first ex: ->postLaju()");
    }
}
