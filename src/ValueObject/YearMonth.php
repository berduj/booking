<?php

declare(strict_types=1);

namespace App\ValueObject;

class YearMonth
{
    private int $month;
    private int $year;

    public function __construct(int $year, int $month)
    {
        if ($month === 0) {
            $month = (int) date('m');
        }
        if ($year === 0) {
            $year = (int) date('Y');
        }
        $this->month = $month;
        $this->year = $year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getForSql(): string
    {
        if ($this->month === -1) {
            return sprintf('%04d-', $this->year).'%';
        }

        return sprintf('%04d-%02d-', $this->year, $this->month).'%';
    }

    public function __toString(): string
    {
        return \sprintf('%02d/%04d 00:00:00', $this->month, $this->year);
    }

    public function getLastDay(): \DateTime
    {
        $date = $this->getFirstDay();
        $end = $date->add(new \DateInterval('P1M'));

        return $end;
    }

    public function getFirstDay(): \DateTime
    {
        if ($this->month === -1) {
            return new \DateTime(sprintf('%04d-%02d-%02d', $this->year, 1, 1));
        }

        return new \DateTime(sprintf('%04d-%02d-%02d', $this->year, $this->month, 1));
    }
}
