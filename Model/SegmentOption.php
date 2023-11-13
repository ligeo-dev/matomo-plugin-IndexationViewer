<?php

namespace Piwik\Plugins\IndexationViewer\Model;

final class SegmentOption
{
    /** @var int */
    private $idSegment;
    /** @var string */
    private $name;
    /** @var string */
    private $definition;
    /** @var string */
    private $hash;

    public function __construct(array $data)
    {
        $this->idSegment = intval($data['idsegment'] ?? 0);
        $this->name = strval($data['name'] ?? '');
        $this->definition = strval($data['definition'] ?? '');
        $this->hash = strval($data['hash'] ?? '');
    }

    // Getters --------------------------------------------------------------------------------------------------------

    public function getIdSegment(): int
    {
        return $this->idSegment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
