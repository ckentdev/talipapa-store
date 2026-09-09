<?php

namespace App\Enums;

enum VocSource: string
{
    case Search = 'search';
    case Voice = 'voice';
    case Playground = 'playground';
}
