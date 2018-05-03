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

class CityLink extends BaseTracker
{
    protected $url = "http://www.citylinkexpress.com/MY/ShipmentTrack.aspx";
    protected $source = "City Link Express";
    protected $code = "citylink";
    protected $method = METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'type' => 'consignment',
            'no' => $refNum,
        ];
    }

    public function startCrawl($result)
    {
//        $crawler = new Crawler($result['body']);
//        $crawlerResult = $crawler->filter('#tr4'.$this->getTrackingNumber())
//            ->filter('table tr')->each(function (Crawler $node, $i) {
//            $result = $node->filter('td')->each(function (Crawler $node, $i) {
//                return trim_spaces($node->text());
//            });
//            $data = [];
//            foreach ($result as $key => $item) {
//                if ($key == 0) {
//                    $data['date'] = $item;
//                }
//                if ($key == 1) {
//                    $data['process'] = $item;
//                    $data['type'] = $this->distinguishProcess($item);
//                }
//
//                if ($key == 2) {
//                    $parcel = Carbon::createFromFormat("d M Y h:i a", $data['date']." ".$item);
//                    $data['date'] = $parcel->toDateTimeString();
//                    $data['timestamp'] = $parcel->timestamp;
//                }
//
//                if ($key == 3) {
//                    $data['event'] = $item;
//                }
//            }
//
//            return $data;
//        });
        echo $result['body'];
        die();

//        return $this->buildResponse($result, $crawlerResult);
    }
}