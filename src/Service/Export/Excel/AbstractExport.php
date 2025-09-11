<?php

declare(strict_types=1);

namespace App\Service\Export\Excel;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractExport extends AbstractXlsDocument
{
    /**
     * @var array<mixed>
     */
    protected array $styleArrayTitre = [
        'font' => [
            'bold' => true,
            'size' => 13,
        ],
    ];

    private Connection $connection;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        /** @var Connection $connection */
        $connection = $managerRegistry->getConnections()['readonly'];
        $this->connection = $connection;
    }

    abstract public function execute(): void;

    /**
     * @return array<int, mixed>
     */
    protected function getData(string $sql): array
    {
        $statement = $this->connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }

    protected function setTitre(string $titre): void
    {
        $this->spreadsheet->getActiveSheet()->setCellValue('A1', $titre);
        $this->spreadsheet->getActiveSheet()->setCellValue('A2', date('d/m/Y H\hi'));
        $this->spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($this->styleArrayTitre);
        $this->spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        $this->spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
    }

    /**
     * @param array<mixed> $data
     */
    protected function displayData(array $data): void
    {
        if (!count($data)) {
            return;
        }

        $sheet = $this->spreadsheet->getActiveSheet();

        $cols = array_keys($data[0]);
        $sheet->fromArray($cols, null, 'A4')
            ->fromArray($data, null, 'A5');

        $col = 1;

        foreach ($data[0] as $v) {
            $sheet->getColumnDimensionByColumn($col)->setWidth(20);
            $sheet->getCell([$col, 4])->getStyle()->getFont()->setBold(true);
            $col++;
        }
    }
}
