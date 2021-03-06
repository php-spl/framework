<?php

namespace Spl\Error\Traits;

trait Helper
{
    /**
     * @var callable[] Processors and/or filters
     */
    protected $helpers = [];

    public function addHelper(callable $helper)
    {
        $this->helpers[] = $helper;
    }

    public function getHelpers(): array
    {
        return $this->helpers;
    }

    /**
     * @param callable[] $helpers
     */
    public function setHelpers(array $helpers)
    {
        $this->helpers = [];
        foreach ($helpers as $id => $helper) {
            if (!is_callable($helper)) {
                throw new \UnexpectedValueException("Processor or filter n°$id is a " . gettype($helper) . " instead of a callable.");
            }

            $this->helpers[] = $helper;
        }
    }

    /**
     * Returns the record, altered by all successive processors or false if one filter has returned false.
     * @return array|bool
     */
    public function processHelpers(array $record)
    {
        foreach ($this->helpers as $id => $helper) {
            $returnedValue = $helper($record);
            if ($returnedValue === false) {
                return false;
            }

            $type = gettype($returnedValue);
            if ($type !== "boolean") {
                if($type !== "array" || empty($returnedValue)) {
                    throw new \UnexpectedValueException("Helper n°$id returned an empty array or a value of type '$type' instead of array.");
                }

                $record = $returnedValue;
            }
        }

        return $record;
    }
}
