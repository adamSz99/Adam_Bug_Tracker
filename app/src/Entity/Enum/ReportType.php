<?php
/**
 * Report type.
 */

namespace App\Entity\Enum;

/**
 * Enum ReportType.
 */
enum ReportType: string
{
    case BUG = 'BUG';

    case UNKNOWN = 'UNKNOWN';

    case IMPROVEMENT = 'IMPROVEMENT';

    case FEATURE_REQUEST = 'FEATURE_REQUEST';

    /**
     * Get the type label.
     *
     * @return string Enum value label
     */
    public function label(): string
    {
        return match ($this) {
            ReportType::BUG => 'label.bug',
            ReportType::UNKNOWN => 'label.unknown',
            ReportType::IMPROVEMENT => 'label.improvement',
            ReportType::FEATURE_REQUEST => 'label.feature_request'
        };
    }
}
