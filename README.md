# Get Parcel Feed Tracker

[![Build Status](https://travis-ci.org/afiqiqmal/parcel-track.svg?branch=master)](https://travis-ci.org/afiqiqmal/parcel-track)
[![Coverage](https://img.shields.io/codecov/c/github/afiqiqmal/parcel-track.svg)](https://codecov.io/gh/afiqiqmal/parcel-track)

It is simple wrapper class written in php to fetch posts from certain Malaysian Parcel Data

* Currently just only POSTLAJU, GDEX, ABXExpress

Tested in PHP 7.1

## Installation

#### Step 1: Install from composer
```
composer require afiqiqmal/parcel-track
```
Alternatively, you can specify as a dependency in your project's existing composer.json file
```
{
   "require": {
      "afiqiqmal/parcel-tracker": "^1.0"
   }
}
```

## Usage
After installing, you need to require Composer's autoloader and add your code.

```php
require_once __DIR__ .'/../vendor/autoload.php';
```

#### Sample for Post Laju
```php
$data = parcel_track()
	->postLaju()
	->setTrackingNumber("ER157080065MY")
	->fetch();
```

#### Sample for GDex
```php
$data = parcel_track()
	->gdex()
	->setTrackingNumber("4941410530")
	->fetch();
```

#### Sample for Abx Express
```php
$data = parcel_track()
	->abxExpress()
	->setTrackingNumber("EZP843055940197")
	->fetch();
```


### Method
<table border="1">
    <tr>
        <th>Method</th>
        <th>Param</th>
        <th>Description</th>
    </tr>
    <tr>
        <td>postLaju()</td>
        <td></td>
        <td>Post Laju Courier</td>
    </tr>
    <tr>
        <td>abxExpress()</td>
        <td></td>
        <td>Post Laju Courier</td>
    </tr>
    <tr>
        <td>gdex()</td>
        <td></td>
        <td>GD Express Courier</td>
    </tr>
    <tr>
        <td>setTrackingNumber($refNumber)</td>
        <td><code>String</code></td>
        <td>Enter the tracking number</td>
    </tr>
</table>


### Result

You should getting data similarly like below:
```json
{
    "code": 200,
    "error": false,
    "tracker": [
        {
            "date": "2018-02-05 16:24:20",
            "timestamp": 1517847860,
            "process": "Item posted over the counter",
            "type": "item_received",
            "event": "Pos Laju Temerloh"
        },
        {
            "date": "2018-02-05 22:01:57",
            "timestamp": 1517868117,
            "process": "Item dispatched out",
            "type": "dispatch",
            "event": "Pos Laju Temerloh"
        },
        {
            "date": "2018-02-06 03:44:45",
            "timestamp": 1517888685,
            "process": "Consignment dispatch out from Transit Office",
            "type": "dispatch",
            "event": "Pos Laju Transit Office"
        },
        {
            "date": "2018-02-07 12:20:25",
            "timestamp": 1518006025,
            "process": "Arrive at delivery facility at",
            "type": "arrived_facility",
            "event": "Pos Laju Keningau"
        },
        {
            "date": "2018-02-08 07:44:49",
            "timestamp": 1518075889,
            "process": "Arrive at delivery facility at",
            "type": "arrived_facility",
            "event": "Pos Laju Keningau"
        },
        {
            "date": "2018-02-08 07:51:36",
            "timestamp": 1518076296,
            "process": "Item out for delivery",
            "type": "out_for_delivery",
            "event": "Pos Laju Keningau"
        },
        {
            "date": "2018-02-08 14:01:24",
            "timestamp": 1518098484,
            "process": "Item delivered to HASMIN",
            "type": "delivered",
            "event": "Pos Laju Keningau"
        }
    ],
    "footer": {
        "source": "Post Laju",
        "developer": {
            "name": "Hafiq",
            "homepage": "https:\/\/github.com\/afiqiqmal"
        }
    }
}
```

## Todo
- Struggling for other Parcel Data

## License
Licensed under the [MIT license](http://opensource.org/licenses/MIT)
