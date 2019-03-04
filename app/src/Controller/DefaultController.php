<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="root")
     */
    public function root()
    {
        return $this->redirectToRoute('charts');
    }

    /**
     * @Route("/phpinfo", name="phpinfo")
     */
    public function phpinfo()
    {
        phpinfo();
        return new Response();
    }
}
