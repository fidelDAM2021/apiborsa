<?php

namespace App\Entity;

use App\Repository\AlumneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Cicle;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlumneRepository::class)]
class Alumne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"El nom de l'alumne és obligatori")]
    private ?string $nomalumne = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"Els cognoms de l'alumne són obligatoris")]
    private ?string $cognoms = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"La població de l'alumne és obligatoria")]
    private ?string $poblacio = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"El telèfon de l'alumne és obligatori")]
    private ?string $telefon = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message:"L'email de l'alumne és obligatori")]
    #[Assert\Email(message:"L'email no és vàlid")]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"El transport de l'alumne és obligatori")]
    #[Assert\Type(type: 'bool',message:"El transport ha de ser un booleà")]
    private ?bool $transport = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"L'estat actiu de l'alumne és obligatori")]
    #[Assert\Type(type: 'bool',message:"Actiu ha de ser un booleà")]
    private ?bool $actiu = null;

    #[ORM\ManyToMany(targetEntity: Cicle::class, inversedBy: 'alumnes')]
    private Collection $cicle;

    #[ORM\OneToOne(mappedBy: 'alumne', cascade: ['persist', 'remove'])]
    private ?Curriculum $curriculum = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'bool',message:"pdf ha de ser un booleà")]
    private ?bool $pdf = null;

    private Array $codiscicle;
    private Array $dadescurriculum;

    public function getCodisCicle(): ?Array {
        return $this->codiscicle;
    }

    public function getDadesCurriculum(): ?Array {
        return $this->dadescurriculum;
    }

    public function __construct()
    {
        $this->cicle = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomalumne(): ?string
    {
        return $this->nomalumne;
    }

    public function setNomalumne(string $nomalumne): self
    {
        $this->nomalumne = $nomalumne;

        return $this;
    }

    public function getCognoms(): ?string
    {
        return $this->cognoms;
    }

    public function setCognoms(string $cognoms): self
    {
        $this->cognoms = $cognoms;

        return $this;
    }

    public function getPoblacio(): ?string
    {
        return $this->poblacio;
    }

    public function setPoblacio(string $poblacio): self
    {
        $this->poblacio = $poblacio;

        return $this;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(string $telefon): self
    {
        $this->telefon = $telefon;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isTransport(): ?bool
    {
        return $this->transport;
    }

    public function setTransport(bool $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function isActiu(): ?bool
    {
        return $this->actiu;
    }

    public function setActiu(bool $actiu): self
    {
        $this->actiu = $actiu;

        return $this;
    }

    /**
     * @return Collection<int, Cicle>
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
    public function getValues(): ?array {
        return get_object_vars($this);
    }

    public function getCurriculum(): ?Curriculum
    {
        return $this->curriculum;
    }

    public function setCurriculum(?Curriculum $curriculum): self
    {
        // unset the owning side of the relation if necessary
        if ($curriculum === null && $this->curriculum !== null) {
            $this->curriculum->setAlumne(null);
        }

        // set the owning side of the relation if necessary
        if ($curriculum !== null && $curriculum->getAlumne() !== $this) {
            $curriculum->setAlumne($this);
        }

        $this->curriculum = $curriculum;

        return $this;
    }
    public function __toString(): string {
        return $this->getCognoms().", ".$this->getNomalumne();
    }

    public function isPdf(): ?bool
    {
        return $this->pdf;
    }

    public function setPdf(?bool $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }
    public function toArray(): array {
        $ciclesArray=[];
        foreach($this->getCicle() as $cicle) {
            $ciclesArray[]=$cicle->toArray();
        }
        $alumneArray=[
            'id'=>$this->id,
            'nomalumne'=>$this->nomalumne,
            'cognoms'=>$this->cognoms,
            'poblacio'=>$this->poblacio,
            'telefon'=>$this->telefon,
            'email'=>$this->email,
            'transport'=>$this->transport,
            'actiu'=>$this->actiu,
            'pdf'=>$this->pdf,
            'cicles'=>$ciclesArray,
            'curriculum'=>[
                'experiencia'=>$this->curriculum->getExperiencia(),
                'idiomes'=>$this->curriculum->getIdiomes(),
                'estudis'=>$this->curriculum->getEstudis(),
                'competencies'=>$this->curriculum->getCompetencies()
            ]
        ];
        return $alumneArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->nomalumne=$content["nomalumne"];
        $this->cognoms=$content['cognoms'];
        $this->poblacio=$content['poblacio'];
        $this->telefon=$content['telefon'];
        $this->email=$content['email'];
        $this->transport=$content['transport'];
        $this->actiu=$content['actiu'];
        $this->pdf=$content['pdf'];
        $this->codiscicle=[];
        foreach($content['cicles'] as $cicle) {
            $this->codiscicle[]=$cicle;
        }
        $this->dadescurriculum=[
            'experiencia'=>$content['experiencia'],
            'idiomes'=>$content['idiomes'],
            'estudis'=>$content['estudis'],
            'competencies'=>$content['competencies']
        ];
    }
}
