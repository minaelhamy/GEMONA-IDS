<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class RefreshSupermarketCatalog extends Command
{
    private const MINIMUM_PYTHON_VERSION = '3.9.0';

    protected $signature = 'supermarket:refresh
        {--skip-scrape : Import the existing latest files without running the scraper}
        {--margin= : Margin percentage to pass to the importer}
        {--prices-only : For existing supermarket products, update only price, availability, source candidates, and sync metadata}';

    protected $description = 'Run the supermarket scraper, rebuild duplicate clusters, and import the catalog.';

    public function handle(): int
    {
        if (!$this->option('skip-scrape')) {
            $result = $this->runScraper();
            if ($result !== self::SUCCESS) {
                return $result;
            }
        }

        $arguments = ['--products' => config('supermarkets.products_path'), '--clusters' => config('supermarkets.clusters_path')];
        if ($this->option('margin') !== null) {
            $arguments['--margin'] = $this->option('margin');
        }
        if ($this->option('prices-only')) {
            $arguments['--prices-only'] = true;
        }

        $importResult = $this->call('supermarket:import', $arguments);
        if ($importResult !== self::SUCCESS) {
            return $importResult;
        }

        $repairArguments = ['--keep-missing-images' => true];
        if ($this->option('margin') !== null) {
            $repairArguments['--margin'] = $this->option('margin');
        }

        return $this->call('gemona:repair-supermarket-catalog', $repairArguments);
    }

    private function runScraper(): int
    {
        $python = $this->resolvePath(config('supermarkets.python'));
        $scraperRoot = $this->resolvePath(config('supermarkets.scraper_root'));
        $sources = config('supermarkets.sources', []);

        if (Str::contains($python, DIRECTORY_SEPARATOR) && !is_file($python)) {
            $this->warn("Configured Python was not found at {$python}; falling back to python3.");
            $python = 'python3';
        }

        if (!$this->pythonIsSupported($python)) {
            $this->error(sprintf(
                'The supermarket scraper requires Python %s or newer. Configure SUPERMARKET_PYTHON in .env to a newer Python binary, or run with --skip-scrape to import existing data files only.',
                self::MINIMUM_PYTHON_VERSION
            ));
            return self::FAILURE;
        }

        $crawl = array_merge(
            [$python, '-m', 'scraper.cli', 'crawl-many', '--sources'],
            $sources,
            ['--progress-every', '250']
        );

        if (!$this->runProcess($crawl, $scraperRoot)) {
            return self::FAILURE;
        }

        $clustersPath = $this->resolvePath(config('supermarkets.clusters_path'));
        $productsPath = $this->resolvePath(config('supermarkets.products_path'));

        return $this->runProcess(
            [$python, '-m', 'scraper.cli', 'dedupe', $productsPath, '--output', $clustersPath],
            $scraperRoot
        ) ? self::SUCCESS : self::FAILURE;
    }

    private function runProcess(array $command, string $cwd): bool
    {
        $this->line('$ ' . implode(' ', array_map('escapeshellarg', $command)));

        $process = new Process($command, $cwd);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return false;
        }

        return true;
    }

    private function pythonIsSupported(string $python): bool
    {
        $process = new Process([
            $python,
            '-c',
            'import sys; print(".".join(map(str, sys.version_info[:3])))',
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("Unable to run configured Python binary: {$python}");
            $this->error($process->getErrorOutput());
            return false;
        }

        $version = trim($process->getOutput());
        $this->line("Using Python {$version}: {$python}");

        return version_compare($version, self::MINIMUM_PYTHON_VERSION, '>=');
    }

    private function resolvePath(string $path): string
    {
        if (Str::startsWith($path, ['/']) || !Str::contains($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
