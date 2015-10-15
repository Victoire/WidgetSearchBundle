<?php

namespace Victoire\Widget\SearchBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function testQuery()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/query');
    }
}
