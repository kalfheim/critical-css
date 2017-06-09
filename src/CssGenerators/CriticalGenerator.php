<?php

namespace Alfheim\CriticalCss\CssGenerators;

use Symfony\Component\Process\ProcessBuilder;
use Alfheim\CriticalCss\Storage\StorageInterface;
use Alfheim\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

/**
 * Generates critical-path CSS using the Critical npm package.
 *
 * @see https://github.com/addyosmani/critical
 */
class CriticalGenerator implements CssGeneratorInterface
{
    /** @var array */
    protected $css;

    /** @var \Alfheim\CriticalCss\HtmlFetchers\HtmlFetcherInterface */
    protected $htmlFetcher;

    /** @var \Alfheim\CriticalCss\Storage\StorageInterface */
    protected $storage;

    /** @var string */
    protected $criticalBin = 'critical';

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var array */
    protected $ignore;

    /** @var int|null */
    protected $timeout;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $css,
                                HtmlFetcherInterface $htmlFetcher,
                                StorageInterface $storage)
    {
        $this->css         = $css;
        $this->htmlFetcher = $htmlFetcher;
        $this->storage     = $storage;
    }

    /**
     * Set the path to the Critical bin (executable.)
     *
     * @param  string $critical
     *
     * @return void
     */
    public function setCriticalBin($critical)
    {
        $this->criticalBin = $critical;
    }

    /**
     * Set optional options for Critical.
     *
     * @param  int      $width
     * @param  int      $height
     * @param  array    $ignore
     * @param  int|null $timeout
     *
     * @return void
     */
    public function setOptions($width = 900, $height = 1300, array $ignore = [], $timeout = null)
    {
        $this->width  = $width;
        $this->height = $height;
        $this->ignore = $ignore;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($uri, $alias = null, $routeCss = '')
    {
        $html = $this->htmlFetcher->fetch($uri);

        $builder = new ProcessBuilder;

        $builder->setPrefix($this->criticalBin);

        $builder->setArguments([
            '--base='.realpath(__DIR__.'/../.tmp'),
            '--width='.$this->width,
            '--height='.$this->height,
            '--minify',
        ]);

        if (!is_null($this->timeout)) {
            $builder->setTimeout($this->timeout);

            $builder->add('--timeout='.$this->timeout);
        }

        if ($routeCss === '') {
            foreach ($this->css as $css) {
                $builder->add('--css=' . $css);
            }
        } else {
            $builder->add('--css=' . $routeCss);
        }

        foreach ($this->ignore as $ignore) {
            $builder->add('--ignore='.$ignore);
        }

        $builder->setInput($html);

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            $message = 'Error processing URI [%s]. This is probably caused by the Critical npm package. Checklist: ' . PHP_EOL .
                '1) `critical_bin` is correct' . PHP_EOL .
                '2) `css` paths are correct 3) run `npm`' . PHP_EOL .
                '3) run `npm` install` again.' . PHP_EOL .
                ' uri: ' . $uri . PHP_EOL .
                ' Error output: ' . $process->getErrorOutput();
            throw new CssGeneratorException($message, $process->getExitCode());
        }
        return $this->storage->writeCss(
            is_null($alias) ? $uri : $alias,
            $process->getOutput()
        );
    }
}
