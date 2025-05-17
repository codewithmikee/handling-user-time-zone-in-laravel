<?php

/**
 * name: Mikiyas Birhanu
 * date: 2025-05-17
 * github: https://github.com/codewithmikee
 *
 * UserTimeZoneHandler.php
 *
 * This class provides comprehensive timezone handling functionality for Laravel applications.
 * It supports:
 * - Conversion between timezones (UTC ↔ user timezone)
 * - Calendar provider formatting (Google, Outlook)
 * - Time range conversions
 * - Supports both static and instance-based usage
 */

namespace App\Services\TimeZoneHandlers;

use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Comprehensive timezone handling service for Laravel applications
 *
 * Features:
 * - Convert between timezones (UTC ↔ user timezone)
 * - Calendar provider formatting (Google, Outlook)
 * - Time range conversions
 * - Supports both static and instance-based usage
 */
class UserTimeZoneHandler
{
    protected ?string $userTimeZone;

    /**
     * Initialize with a user's timezone (optional)
     * If not provided, methods require explicit timezone parameter
     */
    public function __construct(?string $userTimeZone = null)
    {
        if ($userTimeZone && !$this->isValidTimeZone($userTimeZone)) {
            Log::warning("Invalid timezone provided: {$userTimeZone}, falling back to UTC");
            $userTimeZone = 'UTC';
        }
        $this->userTimeZone = $userTimeZone;
    }

    // ================= Core Conversion Methods =================

    /**
     * Convert datetime from UTC to target timezone
     *
     * @param Carbon|string $utcTime Input time (parsed as UTC if string)
     * @param string|null $targetTimeZone Target timezone (uses instance timezone if null)
     * @return Carbon
     * @throws Exception On invalid datetime format
     */
    public static function toTimeZone($utcTime, ?string $targetTimeZone = null): Carbon
    {
        try {
            $targetTimeZone = self::validateTimeZone($targetTimeZone);
            $carbon = $utcTime instanceof Carbon ? $utcTime : Carbon::parse($utcTime, 'UTC');
            return $carbon->copy()->setTimezone($targetTimeZone);
        } catch (Exception $e) {
            Log::error("Timezone conversion error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convert datetime from source timezone to UTC
     *
     * @param Carbon|string $localTime Input time
     * @param string|null $sourceTimeZone Source timezone (uses instance timezone if null)
     * @return Carbon
     * @throws Exception On invalid datetime format
     */
    public static function toUtc($localTime, ?string $sourceTimeZone = null): Carbon
    {
        try {
            $sourceTimeZone = self::validateTimeZone($sourceTimeZone);
            $carbon = $localTime instanceof Carbon ? $localTime : Carbon::parse($localTime, $sourceTimeZone);
            return $carbon->copy()->setTimezone('UTC');
        } catch (Exception $e) {
            Log::error("UTC conversion error: " . $e->getMessage());
            throw $e;
        }
    }

    // ================= Instance Methods =================

    /**
     * Convert UTC to user's timezone (instance version)
     */
    public function toUserTimeZone($utcTime): Carbon
    {
        return self::toTimeZone($utcTime, $this->userTimeZone);
    }

    /**
     * Convert user's local time to UTC (instance version)
     */
    public function userTimeToUtc($localTime): Carbon
    {
        return self::toUtc($localTime, $this->userTimeZone);
    }

    // ================= Formatting Methods =================

    /**
     * Format datetime for calendar providers
     *
     * @param Carbon $time Input time
     * @param string $providerType 'google'|'outlook'|'iso8601'
     * @return array [dateTime: string, timeZone: 'UTC']
     */
    public static function formatForProvider(Carbon $time, string $providerType): array
    {
        $utcTime = $time->copy()->setTimezone('UTC');

        return match ($providerType) {
            'google' => [
                'dateTime' => $utcTime->toRfc3339String(),
                'timeZone' => 'UTC'
            ],
            'outlook' => [
                'dateTime' => $utcTime->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC'
            ],
            default => [
                'dateTime' => $utcTime->toIso8601String(),
                'timeZone' => 'UTC'
            ],
        };
    }

    // ================= Time Range Conversions =================

    /**
     * Convert time ranges between timezones
     *
     * @param array $ranges Array of [start_time, end_time] pairs
     * @param string $fromTimeZone Source timezone
     * @param string $toTimeZone Target timezone
     * @return array Converted time ranges in H:i:s format
     */
    public static function convertTimeRanges(
        array $ranges,
        string $fromTimeZone,
        string $toTimeZone
    ): array {
        $fromTimeZone = self::validateTimeZone($fromTimeZone);
        $toTimeZone = self::validateTimeZone($toTimeZone);

        return array_map(function ($range) use ($fromTimeZone, $toTimeZone) {
            return [
                'start_time' => Carbon::parse($range['start_time'], $fromTimeZone)
                    ->setTimezone($toTimeZone)
                    ->format('H:i:s'),
                'end_time' => Carbon::parse($range['end_time'], $fromTimeZone)
                    ->setTimezone($toTimeZone)
                    ->format('H:i:s'),
            ];
        }, $ranges);
    }

    // ================= Utility Methods =================

    /**
     * Validate and normalize timezone identifier
     */
    public static function validateTimeZone(?string $timeZone): string
    {
        if (!$timeZone || !self::isValidTimeZone($timeZone)) {
            Log::warning("Invalid timezone provided, falling back to UTC");
            return 'UTC';
        }
        return $timeZone;
    }

    /**
     * Check if timezone identifier is valid
     */
    public static function isValidTimeZone(string $timeZone): bool
    {
        return in_array($timeZone, timezone_identifiers_list(), true);
    }

    /**
     * Get current datetime in specified timezone
     */
    public static function nowInTimeZone(string $timeZone): Carbon
    {
        return Carbon::now()->setTimezone(self::validateTimeZone($timeZone));
    }
}
