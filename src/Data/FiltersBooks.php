<?php

namespace App\Data;


use App\Entity\Categories;

class FiltersBooks
{
    public Categories $category;

    /**
     * @return Categories
     */
    public function getCategory(): Categories
    {
        return $this->category;
    }

    /**
     * @param Categories $category
     */
    public function setCategory(Categories $category): void
    {
        $this->category = $category;
    }





}