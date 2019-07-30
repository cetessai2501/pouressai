<?php
namespace App\Blog\Table;
use App\Database\Table;
use App\Blog\CategoryEntity;


class CategoriesTable extends Table
{
    const TABLE = 'categories';

    public const ENTITY = CategoryEntity::class;

  
    public function findBySlug($slug)
    {
        return $this->getDatabase()->fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    










}
