<?php

declare(strict_types=1);

namespace Fmarangi\Oxid\CategoryFeed;

use Composer\Command\BaseCommand;
use Omikron\FactFinder\Oxid\Model\Config\FtpParams;
use Omikron\FactFinder\Oxid\Model\Export\FtpClient;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('factfinder:export:categories');
        $this->setDescription('Generate and upload the FACT-Finder category feed');
        $this->addArgument('shop', InputArgument::REQUIRED, 'Shop ID');
        $this->addOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Language ID', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bootstrapOxid((int) $input->getArgument('shop'), (int) $input->getOption('lang'));

        $output->writeln($this->getChannel());

        $feed = tmpfile();
        array_map($this->feedRow($feed), iterator_to_array(new CategoryData()));

        $ftpUploader = oxNew(FtpClient::class, oxNew(FtpParams::class));
        $ftpUploader->upload($feed, sprintf('categories.%s.csv', $this->getChannel()));
    }

    private function getChannel(): string
    {
        $languageCode = Registry::getLang()->getLanguageAbbr();
        return Registry::getConfig()->getConfigParam('ffChannel')[$languageCode];
    }

    private function feedRow($handle): callable
    {
        fputcsv($handle, ['Name', 'parentCategory', 'sourceField', 'URL'], ';');
        return function (array $data) use ($handle): void {
            fputcsv($handle, $data, ';');
        };
    }

    private function bootstrapOxid(int $shopId, int $languageId): void
    {
        $vendorDir = $this->getComposer()->getConfig()->get('vendor-dir');
        require_once $vendorDir . '/../source/bootstrap.php';

        Registry::getConfig()->setShopId($shopId);
        Registry::set(Config::class, null);
        Registry::getLang()->setBaseLanguage($languageId);
    }
}
