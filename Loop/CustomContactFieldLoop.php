<?php

namespace CustomContact\Loop;

use CustomContact\Model\CustomContact;
use CustomContact\Model\CustomContactI18nQuery;
use CustomContact\Model\CustomContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class CustomContactFieldLoop extends BaseLoop implements PropelSearchLoopInterface
{

    public function parseResults(LoopResult $loopResult)
    {
        $index = 0;

        foreach (json_decode($loopResult->getResultDataCollection()->getData()[0]) as $field) {

            $loopResultRow = new LoopResultRow($field);

            $loopResultRow
                ->set('LABEL', $field->label)
                ->set('REQUIRED', $field->required)
            ;

            $this->addOutputFields($loopResultRow, $field);

            $loopResult->addRow($loopResultRow);
            $index++;
        }

        return $loopResult;
    }

    public function buildModelCriteria()
    {
        $search = CustomContactI18nQuery::create()
            ->select(['field_configuration']);

        $id = $this->getId();
        if (null !== $id) {
            $search->filterById($id, Criteria::IN);
        }

        $search->find();

        return $search;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id')
        );
    }
}