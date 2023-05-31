<?php

namespace CustomContact\Loop;

use CustomContact\Model\CustomContact;
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
        /** @var CustomContact $form */
        foreach ($loopResult->getResultDataCollection()->getData() as $form) {
            $fieldConfiguration = json_decode($form->getFieldConfiguration());

            foreach ($fieldConfiguration as $field) {
                $loopResultRow = new LoopResultRow($form);

                foreach ($field as $key => $value) {
                    $loopResultRow->set(str_replace(' ', '_', strtoupper($key)), $value);
                }

                $this->addOutputFields($loopResultRow, $field);

                $loopResult->addRow($loopResultRow);
            }
        }

        return $loopResult;
    }

    public function buildModelCriteria()
    {
        $search = CustomContactQuery::create();

        $id = $this->getId();
        if (null !== $id) {
            $search->filterById($id, Criteria::IN);
        }

        $code = $this->getCode();
        if (null !== $code) {
            $search->filterByCode($code, Criteria::IN);
        }

        return $search;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAlphaNumStringListTypeArgument('code')
        );
    }
}
