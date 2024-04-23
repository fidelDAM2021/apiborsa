<?php

namespace App\Entity;

use App\Repository\SectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SectorRepository::class)]
#[UniqueEntity('nomsector',message:"Ja hi ha un sector amb aquest nom")]
class Sector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"El nom del sector és obligatori")]

    private ?string $nomsector = null;

    #[ORM\OneToMany(mappedBy: 'idsector', targetEntity: Empresa::class)]
    private Collection $empresas;

    public function __construct()
    {
        $this->empresas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomsector(): ?string
    {
        return $this->nomsector;
    }

    public function setNomsector(string $nomsector): self
    {
        $this->nomsector = $nomsector;

        return $this;
    }

    /**
     * @return Collection<int, Empresa>
     */
    public function getEmpresas(): Collection
    {
        return $this->empresas;
    }

    public function addEmpresa(Empresa $empresa): self
    {
        if (!$this->empresas->contains($empresa)) {
            $this->empresas->add($empresa);
            $empresa->setIdsector($this);
        }

        return $this;
    }

    public function removeEmpresa(Empresa $empresa): self
    {
        if ($this->empresas->removeElement($empresa)) {
            // set the owning side to null (unless already changed)
            if ($empresa->getIdsector() === $this) {
                $empresa->setIdsector(null);
            }
        }

        return $this;
    }
    public function __toString(): string {
        return $this->getNomsector();
    }
    public function toArray(): array {
        $sectorArray=[
            'id'=>$this->id,
            'nomsector'=>$this->nomsector,
        ];
        return $sectorArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->nomsector=$content["nomsector"];
    }
}
