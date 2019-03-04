<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/phpinfo", name="phpinfo")
     */
    public function phpinfo()
    {
        phpinfo();
        return new Response();
    }
}
