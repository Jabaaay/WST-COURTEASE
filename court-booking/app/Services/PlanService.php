<?php

namespace App\Services;

class PlanService
{
    public static function getPlanFeatures($plan)
    {
        return [
            'basic' => [
                'weekly_booking_limit' => 2,
                'can_book_weekends' => false,
                'can_reschedule' => false,
                'advance_booking_days' => 7,
            ],
            'premium' => [
                'weekly_booking_limit' => 4,
                'can_book_weekends' => true,
                'can_reschedule' => true,
                'advance_booking_days' => 14,
            ],
            'ultimate' => [
                'weekly_booking_limit' => -1, // unlimited
                'can_book_weekends' => true,
                'can_reschedule' => true,
                'advance_booking_days' => 30,
            ],
        ][$plan] ?? null;
    }

    public static function canMakeBooking($user, $tenant)
    {
        // Check if user has reached weekly booking limit
        if ($tenant->weekly_booking_limit > 0 && $user->weekly_booking_count >= $tenant->weekly_booking_limit) {
            return false;
        }

        // Check if booking is for weekend
        if (!$tenant->can_book_weekends && now()->isWeekend()) {
            return false;
        }

        return true;
    }

    public static function canReschedule($user, $tenant)
    {
        if (!$tenant->can_reschedule) {
            return false;
        }

        // For premium plan, limit reschedules
        if ($tenant->plan === 'premium' && $user->reschedule_count >= 1) {
            return false;
        }

        return true;
    }

    public static function canBookInAdvance($tenant, $bookingDate)
    {
        $maxAdvanceDate = now()->addDays($tenant->advance_booking_days);
        return $bookingDate <= $maxAdvanceDate;
    }

    public static function updateTenantPlan($tenant, $plan)
    {
        $features = self::getPlanFeatures($plan);
        if (!$features) {
            return false;
        }

        $tenant->update([
            'plan' => $plan,
            'weekly_booking_limit' => $features['weekly_booking_limit'],
            'can_book_weekends' => $features['can_book_weekends'],
            'can_reschedule' => $features['can_reschedule'],
            'advance_booking_days' => $features['advance_booking_days'],
        ]);

        return true;
    }
} 