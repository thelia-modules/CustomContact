<?php

namespace CustomContact;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Model\LangQuery;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Module\BaseModule;

class CustomContact extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'customcontact';

    /** @var Translator */
    protected $translator;

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    public function postActivation(ConnectionInterface $con = null): void
    {
        // create new message
        if (null === MessageQuery::create()->findOneByName('mail_custom_contact')) {

            $message = new Message();
            $message
                ->setName('mail_custom_contact')
                ->setHtmlTemplateFileName('custom_contact.html')
                ->setHtmlLayoutFileName('')
                ->setTextTemplateFileName('custom_contact.txt')
                ->setTextLayoutFileName('')
                ->setSecured(0);

            $languages = LangQuery::create()->find();

            foreach ($languages as $language) {
                $locale = $language->getLocale();

                $message->setLocale($locale);

                $message->setTitle(
                    $this->trans('Custom Contact message', [], $locale)
                );
                $message->setSubject(
                    $this->trans('Contact to {$store_name}', [], $locale)
                );
            }

            $message->save();
        }
    }

    protected function trans($id, array $parameters = [], $locale = null)
    {
        if (null === $this->translator) {
            $this->translator = Translator::getInstance();
        }

        return $this->translator->trans($id, $parameters, CustomContact::DOMAIN_NAME, $locale);
    }

    /**
     * Defines how services are loaded in your modules
     *
     * @param ServicesConfigurator $servicesConfigurator
     */
    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }

    /**
     * Execute sql files in Config/update/ folder named with module version (ex: 1.0.1.sql).
     *
     * @param $currentVersion
     * @param $newVersion
     * @param ConnectionInterface $con
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null): void
    {
        $finder = Finder::create()
            ->name('*.sql')
            ->depth(0)
            ->sortByName()
            ->in(__DIR__.DS.'Config'.DS.'update');

        $database = new Database($con);

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            if (version_compare($currentVersion, $file->getBasename('.sql'), '<')) {
                $database->insertSql(null, [$file->getPathname()]);
            }
        }
    }
}
