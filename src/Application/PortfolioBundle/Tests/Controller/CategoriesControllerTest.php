<?php

namespace Application\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{

    public function testEmptyCategorysList()
    {
        $this->loadFixtures(array());

        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'qwerty',
        ));
        $crawler = $client->request('GET', '/admin/portfolio/categories');

        // @todo: change assert
        $this->assertTrue($crawler->filter('html:contains("List of categories is empty")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Web Development")')->count() == 0);
    }

    public function testCategoriesList()
    {
        $this->loadFixtures(array(
                    'Application\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
                    'Application\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
                ));

        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'qwerty',
        ));
        $crawler = $client->request('GET', '/admin/portfolio/categories');

        $this->assertTrue($crawler->filter('html:contains("Web Development")')->count() > 0);
    }

    public function testCreateValidCategory()
    {
        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'qwerty',
        ));
        $crawler = $client->request('GET', '/admin/portfolio/category/create');

        $form = $crawler->selectButton('Send')->form();

        $form['category[name]'] = 'Web Design';
        $form['category[slug]'] = 'web-design';
        $form['category[description]'] = 'Short text about web design servise.';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Web Design")')->count() > 0);
    }

//    public function testCreateInvalidCategory()
//    {
//    }
//
//    public function testEditCategory()
//    {
//    }
//
//    public function testDeleteCategory()
//    {
//    }

}
