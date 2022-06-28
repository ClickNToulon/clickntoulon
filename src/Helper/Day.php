<?php

namespace App\Helper;

use JetBrains\PhpStorm\ArrayShape;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class Day
{
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;
    public const SUNDAY = 7;

    #[ArrayShape([
        self::MONDAY => "string",
        self::TUESDAY => "string",
        self::WEDNESDAY => "string",
        self::THURSDAY => "string",
        self::FRIDAY => "string",
        self::SATURDAY => "string",
        self::SUNDAY => "string"
    ])]
    public static function getWeekDays(): array
    {
        return [
            self::MONDAY => 'Lundi',
            self::TUESDAY => 'Mardi',
            self::WEDNESDAY => 'Mercredi',
            self::THURSDAY => 'Jeudi',
            self::FRIDAY => 'Vendredi',
            self::SATURDAY => 'Samedi',
            self::SUNDAY => 'Dimanche'
        ];
    }
}