#!/usr/bin/env php
<?php declare(strict_types=1);

$options = getopt('s:');
$shopId  = $options['s'] ?? 0;
if (!$shopId) {
    throw new RuntimeException('Please specify the shop ID using the "s" parameter!');
}

require_once __DIR__ . '/../../../source/bootstrap.php';

error_reporting(-1);
ini_set('display_errors', 'On');

define('OX_IS_ADMIN', true);

use Omikron\FactFinder\Oxid\Model\Api\ClientFactory;
use Omikron\FactFinder\Oxid\Model\Api\PushImport;
use Omikron\FactFinder\Oxid\Model\Config\FtpParams;
use Omikron\FactFinder\Oxid\Model\Export\FtpClient;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\CategoryList;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;

/**
 * @param int $shopId
 *
 * @return Category[]
 */
function getCategories(): iterable
{
    $categories = oxNew(CategoryList::class);
    $categories->setAdminMode(false);
    $categories->setLoadLevel(2);
    $categories->load();
    $categories->setAdminMode(true);
    return $categories->getArray();
}

function title(?Category $category): string
{
    return $category ? $category->oxcategories__oxtitle->rawValue : '';
}

try {
    Registry::getConfig()->setShopId($shopId);
    Registry::set(Config::class, null);

    $ftpUploader = new FtpClient(new FtpParams());
    $pushImport  = new PushImport(new ClientFactory());

    $handle = fopen('php://temp', 'w+');
    fputcsv($handle, ['Name', 'ParentCategory', 'URL'], ';');
    foreach (getCategories() as $category) {
        fputcsv($handle, [
            title($category),
            title($category->getParentCategory()),
            $category->getLink(),
        ], ';');
    }

    rewind($handle);
    $ftpUploader->upload($handle, sprintf('categories_%d.csv', $shopId));
    $pushImport->execute();
} finally {
    fclose($handle);
}
