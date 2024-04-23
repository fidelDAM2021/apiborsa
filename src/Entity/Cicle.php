<?php

namespace App\Entity;

use App\Repository\CicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CicleRepository::class)]
#[UniqueEntity('nomcicle',message:"Ja hi ha un cicle amb aquest nom")]
class Cicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:"El nom del cicle és obligatori")]
    private ?string $nomcicle = null;

    #[ORM\Column(length: 1)]
    #[Assert\NotBlank(message:"El grau del cicle és obligatori")]
    #[Assert\Choice(choices: ['B', 'M', 'S', 'E'], message: 'El grau ha de ser B, M, S o E')]
    private ?string $graucicle = null;

    #[ORM\ManyToMany(targetEntity: Alumne::class, mappedBy: 'cicle')]
    #[MaxDepth(1)]
    private Collection $alumnes;

    #[ORM\ManyToMany(targetEntity: Oferta::class, mappedBy: 'cicle')]
    #[MaxDepth(1)]
    private Collection $ofertas;

    public function __construct()
    {
        $this->alumnes = new ArrayCollection();
        $this->ofertas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomcicle(): ?string
    {
        return $this->nomcicle;
    }

    public function setNomcicle(string $nomcicle): self
    {
        $this->nomcicle = $nomcicle;

        return $this;
    }

    public function getGraucicle(): ?string
    {
        return $this->graucicle;
    }

    public function setGraucicle(string $graucicle): self
    {
        $this->graucicle = $graucicle;

        return $this;
    }

    /**
     * @return Collection<int, Alumne>
     */
    public function getAlumnes(): Collection
    {
        return $this->alumnes;
    }

    public function addAlumne(Alumne $alumne): self
    {
        if (!$this->alumnes->contains($alumne)) {
            $this->alumnes->add($alumne);
            $alumne->addCicle($this);
        }

        return $this;
    }

    public function removeAlumne(Alumne $alumne): self
    {
        if ($this->alumnes->removeElement($alumne)) {
            $alumne->removeCicle($this);
        }

        return $this;
    }
    public function __toString(): string {
        return $this->getNomcicle();
    }

    /**
     * @return Collection<int, Oferta>
     */
    public function getOfertas(): Collection
    {
        return $this->ofertas;
    }

    public function addOferta(Oferta $oferta): self
    {
        if (!$this->ofertas->contains($oferta)) {
            $this->ofertas->add($oferta);
            $oferta->addCicle($this);
        }

        return $this;
    }

    public function removeOferta(Oferta $oferta): self
    {
        if ($this->ofertas->removeElement($oferta)) {
            $oferta->removeCicle($this);
        }

        return $this;
    }
    public function toArray(): array {
        $cicleArray=[
            'id'=>$this->id,
            'nomcicle'=>$this->nomcicle,
            'graucicle'=>$this->graucicle,
        ];
        return $cicleArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->nomcicle=$content["nomcicle"];
        $this->graucicle=$content["graucicle"];
    }
}
