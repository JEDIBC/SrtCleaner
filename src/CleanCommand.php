<?php
namespace SrtCleaner;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanCommand
 *
 * @package SrtCleaner
 */
class CleanCommand extends Command
{
    /**
     * @var Cleaner
     */
    protected $cleaner;

    /**
     * CleanCommand constructor.
     *
     * @param Cleaner $cleaner
     */
    public function __construct(Cleaner $cleaner)
    {
        parent::__construct();

        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Clean orphaned srt files')
            ->addArgument('path', InputArgument::REQUIRED, 'Path of the videos.')
            ->setHelp("This command allows you to create users...");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cleaner->cleanDir($input->getArgument('path'));
    }
}
