<?php

namespace App\Entity;

use App\Repository\OrderProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderProductsRepository::class)
 */
class OrderProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="orderProducts")
     */
    private $Products;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderProducts")
     */
    private $theorder;

    /**
     * @ORM\Column(type="integer")
     */
    private $Quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducts(): ?Products
    {
        return $this->Products;
    }

    public function setProducts(?Products $Products): self
    {
        $this->Products = $Products;

        return $this;
    }

    public function getTheorder(): ?Order
    {
        return $this->theorder;
    }

    public function setTheorder(?Order $theorder): self
    {
        $this->theorder = $theorder;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): self
    {
        $this->Quantity = $Quantity;

        return $this;
    }
}
