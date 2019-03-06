<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\DataSet;
use App\Entity\SpeedCategory;
use App\Repository\AddressRepository;
use App\Repository\DataSetRepository;
use App\Repository\SpeedCategoryRepository;
use Carbon\Carbon;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearDatabaseCommand extends Command
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
            ->setName('database:clear')
            ->setDescription('Clears the database')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine->getManager()->createQuery('DELETE FROM App\Entity\Address')->execute();
        $this->doctrine->getManager()->createQuery('DELETE FROM App\Entity\DataSet')->execute();
        $this->doctrine->getManager()->createQuery('DELETE FROM App\Entity\SpeedCategory')->execute();
        $this->doctrine->getManager()->flush();
        $this->logger->info('Memory used: ' . memory_get_peak_usage() . ' bytes');
    }
}
