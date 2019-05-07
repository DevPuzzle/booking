<?php


namespace App\Types;


interface RoleTypes
{
    const GUIDE = 0;
    const OPS = 1;
    const MANAGER = 2;

    const NAMES = [
        0 => 'guide',
        1 => 'ops',
        2 => 'manager',
    ];
}
