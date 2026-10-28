<?php

declare(strict_types=1);

namespace App\Comparison\Enum;

enum Coverage: string
{
    case RoadsideAssistance = 'roadside_assistance';
    case GlassCoverage = 'glass_coverage';
    case DriverInjury = 'driver_injury';
}
