<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

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

    public function startCrawl($result)
    {
        $crawler = new Crawler($result['body']);
        $crawlerResult = $crawler->filter('#tbDetails > tbody > tr:not(.danger)')->each(function (Crawler $node, $i) {
            $result = $node->filter('td')->each(function (Crawler $node, $i) {
                return trim(preg_replace('/\s+/', ' ', $node->text()));
            });
            $data = [];
            foreach ($result as $key => $item) {
                if ($key == 0) {
                    $parcel = Carbon::createFromFormat("d M Y, h:i:s a", $item);
                    $data['date'] = $parcel->toDateTimeString();
                    $data['timestamp'] = $parcel->timestamp;
                }
                if ($key == 1) {
                    $data['process'] = $item;
                    $data['type'] = $this->distinguishProcess($item);
                }
                if ($key == 2) {
                    $data['event'] = $item;
                }
            }

            return $data;
        });

        return $this->buildResponse($result, $crawlerResult);
    }
}