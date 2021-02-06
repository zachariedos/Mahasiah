<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $populator = new \Faker\ORM\Propel\Populator($faker);
        for ($count = 0; $count < 11; $count++) {
            $cat= new Categories();
                if($count == 0){
                    $cat->setName('Table');
                }
                elseif($count == 1){
                    $cat->setName('Miroire');
                }
                elseif($count == 2){
                    $cat->setName('Bougie');
                }
                elseif($count == 3){
                    $cat->setName('Lampe');
                }
                elseif($count == 4){
                    $cat->setName('Canapé');
                }
                elseif($count == 5){
                    $cat->setName('Sculpture');
                }
                elseif($count == 6){
                    $cat->setName('Coussin');
                }
                elseif($count == 7){
                    $cat->setName('Verre');
                }
                elseif($count == 8){
                    $cat->setName('Vase');
                }
                elseif($count == 9){
                    $cat->setName('Abat-jour');
                }
                elseif($count == 10){
                    $cat->setName('Fauteuil');
                }
                $manager->persist($cat);
                $manager->flush();
                $this->categories[] = $cat;
        }
        for ($count = 0; $count < 20; $count++) {
            $product = new Products();
            $title = $faker->sentence(1);
            $product->setTitle($title);
            $desc = $faker->sentence(25);
            $product->setDescription($desc);
            $product->setPrice(rand(1, 50000));
            $product->setQuantity(rand(1, 50));
            $product->setSize("Taille " . $count);
            $mater = $faker->sentence(1);
            $product->setMaterials($mater);
            for ($nbcat = 0; $nbcat < rand(0, 3); $nbcat++) {
                $product->addCategory($this->categories[rand(0, count($this->categories)-1)]);
            }
            $manager->persist($product);
            $manager->flush();
        }
    }
}
