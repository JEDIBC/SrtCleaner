<?php
namespace SrtCleaner;

/**
 * Class Cleaner
 *
 * @package SrtCleaner
 */
class Cleaner
{
    /**
     * @var FinderFactory
     */
    protected $finderFactory;

    /**
     * CleanCommand constructor.
     */
    public function __construct(FinderFactory $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param string $path
     */
    public function cleanDir($path)
    {
        $this->handleDir($path);
    }

    /**
     * @param string $path
     */
    protected function handleDir($path)
    {
        // First recursively handle sub directories
        $directories = $this->finderFactory->createFinder($path)->directories();
        if ($directories->count() > 0) {
            foreach ($directories as $directory) {
                $this->handleDir($directory->getRealPath());
            }
        }

        // Second search current directory
        $subtitles = $this->finderFactory->createFinder($path)->files()->name("*.srt");
        if ($subtitles->count() > 0) {
            $regularFiles = $this->finderFactory->createFinder($path)->files()->notName("*.srt");

            foreach ($subtitles as $subtitle) {
                echo "SRT " . $subtitle->getFilename() . PHP_EOL;
            }
        }
    }
}