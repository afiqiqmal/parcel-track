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

class Gdex extends BaseTracker
{
    protected $url = "http://web2.gdexpress.com/official/iframe/etracking_v2.php";
    protected $source = "GD Express Sdn Bhd";

    public function setTrackingNumber($refNum)
    {
        return [
            'capture' => $refNum,
            'redoc_gdex' => 'cnGdex',
            'Submit' => 'Track',
        ];
    }

    public function startCrawl($result)
    {
        $crawler = new Crawler($result['body']);

        $crawlerResult = $crawler->filter('#products tr:not(:first-child)')->each(
            function (Crawler $node, $i) {
                if (strtolower($node->text()) != 'invalid cn') {
                    $result = $node->filter('td:not(:first-child)')->each(
                        function (Crawler $node, $i) {
                            return trim(preg_replace('/\s+/', ' ', $node->text()));
                        }
                    );

                    $data = [];
                    foreach ($result as $key => $item) {
                        if ($key == 0) {
                            $parcel = Carbon::createFromFormat("d/m/Y H:i:s", $item);
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
                } else {
                    return null;
                }
            }
        );

        //reset if not found. weird dom output
        if ($crawlerResult[0] == null) {
            $crawlerResult = [];
        }

        return [
            'code' => $result['status_code'],
            'error' => false,
            'tracker' => array_reverse($crawlerResult),
            'footer' => $result['footer']
        ];
    }
}