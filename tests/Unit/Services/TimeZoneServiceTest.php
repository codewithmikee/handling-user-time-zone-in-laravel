<?php

namespace Tests\Unit\Services;

use App\Services\TimeZoneHandlers\UserTimeZoneHandler as TimeZoneService;
use Carbon\Carbon;
use Tests\TestCase;

class TimeZoneServiceTest extends TestCase
{
    // ================= Conversion Tests =================

    /** @test */
    public function it_converts_utc_to_timezone()
    {
        $utcTime = '2023-01-01 12:00:00';
        $nyTime = TimeZoneService::toTimeZone($utcTime, 'America/New_York');

        $this->assertEquals('07:00:00', $nyTime->format('H:i:s'));
        $this->assertEquals('America/New_York', $nyTime->timezoneName);
    }

    /** @test */
    public function it_converts_timezone_to_utc()
    {
        $nyTime = '2023-01-01 07:00:00';
        $utcTime = TimeZoneService::toUtc($nyTime, 'America/New_York');

        $this->assertEquals('12:00:00', $utcTime->format('H:i:s'));
        $this->assertEquals('UTC', $utcTime->timezoneName);
    }

    /** @test */
    public function it_handles_invalid_timezone_by_defaulting_to_utc()
    {
        $time = TimeZoneService::toTimeZone('now', 'Invalid/Timezone');
        $this->assertEquals('UTC', $time->timezoneName);
    }

    // ================= Instance Method Tests =================

    /** @test */
    public function instance_methods_use_configured_timezone()
    {
        $service = new TimeZoneService('Asia/Tokyo');
        $utcTime = '2023-01-01 12:00:00';

        $localTime = $service->toUserTimeZone($utcTime);
        $this->assertEquals('21:00:00', $localTime->format('H:i:s'));
    }

    // ================= Formatting Tests =================

    /** @test */
    public function it_formats_for_google_calendar()
    {
        $time = Carbon::parse('2023-01-01 12:00:00', 'UTC');
        $formatted = TimeZoneService::formatForProvider($time, 'google');

        $this->assertEquals('2023-01-01T12:00:00+00:00', $formatted['dateTime']);
    }

    // ================= Time Range Tests =================

    /** @test */
    public function it_converts_time_ranges_between_zones()
    {
        $ranges = [['start_time' => '09:00:00', 'end_time' => '17:00:00']];
        $converted = TimeZoneService::convertTimeRanges($ranges, 'UTC', 'Asia/Tokyo');

        $this->assertEquals('18:00:00', $converted[0]['start_time']);
        $this->assertEquals('02:00:00', $converted[0]['end_time']);
    }
}
