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

class Gdex extends BaseTracker
{
    protected $url = "https://esvr3.gdexpress.com/SOTS_Integrated/api/services/app/eTracker/GetListByCnNumber";
    protected $source = "GD Express Sdn Bhd";
    protected $code = "gdex";
    protected $method = PARCEL_METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'parameter' => [
                'input' => $refNum
            ],
        ];
    }

    public function rawOutput()
    {
        return false;
    }

    public function startCrawl($result)
    {
        if (isset($result['body'])) {
            $main = [];
            if ($result['body']['success']) {
                if (count($result['body']['result']) > 0) {
                    $route = $result['body']['result'][0];
                    if ($route['listPodData']) {
                        foreach ($route['listPodData'] as $item) {
                            $date = explode(' ', $item['dtScan']);
                            $date = Carbon::parse($date[0] . " " . $date[1]);
                            $data = [];
                            $data['date'] = $date->toDateTimeString();
                            $data['timestamp'] = $date->timestamp;
                            switch ($item['type']) {
                                case 'pod':
                                case 'm_pod':
                                    $data['process'] = 'Delivered';
                                    break;
                                case 'i_pod':
                                    $data['process'] = 'Under Claim';
                                    break;
                                case 'undl':
                                case 'm_undl':
                                    $data['process'] = "Undelivered due to " . $item['problem_code'];
                                    break;
                                case 'rts':
                                case 'm_rts':
                                    $data['process'] = "Returned to shipper";
                                    break;
                                case 'I':
                                    $data['process'] = "Picked up by courier";
                                    break;
                                case 'Warehouse':
                                    $data['process'] = "Outbound to HUB";
                                    break;
                                case 'M':
                                    $data['process'] = "Outbound from " . $item['origin'] . " station";
                                    break;
                                case 'H':
                                    $data['process'] = "In transit";
                                    break;
                                case 'R':
                                    $data['process'] = "Inbound to " . $item['origin'] . " station";
                                    break;
                                case 'P':
                                    $data['process'] = "In Packing";
                                    break;
                                case 'D':
                                    $data['process'] = "Out for delivery";
                                    break;
                                default:
                                    $data['process'] = null;
                                    break;
                            }

                            $data['type'] = $this->distinguishProcess($item['type']);

                            switch ($item['origin']) {
                                case "HUB":
                                    $data['event'] = 'Petaling Jaya';
                                    break;
                                case "HBN":
                                    $data['event'] = 'Butterworth';
                                    break;
                                case "Warehouse":
                                    $data['event'] = 'Warehouse';
                                    break;
                                default:
                                    $data['event'] = ucwords($item['origin_defi']);
                                    break;
                            }

                            $main[] = $data;
                        }
                        return $this->buildResponse($result, $main);
                    }
                }
            }
        }

        return $this->buildResponse($result, []);
    }
}