<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/charts")
 */
class ChartController extends AbstractController
{
    /**
     * @Route(name="charts")
     */
    public function charts()
    {
        return $this->render('charts/base.html.twig');
    }

    /**
     * @Route("/average-speed/hour", name="charts.average-speed.hour")
     */
    public function averageSpeedPerHour()
    {
        return $this->render('charts/average-speed-hour.html.twig');
    }

    /**
     * @Route("/average-speed/hour/stock", name="charts.average-speed.hour.stock")
     */
    public function averageSpeedPerHourStock()
    {
        return $this->render('charts/average-speed-hour-stock.html.twig');
    }

    /**
     * @Route("/speed-categories", name="charts.speed-categories")
     */
    public function speedCategories()
    {
        return $this->render('charts/speed-categories.html.twig');
    }
}
