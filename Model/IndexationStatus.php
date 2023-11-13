<?php

namespace Piwik\Plugins\IndexationViewer\Model;

final class IndexationStatus
{
    /** @var int */
    private $idarchive;
    /** @var string */
    private $name;
    /** @var int */
    private $idsite;
    /** @var string */
    private $date1;
    /** @var string */
    private $date2;
    /** @var int */
    private $period;
    /** @var string */
    private $ts_archived;
    /** @var int */
    private $value;

    public function __construct(array $data)
    {
        $this->idarchive = intval($data['idarchive'] ?? 0);
        $this->name = strval($data['name'] ?? '');
        $this->idsite = intval($data['idsite'] ?? 0);
        $this->date1 = strval($data['date1'] ?? '');
        $this->date2 = strval($data['date2'] ?? '');
        $this->period = intval($data['period'] ?? 0);
        $this->ts_archived = strval($data['ts_archived'] ?? '');
        $this->value = intval($data['value'] ?? 0);
    }

    // Getters --------------------------------------------------------------------------------------------------------

    public function getIdarchive(): int
    {
        return $this->idarchive;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdsite(): int
    {
        return $this->idsite;
    }

    public function getDate1(): string
    {
        return $this->date1;
    }

    public function getDate2(): string
    {
        return $this->date2;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function getTsArchived(): string
    {
        return $this->ts_archived;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
