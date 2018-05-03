<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 10:57 PM
 */

namespace afiqiqmal\ParcelTrack\Contract;

use afiqiqmal\ParcelTrack\Tracker\Abx;
use afiqiqmal\ParcelTrack\Tracker\DHL;
use afiqiqmal\ParcelTrack\Tracker\Gdex;
use afiqiqmal\ParcelTrack\Tracker\PosLaju;
use afiqiqmal\ParcelTrack\Tracker\SkyNet;

class BaseParcelTrack
{
    protected $source = null;

    public function postLaju()
    {
        $this->source = new PosLaju();
        return $this;
    }

    public function gdExpress()
    {
        $this->source = new Gdex();
        return $this;
    }

    public function abxExpress()
    {
        $this->source = new Abx();
        return $this;
    }

    public function dhlExpress()
    {
        $this->source = new DHL();
        return $this;
    }

    public function skynet()
    {
        $this->source = new SkyNet();
        return $this;
    }

    protected function execute($requestBody)
    {
        $result = api_request()
            ->baseUrl($this->source->getUrl())
            ->setMethod($this->source->getMethodCall())
            ->setHeader($this->source->getHeader())
            ->appendToResult($this->createFooterJson())
            ->setRequestBody($requestBody);

        if ($this->source->rawOutput()) {
            $result = $result->getRaw()->fetch();
        } else {
            $result = $result->fetch();
        }

        if (isset($result['body'])) {
            return $result;
        }

        return null;
    }

    protected function createFooterJson()
    {
        return [
            'footer' => [
                'source' => $this->source->getSourceName(),
                'developer' => [
                    "name" => "Hafiq",
                    "homepage" => "https://github.com/afiqiqmal"
                ]
            ]
        ];
    }
}