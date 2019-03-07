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
     * @Route("/average-speed", name="charts.average-speed")
     */
    public function averageSpeed()
    {
        return $this->render('charts/average-speed.html.twig');
    }

    /**
     * @Route("/average-speed/stock", name="charts.average-speed.stock")
     */
    public function averageSpeedStock()
    {
        return $this->render('charts/average-speed-stock.html.twig');
    }

    /**
     * @Route("/speed-metrics", name="charts.speed-metrics")
     */
    public function speedMetrics()
    {
        return $this->render('charts/speed-metrics.html.twig');
    }
}
