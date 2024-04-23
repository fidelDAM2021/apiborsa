<?php

namespace App\Entity;

use App\Repository\ContacteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContacteRepository::class)]
class Contacte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:"El nom del contacte és obligatori")]
    private ?string $nomcontacte = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $carrec = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"El telèfon del contacte és obligatori")]
    private ?string $telefon = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message:"El correu electrònic del contacte és obligatori")]
    #[Assert\Email(message:"El correu electrònic no és vàlid")]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'contactes')]
    #[ORM\JoinColumn(referencedColumnName:'nif',nullable: false)]
    private ?Empresa $NIFempresa = null;

    private ?String $nif;

    public function getNif(): ?String {
        return $this->nif;
    }
    public function setNif(String $nif) {
        $this->nif=$nif;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomcontacte(): ?string
    {
        return $this->nomcontacte;
    }

    public function setNomcontacte(string $nomcontacte): self
    {
        $this->nomcontacte = $nomcontacte;

        return $this;
    }

    public function getCarrec(): ?string
    {
        return $this->carrec;
    }

    public function setCarrec(?string $carrec): self
    {
        $this->carrec = $carrec;

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

    public function getNIFempresa(): ?Empresa
    {
        return $this->NIFempresa;
    }

    public function setNIFempresa(?Empresa $NIFempresa): self
    {
        $this->NIFempresa = $NIFempresa;

        return $this;
    }
    public function getValues(): ?array {
        return get_object_vars($this);
    }
    public function toArray(): array {
        $empresaArray=[
            'id'=>$this->id,
            'nomcontacte'=>$this->nomcontacte,
            'carrec'=>$this->carrec,
            'telefon'=>$this->telefon,
            'email'=>$this->email,
            'empresa'=>[
                'nifempresa'=>$this->NIFempresa->getNIF(),
                'nomempresa'=>$this->NIFempresa->getNom()
            ],
        ];
        return $empresaArray;
    }
    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->nomcontacte=$content["nomcontacte"];
        $this->carrec=$content['carrec'];
        $this->telefon=$content['telefon'];
        $this->email=$content['email'];
        $this->nif=$content['nifempresa'];
    }
}
