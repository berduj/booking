<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Document;
use App\Entity\Interfaces\DocumentableInterface;
use App\Repository\DocumentRepository;

class DocumentProvider
{
    public function __construct(private readonly DocumentRepository $documentRepository)
    {
    }

    /**
     * @return array<int, Document>
     */
    public function getDocuments(DocumentableInterface $object): array
    {
        return $this->documentRepository->findBy([
            'foreignClass' => $object::class,
            'foreignId' => $object->getId(),
        ], [
            'sortable' => 'ASC',
        ]);
    }
}
