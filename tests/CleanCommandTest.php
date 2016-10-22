<?php
namespace Tests;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use SrtCleaner\CleanCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CleanCommandTest
 */
class CleanCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testCommandWithoutLang()
    {
        $path = $this->getVirtualPath();

        $application = new SymfonyApplication;
        $application->add(new CleanCommand());

        $command       = $application->find('clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'path'    => $path
            ]
        );

        $this->assertFileExists(sprintf('%s/dir1/subdir1/file1.avi', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file1.srt', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file2', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file2.srt', $path));
        $this->assertFileNotExists(sprintf('%s/dir2/lonely.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.mkv', $path));
        $this->assertFileNotExists(sprintf('%s/dir3/file3.fr.srt', $path));
        $this->assertFileNotExists(sprintf('%s/dir3/file3.en.srt', $path));
        $this->assertFileNotExists(sprintf('%s/dir3/file3.de.srt', $path));
        $this->assertFileExists(sprintf('%s/rootFile.mov', $path));
        $this->assertFileExists(sprintf('%s/rootFile.srt', $path));
        $this->assertFileNotExists(sprintf('%s/rootFile.fr.srt', $path));
    }

    /**
     * @test
     */
    public function testCommandWithLang()
    {
        $path = $this->getVirtualPath();

        $application = new SymfonyApplication;
        $application->add(new CleanCommand());

        $command       = $application->find('clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'path'    => $path,
                '--lang'  => 'fr,en'
            ]
        );

        $this->assertFileExists(sprintf('%s/dir1/subdir1/file1.avi', $path));
        $this->assertFileNotExists(sprintf('%s/dir1/subdir1/file1.srt', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file2', $path));
        $this->assertFileNotExists(sprintf('%s/dir1/subdir1/file2.srt', $path));
        $this->assertFileNotExists(sprintf('%s/dir2/lonely.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.mkv', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.fr.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.en.srt', $path));
        $this->assertFileNotExists(sprintf('%s/dir3/file3.de.srt', $path));
        $this->assertFileExists(sprintf('%s/rootFile.mov', $path));
        $this->assertFileNotExists(sprintf('%s/rootFile.srt', $path));
        $this->assertFileExists(sprintf('%s/rootFile.fr.srt', $path));
    }

    /**
     * @test
     */
    public function testCommandWithDryRun()
    {
        $path = $this->getVirtualPath();

        $application = new SymfonyApplication;
        $application->add(new CleanCommand());

        $command       = $application->find('clean');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command'   => $command->getName(),
                'path'      => $path,
                '--dry-run' => null
            ]
        );

        $this->assertFileExists(sprintf('%s/dir1/subdir1/file1.avi', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file1.srt', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file2', $path));
        $this->assertFileExists(sprintf('%s/dir1/subdir1/file2.srt', $path));
        $this->assertFileExists(sprintf('%s/dir2/lonely.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.mkv', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.fr.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.en.srt', $path));
        $this->assertFileExists(sprintf('%s/dir3/file3.de.srt', $path));
        $this->assertFileExists(sprintf('%s/rootFile.mov', $path));
        $this->assertFileExists(sprintf('%s/rootFile.srt', $path));
        $this->assertFileExists(sprintf('%s/rootFile.fr.srt', $path));
    }

    /**
     * @return string
     */
    protected function getVirtualPath()
    {
        vfsStream::setup();
        vfsStream::create(
            [
                'dir1'            => [
                    'subdir1' => [
                        'file1.avi' => '',
                        'file1.srt' => '',
                        'file2'     => '',
                        'file2.srt' => ''
                    ],
                    'subdir2' => [],
                ],
                'dir2'            => [
                    'lonely.srt' => ''
                ],
                'dir3'            => [
                    'file3.mkv'    => '',
                    'file3.fr.srt' => '',
                    'file3.en.srt' => '',
                    'file3.de.srt' => '',
                ],
                'rootFile.mov'    => '',
                'rootFile.srt'    => '',
                'rootFile.fr.srt' => ''
            ]
        );

        return vfsStream::url('root');
    }
}
