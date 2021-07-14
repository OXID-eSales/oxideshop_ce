<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core\Smarty\Plugin;

use OxidEsales\EshopCommunity\Core\Registry;

/** @deprecated class will be removed in Oxid eShop v7.0 */
final class StringInputParser
{
    private const ALLOWED_LANGUAGE_CONSTRUCTS = ['array', 'null', 'false', 'true'];

    /** @var array|string[] */
    private $unsafeCharacters;
    /** @var array|string[] */
    private $allowedLanguageConstruct;

    public function __construct()
    {
        $this->defineUnsafeCharacters();
        $this->defineAllowedLanguageConstructs();
    }

    /**
     * Interprets string input as an array() construct
     * @param string $input
     * @return array
     */
    public function parseArray(string $input): array
    {
        $result = [];
        try {
            $encodedString = $this->encodeString($input);
            $stringSafeToEval = $this->restoreAllowedLanguageConstructs($encodedString);

            $array = eval("return $stringSafeToEval;");

            $result = $this->restoreEncodedArray($array);
        } catch (\Throwable $exception) {
            Registry::getLogger()->error(
                "String `$input` can't be interpreted as array.\n" . $exception
            );
        }
        return $result;
    }

    /**
     * Interprets string input as a range() construct
     * @param string $input
     * @return array
     */
    public function parseRange(string $input): array
    {
        $result = [];
        try {
            $stringWithRangeParams = preg_replace(['/^(range\()/i', '/(\))$/i'], '', $input);
            $params = explode(',', $stringWithRangeParams);
            $result = range(
                $this->cleanString($params[0]),
                $this->cleanString($params[1]),
                !empty($params[2]) ? (int)$this->cleanString($params[2]) : 1
            );
        } catch (\Throwable $exception) {
            Registry::getLogger()->error(
                "String `$input` can't be interpreted as range.\n" . $exception->getTraceAsString()
            );
        }
        return $result;
    }

    private function defineAllowedLanguageConstructs(): void
    {
        $this->allowedLanguageConstruct = array_merge(
            self::ALLOWED_LANGUAGE_CONSTRUCTS,
            array_map('strtoupper', self::ALLOWED_LANGUAGE_CONSTRUCTS),
            array_map('ucfirst', self::ALLOWED_LANGUAGE_CONSTRUCTS)
        );
    }

    private function defineUnsafeCharacters(): void
    {
        $this->unsafeCharacters = array_flip(
            array_merge(
                range('a', 'z'),
                range('A', 'Z')
            )
        );
    }

    /**
     * @param string $string
     * @return string
     */
    private function encodeString(string $string): string
    {
        $characters = str_split($string);
        foreach ($characters as $k => $character) {
            $characters[$k] = $this->encodeAlphaCharacter($character);
        }
        return implode('', $characters);
    }

    /**
     * @param string $character
     * @return string
     */
    private function encodeAlphaCharacter(string $character): string
    {
        if (!isset($this->unsafeCharacters[$character])) {
            return $character;
        }
        return sprintf('ch_start_%d_ch_end', $this->getCharacterCode($character));
    }

    /**
     * @param string $character
     * @return int
     */
    private function getCharacterCode(string $character): int
    {
        return ord($character);
    }

    /**
     * @param string $string
     * @return string
     */
    private function decodeString(string $string): string
    {
        preg_match_all('/(ch_start_(\d{1,3})_ch_end)/', $string, $kMatches);
        if (empty($kMatches[2]) || empty($kMatches[1])) {
            return $string;
        }
        $encodedCharacters = $kMatches[1];
        $decodedCharacters = [];
        foreach ($kMatches[2] as $key => $characterCode) {
            $decodedCharacters[$key] = $this->getCharacterByCode((int)$characterCode);
        }
        return str_replace($encodedCharacters, $decodedCharacters, $string);
    }

    private function getCharacterByCode(int $characterCode): string
    {
        return chr($characterCode);
    }

    /**
     * @param string $string
     * @return string
     */
    private function cleanString(string $string): string
    {
        return str_replace(['\'', '"'], '', trim($string));
    }

    /**
     * @param string $string
     * @return array|string|string[]
     */
    private function restoreAllowedLanguageConstructs(string $string)
    {
        foreach ($this->allowedLanguageConstruct as $constructName) {
            $encodedConstructName = $this->encodeString($constructName);
            $string = str_replace($encodedConstructName, $constructName, $string);
        }
        return $string;
    }

    /**
     * @param array $array
     * @return array
     */
    private function restoreEncodedArray(array $array): array
    {
        return $this->decoderArrayValues(
            $this->decodeArrayKeys(
                $array
            )
        );
    }

    /**
     * @param $array
     * @return array
     */
    private function decodeArrayKeys($array): array
    {
        $result = [];
        foreach ($array as $k => $v) {
            if (is_string($k)) {
                $k = $this->decodeString($k);
            }
            if (is_array($v)) {
                $v = $this->decodeArrayKeys($v);
            }
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    private function decoderArrayValues(array $array): array
    {
        array_walk_recursive($array, function (&$v) {
            if (is_string($v)) {
                $v = $this->decodeString($v);
            }
        });
        return $array;
    }
}
