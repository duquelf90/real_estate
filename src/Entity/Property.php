<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\PropertyRepository;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */

class Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="properties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $bath;

    /**
     * @ORM\Column(type="integer")
     */
    private $park;

    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $room;

    /**
     * @ORM\Column(type="integer")
     */
    private $mesure;

    /**
     * @ORM\Column(type="integer")
     */
    private $build;

    

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="property", cascade={"persist", "remove"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Photo $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProperty($this);
        }

        return $this;
    }

    public function removeImage(Photo $image): void
    {
        $this->images->removeElement($image);
        $image->setProperty(null);
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBath(): ?int
    {
        return $this->bath;
    }

    public function setBath(int $bath): self
    {
        $this->bath = $bath;

        return $this;
    }

    public function getRoom(): ?int
    {
        return $this->room;
    }

    public function setRoom(int $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getMesure(): ?int
    {
        return $this->mesure;
    }

    public function setMesure(int $mesure): self
    {
        $this->mesure = $mesure;

        return $this;
    }    

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */ 
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of city
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */ 
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of build
     */ 
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set the value of build
     *
     * @return  self
     */ 
    public function setBuild($build)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get the value of park
     */ 
    public function getPark()
    {
        return $this->park;
    }

    /**
     * Set the value of park
     *
     * @return  self
     */ 
    public function setPark($park)
    {
        $this->park = $park;

        return $this;
    }
}
