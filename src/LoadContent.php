<?php


namespace Lasota\LaravelContent;


use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class LoadContent
{
    public $directory = 'content';
    public $extension = 'php';

    public function setDirectory(string $directory = 'content'): void
    {
        $this->directory = $directory;
    }

    public function loadContent(Application $app, RepositoryContract $repository)
    {
        $this->loadContentFiles($app, $repository);
    }

    public function contentPath($path = '')
    {
        $resourcePath = app()->resourcePath();
        return $resourcePath.DIRECTORY_SEPARATOR.$this->directory.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    protected function loadContentFiles(Application $app, RepositoryContract $repository)
    {
        $files = $this->getContentFiles($app);

        foreach ($files as $key => $path) {
            if( $this->getFileExtension($path) == 'md' ) {
                $repository->set($key, $this->parseMarkdown($path));
                continue;
            }
            $repository->set($key, require $path);
        }
    }

    protected function getContentFiles(Application $app)
    {
        $files = [];

        $contentPath = realpath($this->contentPath());

        foreach (Finder::create()->files()->name('*.*')->in($contentPath) as $file) {
            $directory = $this->getNestedDirectory($file, $contentPath);
            dump($file->getRealPath());

            $files[$directory.basename($file->getRealPath(), '.'.$this->getFileExtension($file->getRealPath()))] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $contentPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $contentPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($contentPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }

    protected function parseMarkdown($path)
    {
        return (new \ParsedownExtra())->text(File::get($path));
    }

    protected function getFileExtension($path)
    {
        $path_parts = pathinfo($path);

        return $path_parts['extension'];
    }
}
