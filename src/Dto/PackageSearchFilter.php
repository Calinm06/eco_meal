<?php

namespace App\Dto;

use App\Entity\Business;
use App\Entity\BusinessType;
use App\Entity\Category;

class PackageSearchFilter
{
    private? string $name = null;
    private? int $minPrice = null;

    private? int $maxPrice = null;

    private ?Category $category = null;
    private ?BusinessType $businessType = null;
    private ?Business $business = null;
    private ?string $city = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function setMinPrice(?int $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?int $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getBusinessType(): ?BusinessType
    {
        return $this->businessType;
    }

    public function setBusinessType(?BusinessType $businessType): void
    {
        $this->businessType = $businessType;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): void
    {
        $this->business = $business;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

}
