<?php

declare(strict_types=1);

namespace App\Service\Export\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractXlsDocument
{
    protected Spreadsheet $spreadsheet;
    protected string $filename;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setActiveSheetIndex(0);
    }

    public function getResponse(): Response
    {
        $file = tempnam(sys_get_temp_dir(), $this->filename);
        $this->save($file);

        $response = new BinaryFileResponse($file, Response::HTTP_OK);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $this->filename);

        return $response;
    }

    public function save(string $file): void
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        $writer->save($file);
    }

    protected function loadTemplate(string $template): void
    {
        $this->spreadsheet = IOFactory::load('../templates/exports/'.$template);
    }

    protected function setBorder(int $colonne, int $ligne): void
    {
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $this->spreadsheet->getActiveSheet()->getCell([$colonne, $ligne])->getStyle()->applyFromArray($styleArray);
        // $this->spreadsheet->getActiveSheet()->getStyleByColumnAndRow($colonne, $ligne)->applyFromArray($styleArray);
    }

    protected function print(int $colonne, int $ligne, \Stringable $libelle): void
    {
        $this->spreadsheet->getActiveSheet()->getCell([$colonne, $ligne])->setValue(trim((string) $libelle));
    }
}
