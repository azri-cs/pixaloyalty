<?php

namespace App\Enums;

enum PointTransactionType: string
{
    case ISSUE = 'issue';
    case DEDUCT = 'deduct';

    public function getLabel(): string
    {
        return match($this) {
            self::ISSUE => 'Issue Points',
            self::DEDUCT => 'Deduct Points',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::ISSUE => 'success',
            self::DEDUCT => 'danger',
        };
    }
}
