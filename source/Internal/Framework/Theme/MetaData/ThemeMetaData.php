<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

class ThemeMetaData
{
    private string $id = '';
    private string $version = '';
    private string $title = '';
    private string $description = '';
    private string $thumbnail = '';
    private string $author = '';
    private string $parentTheme = '';
    private array $parentVersions = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        if ($id === '') {
            throw new InvalidThemeMetaDataException('Theme id must not be empty');
        }
        $this->id = $id;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getParentTheme(): string
    {
        return $this->parentTheme;
    }

    public function setParentTheme(string $parentTheme): self
    {
        $this->parentTheme = $parentTheme;
        return $this;
    }

    /** @return string[] */
    public function getParentVersions(): array
    {
        return $this->parentVersions;
    }

    /** @param string[] $parentVersions */
    public function setParentVersions(array $parentVersions): self
    {
        $this->parentVersions = $parentVersions;
        return $this;
    }
}
