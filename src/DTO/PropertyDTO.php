<?php

namespace App\DTO;

class PropertyDTO
{
    private $id;
    private $name;
    private $type;
    private $price;
    private $bath;
    private $room;
    private $mesure;
    private $detail;
    private $location;

    public function __construct(
        int $id,
        string $name,
        string $type,
        int $price,
        int $bath,
        int $room,
        int $mesure,
        ?string $detail,
        string $location
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->bath = $bath;
        $this->room = $room;
        $this->mesure = $mesure;
        $this->detail = $detail;
        $this->location = $location;
    }

    // Define getters for all properties
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getBath(): int
    {
        return $this->bath;
    }

    public function getRoom(): int
    {
        return $this->room;
    }

    public function getMesure(): int
    {
        return $this->mesure;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}