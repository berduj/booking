<?php

declare(strict_types=1);

namespace App\Service\Export\Excel;

use App\Entity\Export;
use App\Security\UserPersonneInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ExportExporter extends AbstractExport
{
    protected ?Export $export;
    protected ?UserPersonneInterface $personne;

    public function setExport(Export $export, UserPersonneInterface $personne): self
    {
        $this->export = $export;
        $this->personne = $personne;

        $slugger = new AsciiSlugger('fr');
        $this->filename = $slugger->slug((string) $export->getLibelle()).'-'.date('Y-m-d').'.xls';

        return $this;
    }

    public function execute(): void
    {
        if (!$this->export) {
            throw new \Exception("Vous devez setter un export avant d'utiliser la function execute");
        }
        $this->setTitre((string) $this->export->getLibelle());

        if (!$this->export or !$this->export->getRequete()) {
            return;
        }

        $sql = $this->export->getRequete();

        if ($this->personne and $this->personne->getId()) {
            $sql = str_replace('#USER#', $this->personne->getId()->toBinary(), $sql);
        }

        $data = $this->getData($sql);

        $this->displayData($data);
    }
}
