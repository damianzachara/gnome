<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\GnomeRepository")
 */
class Gnome
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\Range(
     *      min = 0,
     *      max = 100,
     *      minMessage = "Strength should be range 0-100",
     *      maxMessage = "Strength should be range 0-100"
     * )
     * @ORM\Column(type="integer")
     */
    private $strength;

    /**
     * @Assert\Range(
     *      min = 0,
     *      max = 100,
     *      minMessage = "Age should be range 0-100",
     *      maxMessage = "Age should be range 0-100"
     * )
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @return mixed
     */
    public function getImage(): ?String
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(?string $image)
    {
        $this->image = $image;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Gnome
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStrength(): ?int
    {
        return $this->strength;
    }

    /**
     * @param int $strength
     * @return Gnome
     */
    public function setStrength(int $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return Gnome
     */
    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }
}
