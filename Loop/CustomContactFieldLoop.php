<?php

namespace CustomContact\Loop;

use CustomContact\Model\CustomContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class CustomContactFieldLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection()->getData() as $field) {

            $fieldsConfiguration = json_decode($field->getVirtualColumn('i18n_FIELD_CONFIGURATION'));

            foreach ($fieldsConfiguration as $fieldConfiguration) {
                $loopResultRow = new LoopResultRow($field);

                foreach ($fieldConfiguration as $key => $value) {
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

        $this->configureI18nProcessing(
            $search,
            [
                'FIELD_CONFIGURATION',
            ]
        );

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
