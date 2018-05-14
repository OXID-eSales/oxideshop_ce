<?php

namespace Helper;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\ArrayLoader;

class LanguageDirectoryReader extends ArrayLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        // not an array
        if (!is_array($resource)) {
            $resource = [$resource];
        }

        $messages = [];

        foreach ($resource as $directory) {
            if (!file_exists($directory)) {
                throw new NotFoundResourceException(sprintf('Directory "%s" not found.', $directory));
            }
            $messages = $this->loadDirectory($messages, $directory);
        }
        $catalogue = parent::load($messages, $locale, $domain);

        return $catalogue;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    protected function loadFile($file)
    {
        $aLang = [];
        require $file;
        return $aLang;
    }

    /**
     * @param array  $messages
     * @param string $directory
     *
     * @return array
     */
    protected function loadDirectory($messages, $directory)
    {
        $finder = new Finder();
        $finder->files()->in($directory)->name('*lang.php');

        foreach ($finder as $file) {
            $lang = $this->loadFile($file);
            // not an array
            if (!is_array($lang)) {
                throw new InvalidResourceException(sprintf('Unable to load file "%s".', $file));
            }

            $messages = array_merge($messages, $lang);
        }
        return $messages;
    }
}