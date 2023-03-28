<?php

namespace CustomContactForm\Loop;

use CustomContactForm\Model\CustomContactFormQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class CustomContactFormLoop extends BaseLoop implements PropelSearchLoopInterface
{

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $customFieldForm) {

            $loopResultRow = new LoopResultRow($customFieldForm);

            $loopResultRow
                ->set('ID', $customFieldForm->getId())
                ->set('TITLE', $customFieldForm->getTitle())
                ->set('CODE', $customFieldForm->getCode())
                ->set('FIELD_CONFIGURATION', $customFieldForm->getFieldConfiguration())
                ->set('EMAIL', $customFieldForm->getEmail())
                ;

            $this->addOutputFields($loopResultRow, $customFieldForm);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    public function buildModelCriteria()
    {
        $search = CustomContactFormQuery::create();

        $id = $this->getId();
        if (null !== $id) {
            $search->filterById($id, Criteria::IN);
        }

        $search->orderByCreatedAt(Criteria::DESC);

        return $search;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
        );
    }
}