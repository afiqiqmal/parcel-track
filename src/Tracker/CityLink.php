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
    protected $method = PARCEL_METHOD_POST;

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
        $crawler = new Crawler($result['body']);
//        $crawlerResult = $crawler->filter('#btmPanel')->html();
        $crawlerResult = $crawler->filter('#btmPanel tr')->each(function (Crawler $node, $i) {
            if (strpos($node->html(), 'tabletitle') !== false) {
                $result = $node->filter('td')->each(function (Crawler $node, $i) {
                    $value = trim_spaces($node->text());
                    if (strlen($value) > 2) {
                        return $value;
                    }
                });

                return $this->removeAllNullInArray($result)[0];
            }

            if (strpos($node->html(), 'table_detail') !== false) {
                $result = $node->filter('table tr')->each(function (Crawler $node, $i) {
                    $result = $node->filter('td')->each(function (Crawler $node, $i) {
                        $value = trim_spaces($node->text());
                        if (strlen($value) > 2) {
                            return $value;
                        }
                    });

                    return $this->removeAllNullInArray($result);
                });
                return $this->removeAllNullInArray($result);
            }
        });

        $crawlerResult = $this->removeAllNullInArray($crawlerResult);
        $currentDate = null;
        $finalResult = [];
        foreach ($crawlerResult as $key => $item) {
            //header
            if ($key % 2 == 0) {
                $currentDate = $item;
            }

            //detail
            if ($key % 2 == 1) {
                if (is_array($item)) {
                    foreach ($item as $checkpoint) {
                        $data = [];
                        $parcel = Carbon::createFromFormat('l, F d, Y H:i a', $currentDate." ".$checkpoint[1]);
                        $data['date'] = $parcel->toDateTimeString();
                        $data['timestamp'] = $parcel->timestamp;
                        $data['process'] = isset($checkpoint[0]) ? $checkpoint[0] : null;
                        $data['type'] = $this->distinguishProcess($data['process']);
                        $data['event'] = isset($checkpoint[2]) ? $checkpoint[2] : null;
                        $finalResult[] = $data;
                    }
                }
            }
        }

        return $this->buildResponse($result, $finalResult);
    }

    private function removeAllNullInArray($array)
    {
        $newData = [];
        foreach ($array as $item) {
            if ($item != null) {
                $newData[] = $item;
            }
        }

        return $newData;
    }
}