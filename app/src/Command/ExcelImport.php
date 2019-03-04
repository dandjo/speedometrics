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

class ExcelImport extends Command
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
            ->setName('import:excel')
            ->setDescription('')
            ->setHelp('')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory of your excel files')
            ->addArgument('street', InputArgument::REQUIRED, 'The location street')
            ->addArgument('number', InputArgument::REQUIRED, 'The location number')
            ->addArgument('zip', InputArgument::REQUIRED, 'The location ZIP')
            ->addArgument('city', InputArgument::OPTIONAL, 'The location city')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get or create address
        $street = $input->getArgument('street');
        $number = $input->getArgument('number');
        $zip = $input->getArgument('zip');
        $city = $input->getArgument('city') ?: 'Vienna';
        $address = (new AddressRepository($this->doctrine))->findOneBy([
            'street' => $street,
            'number' => $number,
            'zip' => $zip,
        ]);
        if (empty($address)) {
            $address = new Address();
        }
        $address->setStreet($street);
        $address->setNumber($number);
        $address->setZip($zip);
        $address->setCity($city);
        // read file and import
        $this->doctrine->getManager()->persist($address);
        $directory = $input->getArgument('directory');
        $adapter = new Local($directory);
        $filesystem = new Filesystem($adapter);
        foreach ($filesystem->listContents() as $file) {
            if ($file['type'] === 'file' && isset($file['extension']) && $file['extension'] === 'xls') {
                $this->importFile($directory . DIRECTORY_SEPARATOR . $file['path'], $address);
            }
        }
        $this->doctrine->getManager()->flush();
        $this->logger->info('Memory used: ' . memory_get_peak_usage() . ' bytes');

    }

    /**
     * @param $filename
     * @param Address $address
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function importFile($filename, Address $address) {
        $reader = IOFactory::createReader('Xls');
        $spreadSheet = $reader->load($filename);
        $rows = $spreadSheet->getActiveSheet()->toArray();
        foreach ($rows as $i => $row) {
            $date = array_shift($row);
            $time = array_shift($row);
            if (empty($date) || empty($time)) {
                // 0 = date in "d.m.Y"
                // 1 = time in "H:i"
                continue;
            }
            $dateTimeStr = trim($date) . ' ' . trim($time);
            if (!strtotime($dateTimeStr)) {
                continue;
            }
            $dateTime = Carbon::createFromFormat('d.m.Y H:i', $dateTimeStr);
            if (!$dateTime) {
                continue;
            }
            $this->logger->info($dateTime->format('c'));
            $dataSet = (new DataSetRepository($this->doctrine))->findOneBy([
                'dateTime' => $dateTime,
                'address' => $address,
            ]);
            if (empty($dataSet)) {
                $dataSet = new DataSet();
            }
            $dataSet->setDateTime($dateTime);
            $dataSet->setAddress($address);
            $minimumSpeed = 15;
            $speedDistance = 5;
            for ($r = 0; $r <  22; $r++) {
                $rangeFrom = $r === 0 ? 0 : $minimumSpeed + ($speedDistance * ($r - 1));
                $rangeTo = $minimumSpeed + ($speedDistance * $r);
                $speedCategory = (new SpeedCategoryRepository($this->doctrine))->findOneBy([
                    'dataSet' => $dataSet,
                    'rangeFrom' => $rangeFrom,
                    'rangeTo' => $rangeTo,
                ]);
                if (empty($speedCategory)) {
                    $speedCategory = new SpeedCategory();
                }
                $speedCategory->setDataSet($dataSet);
                $speedCategory->setRangeFrom($rangeFrom);
                $speedCategory->setRangeTo($rangeTo);
                $speedCategory->setAmountVehicles(intval(trim($row[$r])));
                $this->doctrine->getManager()->persist($speedCategory);
            }
            $this->doctrine->getManager()->persist($dataSet);
        }
    }
}
