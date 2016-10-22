<?php
namespace SrtCleaner;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class CleanCommand
 *
 * @package SrtCleaner
 */
class CleanCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Clean orphaned srt files')
            ->addArgument('path', InputArgument::REQUIRED, 'Path of the videos.')
            ->addOption('lang', null, InputOption::VALUE_OPTIONAL, 'Lang extension of srt files.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE)
            ->setHelp("This command allows you to create users...");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Retrieve parameters
        $path   = $input->getArgument('path');
        $langs  = mb_convert_case($input->getOption('lang'), MB_CASE_LOWER);
        $langs  = empty($langs) ? [] : explode(',', $langs);
        $dryRun = (bool) $input->getOption('dry-run');

        // Create display style
        $style = $this->createStyle($input, $output);

        // Print title
        $title = sprintf('Cleaning orphaned srt(s) in directory %s', $path);
        if (!empty($langs)) {
            $title .= sprintf(' for lang(s) %s', implode(', ', $langs));
        }
        $style->title($title);

        // Cleaning subtitle(s)
        $toDeleteSubtitles = $this->handleDir($path, $langs);
        foreach ($toDeleteSubtitles as $subtitle) {
            $style->note(sprintf('Deleting %s', $subtitle));
            if (!$dryRun) {
                unlink($subtitle);
            }
        }
        $style->success(empty($toDeleteSubtitles) ? 'No subtitle to delete' : sprintf('%d subtitle(s) deleted successfully', count($toDeleteSubtitles)));
    }

    /**
     * @param string $path
     * @param array  $langs
     *
     * @return array
     */
    protected function handleDir($path, array $langs)
    {
        return array_merge(
            $this->handleSubDirs($path, $langs),
            $this->searchDir($path, $langs)
        );
    }

    /**
     * @param string $path
     * @param array  $langs
     *
     * @return array
     */
    protected function handleSubDirs($path, array $langs)
    {
        // First recursively handle sub directories
        $directories = $this->createFinder($path)->directories();
        if ($directories->count() == 0) {
            return [];
        }

        return array_reduce(
            iterator_to_array($directories->getIterator()),
            function (array $carry, SplFileInfo $directory) use ($langs) {
                return array_merge(
                    $carry,
                    $this->handleDir($directory->getPathname(), $langs)
                );
            },
            []
        );
    }

    /**
     * @param       $path
     * @param array $langs
     *
     * @return array
     */
    protected function searchDir($path, array $langs)
    {
        // Search subtitles in $path
        $subtitles = $this->createFinder($path)->files()->name("*.srt");
        if ($subtitles->count() == 0) {
            return [];
        }

        // Searching non srt files and generate allowed subtitles list
        $regularFiles = $this->createFinder($path)->files()->notName("*.srt");
        $allowedList  = [];
        foreach ($regularFiles as $regularFile) {
            $extension = $regularFile->getExtension();
            $baseName  = $regularFile->getBasename(empty($extension) ? null : sprintf('.%s', $extension));

            if (empty($langs)) {
                $allowedList[] = sprintf('%s.srt', $baseName);
            } else {
                foreach ($langs as $lang) {
                    $allowedList[] = sprintf('%s.%s.srt', $baseName, $lang);
                }
            }
        }

        // Return all non wanted subtitles
        return array_reduce(
            iterator_to_array($subtitles->getIterator()),
            function (array $carry, \SplFileInfo $subtitle) use ($allowedList) {
                if (!in_array($subtitle->getBasename(), $allowedList)) {
                    $carry[] = $subtitle->getPathname();
                }

                return $carry;
            },
            []
        );
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    protected function createFinder($path)
    {
        $finder = new Finder();

        return $finder->in($path)->depth("< 1");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return SymfonyStyle
     */
    protected function createStyle(InputInterface $input, OutputInterface $output)
    {
        return new SymfonyStyle($input, $output);
    }
}
