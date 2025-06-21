<?php

namespace Src\ContentGeneration\ReviewSchedule\Domain\Entities;

class Vehicle
{
    private string $make;
    private string $model;
    private int $year;
    private string $tireSize;
    private string $category;
    private ?string $recommendedOil;
    private array $pressureData;

    public function __construct(
        string $make,
        string $model,
        int $year,
        string $tireSize,
        string $category = '',
        ?string $recommendedOil = null,
        array $pressureData = []
    ) {
        $this->make = $make;
        $this->model = $model;
        $this->year = $year;
        $this->tireSize = $tireSize;
        $this->category = $category;
        $this->recommendedOil = $recommendedOil;
        $this->pressureData = $pressureData;
    }

    public function getMake(): string
    {
        return $this->make;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getTireSize(): string
    {
        return $this->tireSize;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getRecommendedOil(): ?string
    {
        return $this->recommendedOil;
    }

    public function getPressureData(): array
    {
        return $this->pressureData;
    }

    public function getFullName(): string
    {
        return "{$this->make} {$this->model} {$this->year}";
    }

    public function isMotorcycle(): bool
    {
        return strpos($this->tireSize, '/') !== false &&
            (strpos($this->tireSize, 'dianteiro') !== false ||
                strpos($this->tireSize, 'traseiro') !== false);
    }

    public function isElectric(): bool
    {
        return strpos(strtolower($this->category), 'electric') !== false;
    }

    public function isHybrid(): bool
    {
        return strpos(strtolower($this->category), 'hybrid') !== false;
    }

    public function getVehicleType(): string
    {
        if ($this->isElectric()) {
            return 'electric';
        }

        if ($this->isHybrid()) {
            return 'hybrid';
        }

        if ($this->isMotorcycle()) {
            return 'motorcycle';
        }

        return 'car';
    }
}
