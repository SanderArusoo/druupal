<?php

namespace Drupal\hello_world\Controller;


use Symfony\Component\HttpFoundation\Response;

class GreetingController
{
  public function greeting()
  {
    return new Response('Hello World');
  }


}
