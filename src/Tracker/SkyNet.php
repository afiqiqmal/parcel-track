<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace Afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class SkyNet extends BaseTracker
{
    protected $url = "http://track.skynetexpressict.com/";
    protected $source = "Skynet Express";
    protected $code = "skynet";
    protected $method = PARCEL_METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'hawbNoList' => $refNum,
        ];
    }

    public function startCrawl($result)
    {
        if (isset($result['body'])) {
            $crawler = new Crawler($result['body']);
            $crawlerResult = $crawler->filter('#tr2' . $this->getTrackingNumber())
                ->filter('table tr')->each(function (Crawler $node, $i) {
                    $result = $node->filter('td')->each(function (Crawler $node, $i) {
                        return trim_spaces($node->text());
                    });

                    return $this->removeAllNullInArray($result, true);
            });

            $currentDate = null;
            $dataLast = [];
            if (isset($crawlerResult[0][0]) && Carbon::hasFormat($crawlerResult[0][0], 'd M Y')) {
                $currentDate = $crawlerResult[0][0].' '.$crawlerResult[1][1];
                $parcel = Carbon::createFromFormat("d M Y g:i A", $currentDate);
                $dataLast[] = [
                    'date' => $parcel->toDateTimeString(),
                    'timestamp' => $parcel->timestamp,
                    'process' => $crawlerResult[1][0],
                    'type' => $this->distinguishProcess($crawlerResult[1][0]),
                    'event' => ucwords(strtolower($crawlerResult[1][2])),
                ];
            } else {
                return $this->buildResponse($result, $dataLast);
            }

            $crawlerResult = $crawler->filter('#tr4' . $this->getTrackingNumber())
                ->filter('table tr')->each(function (Crawler $node, $i) {
                    $result = $node->filter('td')->each(function (Crawler $node, $i) {
                        return trim_spaces($node->text());
                    });

                    $result = $this->removeAllNullInArray($result, true);
                    if ($result) {
                        if (isset($result[1])) {
                            unset($result[1]);
                            $result = array_values($result);
                        }
                        if (Carbon::hasFormat($result[0], 'd M Y')) {
                            return $result;
                        }

                        if (Carbon::hasFormat($result[0], 'g:i A')) {
                            return $result;
                        }
                    }

                    return null;
                });

            $data = $this->removeAllNullInArray($crawlerResult);
            $currentDate = null;
            foreach ($data as $item) {
                if (isset($item[0]) && Carbon::hasFormat($item[0], 'd M Y')){
                    $currentDate = $item[0];
                }

                if (count($item) > 1) {
                    $parcel = Carbon::createFromFormat("d M Y g:i A", $currentDate . " " . $item[0]);
                    $dataLast[] = [
                        'date' => $parcel->toDateTimeString(),
                        'timestamp' => $parcel->timestamp,
                        'process' => $item[1],
                        'type' => $this->distinguishProcess($item[1]),
                        'event' => ucwords(strtolower($item[2])),
                    ];
                }
            }

            return $this->buildResponse($result, $dataLast);
        }

        return $this->buildResponse($result, []);
    }

    private function removeAllNullInArray($array, $removeDuplicate = false)
    {
        $newData = [];
        $array = array_values(array_filter($array));
        foreach ($array as $item) {
            if ($item != null) {
                $newData[] = $item;
            }
        }

        if ($removeDuplicate) {
            $newData = array_unique($array, SORT_REGULAR);
        }

        return $newData;
    }
}