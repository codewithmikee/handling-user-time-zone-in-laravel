<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TimeZoneHandlers\UserTimeZoneHandler;

class TimeZoneTestController extends Controller
{
    public function show()
    {
        return view('timezone-converter', [
            'timezones' => [
                'UTC',
                'America/New_York',
                'Europe/London',
                'Asia/Tokyo'
            ]
        ]);
    }

    public function convert(Request $request)
    {
        $handler = new UserTimeZoneHandler($request->timezone);

        return [
            'conversions' => [
                'UTC' => $handler->toUtc($request->datetime)->format('Y-m-d H:i:s'),
                'New York' => $handler->toTimeZone($request->datetime, 'America/New_York')
                    ->format('Y-m-d H:i:s'),
                'London' => $handler->toTimeZone($request->datetime, 'Europe/London')
                    ->format('Y-m-d H:i:s'),
                'Tokyo' => $handler->toTimeZone($request->datetime, 'Asia/Tokyo')
                    ->format('Y-m-d H:i:s'),
            ]
        ];
    }
}
