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
use Thelia\Model\LangQuery;

class CustomContactLoop extends BaseLoop implements PropelSearchLoopInterface
{

    public function parseResults(LoopResult $loopResult)
    {
        /* @var CustomContact $customFieldForm */
        foreach ($loopResult->getResultDataCollection() as $customFieldForm) {

            if ($this->getLang() !== null)
            {
                $lang = LangQuery::create()
                    ->filterByLocale($this->getLang())
                    ->findOne();

                $customFieldForm->setLocale($lang->getLocale());
            }

            if ($this->getLangId() !== null)
            {
                $lang = LangQuery::create()
                    ->filterById($this->getLangId())
                    ->findOne();

                $customFieldForm->setLocale($lang->getLocale());
            }

            $loopResultRow = new LoopResultRow($customFieldForm);

            if ($this->getIsFront() === null ||
                $customFieldForm->getTitle() !== null &&
                $customFieldForm->getFieldConfiguration() !== null &&
                $customFieldForm->getEmail() !== null
            ) {
            $loopResultRow
                ->set('ID', $customFieldForm->getId())
                ->set('TITLE', $customFieldForm->getTitle())
                ->set('CODE', $customFieldForm->getCode())
                ->set('FIELD_CONFIGURATION', $customFieldForm->getFieldConfiguration())
                ->set('LOCALE', $customFieldForm->getLocale())
                ->set('EMAIL', $customFieldForm->getEmail())
                ->set('URL_SUCCESS', $customFieldForm->getSuccessUrl())
                ;

            $this->addOutputFields($loopResultRow, $customFieldForm);

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

        $search
            ->orderByCreatedAt(Criteria::DESC)
            ->find();

        return $search;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAnyListTypeArgument('lang'),
            Argument::createAnyListTypeArgument('lang_id'),
            Argument::createAnyListTypeArgument('is_front')
        );
    }
}