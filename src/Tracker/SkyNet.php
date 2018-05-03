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

class SkyNet extends BaseTracker
{
    protected $url = "http://track.skynetexpressict.com/";
    protected $source = "Skynet Express";
    protected $code = "skynet";
    protected $method = METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'hawbNoList' => 238216506684,
            'x' => 34,
            'y' => 6,
        ];
    }

    public function startCrawl($result)
    {
        $crawler = new Crawler($result['body']);
        $crawlerResult = $crawler->filter('#tr4'.$this->getTrackingNumber())
            ->filter('table tr')->each(function (Crawler $node, $i) {
            $result = $node->filter('td')->each(function (Crawler $node, $i) {
                return trim_spaces($node->text());
            });
            $data = [];
            foreach ($result as $key => $item) {
                if ($key == 0) {
                    $data['date'] = $item;
                }
                if ($key == 1) {
                    $data['process'] = $item;
                    $data['type'] = $this->distinguishProcess($item);
                }

                if ($key == 2) {
                    $parcel = Carbon::createFromFormat("d M Y h:i a", $data['date']." ".$item);
                    $data['date'] = $parcel->toDateTimeString();
                    $data['timestamp'] = $parcel->timestamp;
                }

                if ($key == 3) {
                    $data['event'] = $item;
                }
            }

            return $data;
        });

        return $this->buildResponse($result, $crawlerResult);
    }
}