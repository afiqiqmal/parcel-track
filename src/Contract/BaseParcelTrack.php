<?php
/**
 * Created by PhpStorm.
 * User: hafiq
 * Date: 01/05/2018
 * Time: 10:57 PM
 */

namespace afiqiqmal\ParcelTrack\Contract;

use afiqiqmal\ParcelTrack\Tracker\Gdex;
use afiqiqmal\ParcelTrack\Tracker\PosLaju;

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

    protected function execute($requestBody)
    {
        $result = api_request()->baseUrl($this->source->getUrl())
            ->postMethod()
            ->appendToResult($this->createFooterJson())
            ->setRequestBody($requestBody)
            ->getRaw()
            ->fetch();

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