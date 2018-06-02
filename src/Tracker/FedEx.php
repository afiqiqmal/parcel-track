<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace Afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class FedEx extends BaseTracker
{
    protected $url = "https://www.fedex.com/trackingCal/track";
    protected $source = "FedEx Express";
    protected $code = "fedex";
    protected $method = PARCEL_METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'data' => json_encode([
                "TrackPackagesRequest" => [
                    "trackingInfoList" => [
                        [
                            "trackNumberInfo" => [
                                "trackingNumber" => $refNum
                            ]
                        ]
                    ]
                ]
            ]),
            'action' => 'trackpackages',
            'format' => 'json',
            'version' => 1,
            'locale' => 'en_MY'
        ];
    }

    public function rawOutput()
    {
        return false;
    }

    public function startCrawl($result)
    {
        //take the first
        $finalOutput = [];
        //take the first
        if (isset($result['body']['TrackPackagesResponse']['packageList'][0]['scanEventList'])) {
            $output = $result['body']['TrackPackagesResponse']['packageList'][0]['scanEventList'];
            $output = array_reverse($output);
            foreach ($output as $key => $item) {
                $data = [];
                //check date if null, then it should be not checkpoints
                if ($item['date'] == "") {
                    break;
                }

                $date = trim($item['date']);
                $time = trim($item['time']);
                $parcel = Carbon::createFromFormat("Y-m-d H:i:s", $date . " " . $time);

                $data['date'] = $parcel->toDateTimeString();
                $data['timestamp'] = $parcel->timestamp;
                $data['process'] = trim_spaces($item['status']);
                $data['type'] = $this->distinguishProcess(trim_spaces($item['status']), $item == reset($output));
                $data['event'] = trim_spaces($item['scanLocation']);

                $finalOutput[] = $data;
            }

            return $this->buildResponse($result, $finalOutput, 200, false);
        } else {
            return $this->buildResponse($result, []);
        }
    }
}