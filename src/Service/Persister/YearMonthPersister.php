<?php

declare(strict_types=1);

namespace App\Service\Persister;

use App\Infrastructure\SessionInterface;
use App\ValueObject\YearMonth;

class YearMonthPersister
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function set(YearMonth $yearMonth, string $prefix): void
    {
        $this->session->set($prefix.'_year_month_year', $yearMonth->getYear());
        $this->session->set($prefix.'_year_month_month', $yearMonth->getMonth());
    }

    public function get(string $prefix): YearMonth
    {
        $year = (int) $this->session->get($prefix.'_year_month_year', 0);
        $month = (int) $this->session->get($prefix.'_year_month_month', 0);

        return new YearMonth($year, $month);
    }
}
