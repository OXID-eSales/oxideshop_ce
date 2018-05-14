<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class TranslationsModule extends \Codeception\Module
{
    /**
     * @var array
     */
    private $paths = ['Application/translations'];

    /**
     * @var array
     */
    protected $config = [
        'paths' => null,
    ];

    /**
     * @var array
     */
    protected $requiredFields = ['shop_path'];

    /**
     * @var Translator
     */
    private $translator;

    public function _initialize()
    {
        parent::_initialize();

        $this->translator = new Translator($this->getLanguageDirectoryPaths());
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string)
    {
        return $this->translator->translate($string);
    }

    private function getLanguageDirectoryPaths()
    {
        $fullPaths = [];
        if ($this->config['paths']) {
            $customPaths = explode(',', $this->config['paths']);
            $this->paths = array_merge($this->paths, $customPaths);
        }
        foreach ($this->paths as $path) {
            $fullPaths[] = $this->config['shop_path'].'/'.trim($path, '/').'/';
        }
        return $fullPaths;
    }
}
