<?php

namespace App\Entity;

use App\Repository\CurriculumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurriculumRepository::class)]
class Curriculum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $experiencia = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idiomes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $estudis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $competencies = null;

    #[ORM\OneToOne(inversedBy: 'curriculum', cascade: ['persist', 'remove'])]
    private ?Alumne $alumne = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEstudis(): ?string
    {
        return $this->estudis;
    }

    public function setEstudis(?string $estudis): self
    {
        $this->estudis = $estudis;

        return $this;
    }

    public function getCompetencies(): ?string
    {
        return $this->competencies;
    }

    public function setCompetencies(?string $competencies): self
    {
        $this->competencies = $competencies;

        return $this;
    }

    public function getAlumne(): ?Alumne
    {
        return $this->alumne;
    }

    public function setAlumne(?Alumne $alumne): self
    {
        $this->alumne = $alumne;

        return $this;
    }

    public function fromJSON($content): void {
        $content=json_decode($content,true);
        $this->experiencia=$content["experiencia"]|"";
        $this->estudis=$content['estudis']|"";
        $this->competencies=$content['competencies']|"";
        $this->idiomes=$content['idiomes']|"";
    }
}
