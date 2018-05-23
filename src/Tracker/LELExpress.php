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

class LELExpress extends BaseTracker
{
    protected $url = "http://www.lex.com.my/tracker/";
    protected $source = "Lazada E-Logistic Express";
    protected $code = "lex";

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'trackingNumber' => $refNum,
        ];
    }

    public function startCrawl($result)
    {
        if ($result['body']) {
            $crawler = new Crawler($result['body']);

            $finalResult = [];
            $crawler->filter('.trace__table tr')->each(function (Crawler $node, $i) use (&$finalResult) {
                $result = $node->filter('td')->each(function (Crawler $node, $i) {
                    if (strpos($node->html(), 'trace__item') !== false) {
                        return $node->filter('ul li')->each(function (Crawler $node, $i) {
                            return $node->filter('span')->each(function (Crawler $node, $i) {
                                return trim_spaces($node->text());
                            });
                        });
                    }
                    return trim_spaces($node->text());
                });

                $currentDate = null;
                foreach ($result as $key => $item) {
                    if ($key == 0) {
                        $currentDate = $item;
                    }
                    if ($key == 1) {
                        if (is_array($item)) {
                            foreach ($item as $checkpoint) {
                                $data = [];
                                $parcel = Carbon::createFromFormat("d M H:i", $currentDate . ' ' . $checkpoint[0]);
                                $data['date'] = $parcel->toDateTimeString();
                                $data['timestamp'] = $parcel->timestamp;
                                $data['process'] = $checkpoint[1];
                                $data['type'] = $this->distinguishProcess($checkpoint[1]);
                                $data['event'] = null;
                                $finalResult[] = $data;
                            }
                        }
                    }
                }
            });

            return $this->buildResponse($result, $finalResult);
        }

        return $this->buildResponse($result, []);

    }
}