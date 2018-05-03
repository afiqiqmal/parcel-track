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

class PosLaju extends BaseTracker
{
    protected $url = "https://poslaju.com.my/track-trace-v2/";
    protected $source = "Post Laju";
    protected $code = "poslaju";
    protected $method = METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'trackingNo03' => $refNum,
            'hvfromheader03' => 0,
            'hvtrackNoHeader03' => null,
        ];
    }

    public function startCrawl($result)
    {
        $crawler = new Crawler($result['body']);
        $crawlerResult = $crawler->filter('#tbDetails > tbody > tr:not(.danger)')
            ->each(function (Crawler $node, $i) {
            $result = $node->filter('td')->each(function (Crawler $node, $i) {
                return trim_spaces($node->text());
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