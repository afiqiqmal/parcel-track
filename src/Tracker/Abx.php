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

class Abx extends BaseTracker
{
    protected $url = "http://www.abxexpress.com.my/track.asp?vsearch=True";
    protected $source = "ABX Express Sdn Bhd";
    protected $code = "abx";
    protected $method = METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'tairbillno' => $refNum,
        ];
    }

    public function startCrawl($result)
    {
        $crawler = new Crawler($result['body']);

        $crawlerResult = $crawler->filter('.ResultsTableCell3')->eq(1)
            ->filter('tr:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3))')
            ->each(function (Crawler $node, $i) {
                $result =  $node->filter('td')->each(function (Crawler $node, $i) {
                    return trim(preg_replace('/\s+/', ' ', $node->text()));
                });

                $data = [];
                foreach ($result as $key => $item) {
                    if ($key == 1) {
                        $parcel = Carbon::createFromFormat("d/m/Y H:i:s", $item);
                        $data['date'] = $parcel->toDateTimeString();
                        $data['timestamp'] = $parcel->timestamp;
                    }
                    if ($key == 2) {
                        $data['process'] = $item;
                        $data['type'] = $this->distinguishProcess($item);
                    }
                    if ($key == 0) {
                        $data['event'] = $item;
                    }
                }

                return $data;
            });

        return $this->buildResponse($result, $crawlerResult);
    }
}