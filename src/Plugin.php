<?php

declare(strict_types=1);

namespace Fmarangi\Oxid\CategoryFeed;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, Capable, CommandProvider
{
    public function getCapabilities()
    {
        return [CommandProvider::class => static::class];
    }

    public function getCommands()
    {
        return [new ExportCommand()];
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // Nothing to do here...
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        // Nothing to do here...
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // Nothing to do here...
    }
}
