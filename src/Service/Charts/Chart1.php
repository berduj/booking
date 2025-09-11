<?php

declare(strict_types=1);

namespace App\Service\Charts;

use Symfony\UX\Chartjs\Model\Chart;

class Chart1 extends AbstractSqlChart
{
    public function getChart(): Chart
    {
        $chart = $this->chartbuilder->createChart(Chart::TYPE_BAR);
        $data = $this->buildData();

        $chart->setData([
            'labels' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Nb actions 12 derniers mois',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => array_values($data),
                ],
            ],
        ]);

        return $chart;
    }

    /**
     * @return array<string,string>
     *
     * @throws \DateMalformedStringException
     */
    private function buildData(): array
    {
        /* créer un tableau vide */
        $currentDate = new \DateTime();
        $currentDate->modify('-1 year');
        for ($i = 0; $i < 12; $i++) {
            // Ajouter le premier jour du mois au tableau
            $data[$currentDate->format('Y-m')] = 0;
            $currentDate->modify('+1 month');
        }

        $date = new \DateTime();
        $date->modify('first day of this month');
        $date->modify('-1 year');

        $queryResult = $this->getData(
            "SELECT  DATE_FORMAT(date_debut, '%Y-%m') AS date, COUNT(*) AS actions
    FROM `action` WHERE date_debut > '".$date->format('Y-m-d')."'    GROUP BY 1 ORDER BY 1; "
        );

        foreach ($queryResult as $row) {
            $data[$row['date']] = $row['actions'];
        }

        return $data;
    }
}
