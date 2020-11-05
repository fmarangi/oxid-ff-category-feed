<?php

declare(strict_types=1);

namespace Fmarangi\Oxid\CategoryFeed;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\CategoryList;

class CategoryData implements \IteratorAggregate
{
    public function getIterator()
    {
        $categories = oxNew(CategoryList::class);
        $categories->setAdminMode(false);
        $categories->setLoadLevel(2);
        $categories->load();
        $categories->setAdminMode(true);
        yield from array_map([$this, 'getRowData'], $categories->getArray());
    }

    private function getRowData(Category $category): array
    {
        return [
            $this->title($category),
            $this->title($category->getParentCategory()),
            'CategoryPath',
            $category->getLink(),
        ];
    }

    private function title(?Category $category): string
    {
        return $category ? $category->oxcategories__oxtitle->rawValue : '';
    }
}
