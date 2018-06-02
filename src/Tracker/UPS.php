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

class UPS extends BaseTracker
{
    protected $url = "https://wwwapps.ups.com/WebTracking/track";
    protected $source = "United Parcel Service (M) Sdn Bhd (Main)";
    protected $code = "ups";
    protected $method = PARCEL_METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'trackNums' => $refNum,
            'track.x' => 'Track'
        ];
    }

    public function startCrawl($result)
    {
        if (isset($result['body'])) {
            $crawler = new Crawler($result['body']);

            $count = $crawler->filter('.module3 table tr:not(:first-child)')->count();
            $crawlerResult = $crawler->filter('.module3 table tr:not(:first-child)')->each(function (Crawler $node, $i) use ($count) {
                $result = $node->filter('td')->each(function (Crawler $node, $x) use ($i, $count) {
                    return trim_spaces($node->text());
                });

                $data = [];
                $currentDate = null;
                foreach ($result as $key => $item) {
                    if ($key == 0) {
                        $data['event'] = $item;
                    }
                    if ($key == 1) {
                        $currentDate = $item;
                    }
                    if ($key == 2) {
                        try {
                            $dates = Carbon::createFromFormat("d/m/Y H:i", $currentDate . ' ' . $item);
                            $data['date'] = $dates->toDateTimeString();
                            $data['timestamp'] = $dates->timestamp;
                        } catch (\Exception $exception) {
                            $data['date'] = null;
                            $data['timestamp'] = 0;
                        }
                    }

                    if ($key == 3) {
                        $data['process'] = $item;
                        $data['type'] = $this->distinguishProcess($item, $i == ($count - 1));
                    }
                }

                return $data;
            });

            return $this->buildResponse($result, $crawlerResult);
        }

        return $this->buildResponse($result, []);
    }
}