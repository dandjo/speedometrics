<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\DataSet;
use App\Entity\SpeedCategory;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RandomDataImportCommand extends Command
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Import constructor.
     * @param RegistryInterface $doctrine
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(RegistryInterface $doctrine, LoggerInterface $logger, string $name = null)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('import:random')
            ->setDescription('Imports random data')
            ->addArgument('iterations', InputArgument::OPTIONAL, 'Amount of iterations', 0)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($iter = 0; $iter < intval($input->getArgument('iterations')); $iter++) {
            $address = new Address();
            $address->setStreet(implode(' ', [
                array_rand(array_flip([
                    'Sankt Erich',
                    'Seefelder',
                    'König Arthur',
                    'Papst Augustus',
                    'Erzherzog Johann',
                    'Landstraßer',
                    'Kloburger',
                    'Wiener',
                    'Brünner',
                    'Salzburger',
                    'Grazer',
                    'Villacher',
                    'Eisenstädter',
                    'Linzer',
                ])),
                array_rand(array_flip([
                    'Straße',
                    'Gasse',
                    'Weg',
                    'Promenade',
                    'Steg',
                    'Hauptstraße',
                    'Flur',
                ])),
            ]));
            $address->setNumber(random_int(1, 200));
            $address->setZip(1000 + 10 * random_int(1, 23));
            $address->setCity('Vienna');
            $this->doctrine->getManager()->persist($address);
            $dateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                (new Carbon())->format('Y-m-d') . '00:00:00'
            );
            for ($i = 0; $i < 336; $i++) { // 14 days
                $dataSet = new DataSet();
                $dataSet->setDateTime(clone $dateTime);
                $dataSet->setAddress($address);
                $minimumSpeed = 15;
                $speedDistance = 5;
                $weights = [
                    [1, 3, 6, 10, 32, 26, 18, 12, 8, 5, 4, 3, 2, 1], // typical T30
                    [1, 2, 5, 6, 8, 7, 16, 20, 28, 34, 26, 14, 6, 3], // typical T50
                ];
                for ($r = 0; $r < 22; $r++) {
                    $rangeFrom = $r === 0 ? 0 : $minimumSpeed + ($speedDistance * ($r - 1));
                    $rangeTo = $minimumSpeed + ($speedDistance * $r);
                    $speedCategory = new SpeedCategory();
                    $speedCategory->setDataSet($dataSet);
                    $speedCategory->setRangeFrom($rangeFrom);
                    $speedCategory->setRangeTo($rangeTo);
                    $speedCategory->setAmountVehicles(intval(random_int(1, 10) * ($weights[$iter % 2][$r] ?? 0) / 10));
                    $this->doctrine->getManager()->persist($speedCategory);
                }
                $this->doctrine->getManager()->persist($dataSet);
                $dateTime->addHour();
            }
            $this->doctrine->getManager()->flush();
            $this->logger->info('Successfully imported: ' . $address->toString());
        }
        $this->logger->info('Memory used: ' . memory_get_peak_usage() . ' bytes');
    }
}
