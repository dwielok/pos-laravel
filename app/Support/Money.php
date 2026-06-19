<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * All money in this system is stored and computed as integer cents
 * (smallest currency unit) to avoid floating-point rounding errors in
 * tax/discount math. This value object is the single place that
 * converts between "cents" (storage/computation) and "display units"
 * (what a human types into a form or reads on a receipt).
 *
 * Usage:
 *   Money::fromUnits(19.99)->cents()        // 1999
 *   Money::fromCents(1999)->formatted()     // "19.99" (currency symbol applied in Blade/helper)
 *   Money::fromCents(1000)->add(Money::fromCents(500))->cents() // 1500
 */
final class Money
{
    private function __construct(private readonly int $cents) {}

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    /**
     * @param float|string $units e.g. 19.99
     */
    public static function fromUnits(float|string $units): self
    {
        // round() before cast to avoid float-precision artifacts like 19.99 -> 1998.9999...
        return new self((int) round(((float) $units) * 100));
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function units(): float
    {
        return $this->cents / 100;
    }

    public function add(Money $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(Money $other): self
    {
        return new self($this->cents - $other->cents);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->cents * $factor));
    }

    /**
     * Apply a percentage (e.g. 10 for 10%) and return the resulting amount.
     */
    public function percentage(float $percent): self
    {
        if ($percent < 0) {
            throw new InvalidArgumentException('Percentage cannot be negative.');
        }

        return new self((int) round($this->cents * ($percent / 100)));
    }

    public function isNegative(): bool
    {
        return $this->cents < 0;
    }

    public function isZero(): bool
    {
        return $this->cents === 0;
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function greaterThan(Money $other): bool
    {
        return $this->cents > $other->cents;
    }

    public function lessThan(Money $other): bool
    {
        return $this->cents < $other->cents;
    }

    /**
     * Formatted as a plain decimal string, e.g. "19.99". Currency symbol /
     * thousands separators are a presentation concern handled by the
     * `money()` Blade helper (resources/views + Settings), not here —
     * keeps this class free of locale/config dependencies.
     */
    public function formatted(): string
    {
        return number_format($this->units(), 2, '.', '');
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
