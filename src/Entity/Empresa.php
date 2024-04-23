<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[UniqueEntity('NIF',message:"Aquest CIF ja està registrat")]
class Empresa
{
    #[ORM\Id]
    #[ORM\Column(length: 9)]
    #[Assert\NotBlank(message:"El CIF de l'empresa és obligatori")]
    private ?string $NIF = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"El nom de l'empresa és obligatori")]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"El domicili de l'empresa és obligatori")]
    private ?string $domicili = null;

    #[ORM\Column(length: 5)]
    #[Assert\Regex(pattern:"/^[0-9]{5}$/",message:"El codi postal ha de tenir 5 dígits")]
    #[Assert\NotBlank(message:"El codi postal de l'empresa és obligatori")]
    private ?string $cpostal = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"La població de l'empresa és obligatoria")]
    private ?string $poblacio = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"El telèfon de l'empresa és obligatori")]
    private ?string $telefon = null;

    #[ORM\Column(length: 60)]
    #[Assert\Email(message:"L'email no és vàlid")]
    #[Assert\NotBlank(message:"L'email de l'empresa és obligatori")]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'empresas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message:"El sector de l'empresa és obligatori")]
    private ?Sector $idsector = null;

    #[ORM\Column(length: 9, nullable: true)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'NIFempresa', targetEntity: Contacte::class)]
    private Collection $contactes;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $web = null;

    #[ORM\OneToMany(mappedBy: 'empresa', targetEntity: Oferta::class)]
    private Collection $ofertas;

    private ?string $codSector = null;

    public function __construct()
    {
        $this->contactes = new ArrayCollection();
        $this->ofertas = new ArrayCollection();
    }

    public function getNIF(): ?string
    {
        return $this->NIF;
    }

    public function setNIF(string $NIF): self
    {
        $this->NIF = $NIF;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDomicili(): ?string
    {
        return $this->domicili;
    }

    public function setDomicili(string $domicili): self
    {
        $this->domicili = $domicili;

        return $this;
    }

    public function getCpostal(): ?string
    {
        return $this->cpostal;
    }

    public function setCpostal(string $cpostal): self
    {
        $this->cpostal = $cpostal;

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

    public function getIdsector(): ?Sector
    {
        return $this->idsector;
    }

    public function setIdsector(?Sector $idsector): self
    {
        $this->idsector = $idsector;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Contacte>
     */
    public function getContactes(): Collection
    {
        return $this->contactes;
    }

    public function addContacte(Contacte $contacte): self
    {
        if (!$this->contactes->contains($contacte)) {
            $this->contactes->add($contacte);
            $contacte->setNIFempresa($this);
        }

        return $this;
    }

    public function removeContacte(Contacte $contacte): self
    {
        if ($this->contactes->removeElement($contacte)) {
            // set the owning side to null (unless already changed)
            if ($contacte->getNIFempresa() === $this) {
                $contacte->setNIFempresa(null);
            }
        }

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

        return $this;
    }
    public function __toString(): string {
        return $this->getNom();
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
            $oferta->setNIFEmpresa($this);
        }

        return $this;
    }

    public function removeOferta(Oferta $oferta): self
    {
        if ($this->ofertas->removeElement($oferta)) {
            // set the owning side to null (unless already changed)
            if ($oferta->getNIFEmpresa() === $this) {
                $oferta->setNIFEmpresa(null);
            }
        }

        return $this;
    }

    public function getCodSector(): ?string
    {
        return $this->codSector;
    }
    public function toArray(): array {
        $empresaArray=[
            'NIF'=>$this->NIF,
            'nom'=>$this->nom,
            'domicili'=>$this->domicili,
            'cpostal'=>$this->cpostal,
            'poblacio'=>$this->poblacio,
            'telefon'=>$this->telefon,
            'email'=>$this->email,
            'web'=>$this->web,
            'idsector'=>$this->idsector->getId(),
            'nomsector'=>$this->idsector->getNomsector()
        ];
        return $empresaArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->NIF=$content["NIF"];
        $this->nom=$content['nom'];
        $this->domicili=$content['domicili'];
        $this->cpostal=$content['cpostal'];
        $this->poblacio=$content['poblacio'];
        $this->telefon=$content['telefon'];
        $this->email=$content['email'];
        $this->codSector=$content['sector'];
        $this->web=$content['web'];
    }
}
