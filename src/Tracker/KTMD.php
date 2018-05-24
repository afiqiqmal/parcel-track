<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 11:02 PM
 */

namespace afiqiqmal\ParcelTrack\Tracker;

use Carbon\Carbon;

class KTMD extends BaseTracker
{
    protected $url = "http://parcel.ktmd.com.my/ops/ws/track_consignment.php";
    protected $source = "KTM Distribution Sdn Bhd";
    protected $code = "ktmd";
    protected $method = PARCEL_METHOD_POST;

    public function setTrackingNumber($refNum)
    {
        parent::setTrackingNumber($refNum);
        return [
            'consignments' => $refNum,
        ];
    }

    public function getHeader()
    {
        return [
            'Accept' => 'application/json'
        ];
    }

    public function rawOutput()
    {
        return false;
    }

    public function startCrawl($result)
    {
        $finalOutput = [];
        if (isset($result['body'])) {
            $output = $result['body'];
            if ($output[0]['date'] == '-') {
                return $this->buildResponse($result, []);
            }
            $output = array_reverse($output);
            foreach ($output as $key => $item) {
                $data = [];
                $date = trim($item['date']);
                $parcel = Carbon::createFromFormat("Y-m-d", $date);

                $data['date'] = $parcel->toDateTimeString();
                $data['timestamp'] = $parcel->timestamp;
                $data['process'] = trim_spaces($item['description']);
                $data['type'] = $this->distinguishProcess(trim_spaces($item['description']), $item == reset($output));
                $data['event'] = isset($item['location']) ? trim_spaces($item['location']) : null;

                $finalOutput[] = $data;
            }

            return $this->buildResponse($result, $finalOutput, 200, false);
        }

        return $this->buildResponse($result, []);
    }
}