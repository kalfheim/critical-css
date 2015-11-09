<?php

namespace Krisawzm\CriticalCss\Console;

use Artisan;

class CriticalCssMake extends CriticalCssCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'criticalcss:make';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Generate critical-path CSS';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        parent::handle();

        $cssGenerator = $this->laravel->make('criticalcss.cssgenerator');

        foreach ($this->getUris() as $key => $uri) {
            $this->info(sprintf('Processing URI [%s]', $uri));

            $cssGenerator->generate($uri, $this->getUriAlias($key));
        }

        $this->clearViews();
    }

    /**
     * Returns the alias for a URI, if there is any. If not, returns null.
     *
     * @param  string|int $key The key for the given array item.
     *
     * @return string|null
     */
    protected function getUriAlias($key)
    {
        // If the key is a string, assume it's specified by the user, and
        // therefore is an alias.
        if (is_string($key)) {
            return $key;
        }

        // If not, return null.
        return null;
    }

    /**
     * Returns a list of URIs to generate critical-path CSS for.
     *
     * @return array
     */
    protected function getUris()
    {
        $uris = $this->laravel['config']->get('criticalcss.routes');

        // If null, return all 'GET' routes.
        if (is_null($uris)) {
            $uris = [];
            $router = $this->laravel['router'];

            foreach ($router->getRoutes() as $route) {
                if ($route->getMethods()[0] === 'GET') {
                    $uris[] = $route->getUri();
                }
            }
        }

        return $uris;
    }

    /**
     * Clear compiled views.
     *
     * @return void
     */
    protected function clearViews()
    {
        $this->info('Clearing compiled views');

        Artisan::call('view:clear');
    }
}
