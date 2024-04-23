<?php

namespace App\Entity;

use App\Repository\OfertaRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfertaRepository::class)]
class Oferta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ofertas')]
    #[ORM\JoinColumn(referencedColumnName:'nif',nullable: false)]
    #[Assert\NotBlank(message:"El CIF de l'empresa és obligatori")]
    private ?Empresa $NIFempresa = null;

    #[ORM\Column(type:'date')]
    #[Assert\NotBlank(message:"La data de l'oferta és obligatòria")]
    #[Assert\DateValidator(message:"La data de l'oferta no és vàlida")]
    private ?\DateTime $data = null;

    #[ORM\Column(nullable: true)]
    private ?bool $estat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"El text de l'oferta és obligatori")]
    private ?string $textoferta = null;

    #[ORM\ManyToMany(targetEntity: Cicle::class, inversedBy: 'ofertas')]
    private Collection $cicle;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $experiencia = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idiomes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altres = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urloferta = null;

    private Array $codiscicle;
    private ?String $nif;

    public function getNif(): ?String {
        return $this->nif;
    }
    public function getCodisCicle(): ?Array {
        return $this->codiscicle;
    }

    public function __construct()
    {
        $this->cicle = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNIFempresa(): ?empresa
    {
        return $this->NIFempresa;
    }

    public function setNIFempresa(?empresa $empresa): self
    {
        $this->NIFempresa = $empresa;

        return $this;
    }

    public function getData(): ?\DateTime
    {
        return $this->data;
    }

    public function setData(\DateTime $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function isEstat(): ?bool
    {
        return $this->estat;
    }

    public function setEstat(?bool $estat): self
    {
        $this->estat = $estat;

        return $this;
    }

    public function getTextoferta(): ?string
    {
        return $this->textoferta;
    }

    public function setTextoferta(string $textoferta): self
    {
        $this->textoferta = $textoferta;

        return $this;
    }

    /**
     * @return Collection<int, cicle>
     */
    public function getCicle(): Collection
    {
        return $this->cicle;
    }

    public function addCicle(Cicle $cicle): self
    {
        if (!$this->cicle->contains($cicle)) {
            $this->cicle->add($cicle);
        }

        return $this;
    }

    public function removeCicle(Cicle $cicle): self
    {
        $this->cicle->removeElement($cicle);

        return $this;
    }

    public function getExperiencia(): ?string
    {
        return $this->experiencia;
    }

    public function setExperiencia(?string $experiencia): self
    {
        $this->experiencia = $experiencia;

        return $this;
    }

    public function getIdiomes(): ?string
    {
        return $this->idiomes;
    }

    public function setIdiomes(?string $idiomes): self
    {
        $this->idiomes = $idiomes;

        return $this;
    }

    public function getAltres(): ?string
    {
        return $this->altres;
    }

    public function setAltres(?string $altres): self
    {
        $this->altres = $altres;

        return $this;
    }

    public function getUrloferta(): ?string
    {
        return $this->urloferta;
    }

    public function setUrloferta(?string $urloferta): self
    {
        $this->urloferta = $urloferta;

        return $this;
    }
    public function toArray(): array {
        $ciclesArray=[];
        foreach($this->getCicle() as $cicle) {
            $ciclesArray[]=$cicle->toArray();
        }
        $ofertaArray=[
            'id'=>$this->id,
            'empresa'=>[
                'nifempresa'=>$this->NIFempresa->getNIF(),
                'nomempresa'=>$this->NIFempresa->getNom()
            ],
            'data'=>$this->data->format("d/m/Y"),
            'estat'=>$this->estat,
            'textoferta'=>$this->textoferta,
            'experiencia'=>$this->experiencia,
            'idiomes'=>$this->idiomes,
            'altres'=>$this->altres,
            'urloferta'=>$this->urloferta,
            'cicles'=>$ciclesArray  
        ];
        return $ofertaArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->nif=$content["empresa"];
        $this->data=\DateTime::createFromFormat("Y-m-d",$content['data']);
        $this->estat=$content['estat'];
        $this->textoferta=$content['textoferta'];
        $this->experiencia=$content['experiencia'];
        $this->idiomes=$content['idiomes'];
        $this->altres=$content['altres'];
        $this->urloferta=$content['urloferta'];
        $this->codiscicle=[];
        foreach($content['cicles'] as $cicle) {
            $this->codiscicle[]=$cicle;
        }
    }
}
