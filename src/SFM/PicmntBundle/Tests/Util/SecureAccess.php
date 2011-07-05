<?php

namespace SFM\PicmntBundle\Tests\Util;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecureAccess extends WebTestCase
{

  public function getClient()
  {

    $client = $this->createClient();

    $crawler = $client->request('GET','/logout');
    
    $crawler = $client->request('GET','/login');

    $form = $crawler->selectButton('login')->form();

    $form['_username'] = 'moises';
    $form['_password'] = 'password';

    $crawler = $client->submit($form);
    
    return $client;

  }

}