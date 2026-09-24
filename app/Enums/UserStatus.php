<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BANNED = 'banned';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Hoạt động',
            self::BANNED => 'Đã khóa',
            self::INACTIVE => 'Chưa kích hoạt',
        };
    }
}
