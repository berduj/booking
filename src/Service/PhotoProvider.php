<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Interfaces\PhotoableInterface;
use App\Entity\Photo;
use App\Repository\PhotoRepository;

class PhotoProvider
{
    public function __construct(private readonly PhotoRepository $photoRepository)
    {
    }

    /**
     * @return array<int, Photo>
     */
    public function getPhotos(PhotoableInterface $object): array
    {
        return $this->photoRepository->findBy([
            'foreignClass' => $object::class,
            'foreignId' => $object->getId(),
        ], [
            'sortable' => 'ASC',
        ]);
    }
}
