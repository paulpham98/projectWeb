<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseClass::class, orphanRemoval: true)]
    private $class;

    public function __construct()
    {
        $this->class = new ArrayCollection();
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

    public function setImage($image)
    {
        if ($image != null) {
            $this->image = $image;
        }
        return $this;
    }

    /**
     * @return Collection|CourseClass[]
     */
    public function getClass(): Collection
    {
        return $this->class;
    }

    public function addClass(CourseClass $class): self
    {
        if (!$this->class->contains($class)) {
            $this->class[] = $class;
            $class->setCourse($this);
        }

        return $this;
    }

    public function removeClass(CourseClass $class): self
    {
        if ($this->class->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getCourse() === $this) {
                $class->setCourse(null);
            }
        }

        return $this;
    }
}
