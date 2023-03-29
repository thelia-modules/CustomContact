<?php

namespace CustomContact\Loop;

use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class CustomContactFieldLoop extends BaseLoop implements ArraySearchLoopInterface
{
    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    public function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('fields')
        );
    }

    public function buildArray()
    {
        $items = [];

        $fields = json_decode($this->getFields());

        $index = 0;

        foreach ($fields as $field)
        {
            $items[$index][] = $field->label;
            $items[$index][] = $field->required;
            $index++;
        }

        return $items;
    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $item) {

            $loopResultRow = new LoopResultRow();

            $loopResultRow->set("LABEL", $item[0]);
            $loopResultRow->set("REQUIRED", $item[1]);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}