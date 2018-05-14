<?php

namespace Helper;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

class Translator
{
    /**
     * @var array
     */
    private $sfTranslator;

    /**
     * Constructor.
     *
     * @param array $paths
     */
    public function __construct($paths)
    {
        $this->sfTranslator = new SymfonyTranslator('en');

        $this->sfTranslator->addLoader('oxphp', new LanguageDirectoryReader());

        $languageDir = $this->getLanguageDirectories($paths, 'de');

        $this->sfTranslator->addResource('oxphp', $languageDir, 'de');

        $languageDir = $this->getLanguageDirectories($paths, 'en');

        $this->sfTranslator->addResource('oxphp', $languageDir, 'en');
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->sfTranslator->trans($string);
    }

    /**
     * Returns language map array
     *
     * @param array  $paths
     * @param string $language Language index
     *
     * @return array
     */
    private function getLanguageDirectories($paths, $language)
    {
        $languageDirectories = [];

        foreach ($paths as $path) {
            $languageDirectories[] = $path . $language;
        }

        return $languageDirectories;
    }

}