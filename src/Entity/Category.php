<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CourseDetail::class)]
    private $coures;

    public function __construct()
    {
        $this->coures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|CourseDetail[]
     */
    public function getCoures(): Collection
    {
        return $this->coures;
    }

    public function addCoure(CourseDetail $coure): self
    {
        if (!$this->coures->contains($coure)) {
            $this->coures[] = $coure;
            $coure->setCategory($this);
        }

        return $this;
    }

    public function removeCoure(CourseDetail $coure): self
    {
        if ($this->coures->removeElement($coure)) {
            // set the owning side to null (unless already changed)
            if ($coure->getCategory() === $this) {
                $coure->setCategory(null);
            }
        }

        return $this;
    }
}
