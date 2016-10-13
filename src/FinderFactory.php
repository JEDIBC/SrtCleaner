<?php
namespace SrtCleaner;

use Symfony\Component\Finder\Finder;

/**
 * Class FinderFactory
 *
 * @package SrtCleaner
 */
class FinderFactory
{
    /**
     * @param string $path
     *
     * @return Finder
     */
    public function createFinder($path)
    {
        $finder = new Finder();

        return $finder->in($path)->depth("< 2");
    }
}