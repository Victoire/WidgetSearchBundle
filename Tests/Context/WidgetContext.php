<?php

namespace Victoire\Widget\SearchBundle\Tests\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Victoire\Tests\Features\Context\MinkContext;
use Knp\FriendlyContexts\Context\RawMinkContext;

class WidgetContext extends RawMinkContext
{
    /* @var MinkContext $minkContext */
    private $minkContext;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->minkContext = $environment->getContext('Victoire\Tests\Features\Context\MinkContext');
    }

    /**
     * @When /^I use WidgetSearch to search for "(.+)"$/
     */
    public function iUseWidgetSearchToSearchFor($text)
    {
        $this->minkContext->fillField('q', $text);

        $page = $this->getSession()->getPage();
        $searchButton = $page->find('xpath', 'descendant-or-self::div[contains(@class, "searchWidget-inputGroup")]//button');
        if (null === $searchButton) {
            throw new \Behat\Mink\Exception\ResponseTextException('SearchButton could not be found.', $this->getSession());
        }

        $searchButton->click();
    }
}
