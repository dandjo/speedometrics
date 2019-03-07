<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
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
        $this->doctrine->getManager()->createQuery('DELETE FROM App\Entity\DateTimeContainer')->execute();
        $this->doctrine->getManager()->createQuery('DELETE FROM App\Entity\SpeedMetric')->execute();
        $this->doctrine->getManager()->flush();
        $this->logger->info('Memory used: ' . memory_get_peak_usage() . ' bytes');
    }
}
