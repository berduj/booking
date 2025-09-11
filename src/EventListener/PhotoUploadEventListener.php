<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::preFlush, method: 'onPreFlush', entity: Photo::class)]
#[AsEntityListener(event: Events::preRemove, method: 'onPreRemove', entity: Photo::class)]
final class PhotoUploadEventListener
{
    private string $photoDir;

    public function __construct(private readonly Filesystem $fs, private readonly SluggerInterface $slugger, string $photoDir)
    {
        $this->photoDir = $photoDir;
    }

    public function onPreFlush(Photo $object, EventArgs $args): void
    {
        $destinationDir = $this->getDestinationDir($object, true);
        $file = $object->file;
        if ($file instanceof UploadedFile) {
            $fileName = (string) $this->slugger->slug(str_replace($file->getClientOriginalExtension(), uniqid(), $file->getClientOriginalName())).'.'.$file->getClientOriginalExtension();
            $this->fs->remove($destinationDir.'/'.$object->getFilepath());

            $file->move($destinationDir, $fileName);
            $object->setFilename($fileName);
            $object->setTitre($file->getClientOriginalName());
        }
    }

    private function getDestinationDir(Photo $object, bool $forceCreation): string
    {
        $destinationDir = $this->photoDir.$object->getDir().'/';
        if (!is_dir($destinationDir) && $forceCreation) {
            $this->fs->mkdir($destinationDir, 0777);
        }

        return $destinationDir;
    }

    public function onPreRemove(Photo $object, EventArgs $args): void
    {
        if ($object->getFilename() !== '') {
            $destinationDir = $this->getDestinationDir($object, false);
            $this->fs->remove($destinationDir.$object->getFilename());
        }
    }
}
