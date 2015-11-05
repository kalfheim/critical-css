<?php

namespace Krisawzm\CriticalCss\CssGenerators;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Krisawzm\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

/**
 * Generates critical-path CSS using the Critical tool.
 *
 * @see https://github.com/addyosmani/critical
 */
class CriticalGenerator implements CssGeneratorInterface
{
    /** @var array */
    protected $css;

    /** @var \Krisawzm\CriticalCss\HtmlFetchers\HtmlFetcherInterface */
    protected $htmlFetcher;

    /** @var string */
    protected $criticalBin = 'critical';

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var array */
    protected $ignore;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $css, HtmlFetcherInterface $htmlFetcher)
    {
        $this->css         = $css;
        $this->htmlFetcher = $htmlFetcher;
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
     * @param  int   $width
     * @param  int   $height
     * @param  array $ignore
     *
     * @return void
     */
    public function setOptions($width = 900, $height = 1300, array $ignore = [])
    {
        $this->width  = $width;
        $this->height = $height;
        $this->ignore = $ignore;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($uri)
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

        foreach ($this->css as $css) {
            $builder->add('--css='.$css);
        }

        foreach ($this->ignore as $ignore) {
            $builder->add('--ignore='.$ignore);
        }

        $builder->setInput($html);

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CssGeneratorException(
                sprintf('Error processing URI [%s].', $uri)
            );
        }

        return $process->getOutput();
    }
}
