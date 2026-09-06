<?php

namespace App\Services;

use App\Models\Discount;

class DiscountService
{
    public function calculate(string $itemType, object $item, ?string $couponCode = null): array
    {
        $baseAmount = max(0.0, (float) ($item->price ?? 0));
        if ($baseAmount <= 0) return $this->result($baseAmount, null, null);

        $rules = [];
        foreach (Discount::where('is_active', 1)->get() as $rule) {
            if (!$this->isUsable($rule, $itemType, $item, $couponCode)) continue;
            $rules[] = $rule;
        }

        $bestRule = null;
        $bestAmount = 0.0;
        foreach ($rules as $rule) {
            $amount = $this->discountAmount($rule, $baseAmount);
            if ($amount > $bestAmount) {
                $bestAmount = $amount;
                $bestRule = $rule;
            }
        }

        return $this->result($baseAmount, $bestAmount, $bestRule, $couponCode);
    }

    private function isUsable($rule, string $itemType, object $item, ?string $couponCode): bool
    {
        $now = time();
        if (($rule->code ?? null) !== null) {
            if ($couponCode === null || strtoupper(trim((string) $rule->code)) !== strtoupper(trim($couponCode))) return false;
        } elseif ($couponCode !== null && trim($couponCode) !== '') {
            return false;
        }

        if (!in_array((string) $rule->applies_to, ['all', $itemType], true)) return false;
        if (!empty($rule->starts_at) && strtotime((string) $rule->starts_at) > $now) return false;
        if (!empty($rule->ends_at) && strtotime((string) $rule->ends_at) < $now) return false;
        if ($rule->usage_limit !== null && (int) $rule->usage_count >= (int) $rule->usage_limit) return false;
        if ((float) ($item->price ?? 0) < (float) ($rule->min_amount ?? 0)) return false;

        $target = (string) ($rule->target_type ?? 'all');
        if ($target === 'all') return true;
        if ($target === 'book' || $target === 'subscription') {
            return $target === $itemType && (int) $rule->target_id === (int) $item->id;
        }
        if ($itemType !== 'book') return false;
        if ($target === 'category') return (int) $rule->target_id === (int) ($item->category_id ?? 0);
        if ($target === 'author') return (int) $rule->target_id === (int) ($item->author_id ?? 0);
        return false;
    }

    private function discountAmount($rule, float $baseAmount): float
    {
        $amount = (string) $rule->type === 'percent'
            ? $baseAmount * min(100.0, max(0.0, (float) $rule->value)) / 100
            : max(0.0, (float) $rule->value);
        if ($rule->max_discount !== null) $amount = min($amount, max(0.0, (float) $rule->max_discount));
        return min($baseAmount, round($amount, 2));
    }

    private function result(float $baseAmount, ?float $discountAmount, $rule, ?string $couponCode = null): array
    {
        $discountAmount = $discountAmount ?? 0.0;
        return [
            'base_amount' => $baseAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0.0, round($baseAmount - $discountAmount, 2)),
            'discount' => $rule,
            'coupon_valid' => $couponCode === null || trim($couponCode) === ''
                ? null
                : $rule !== null && ($rule->code ?? null) !== null,
        ];
    }
}
