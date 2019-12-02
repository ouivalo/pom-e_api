<?php


namespace App\Command;


use App\Entity\Composter;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCartQuartier extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'compost:import-carto-quartier';

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->em = $entityManager;

    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import all composter from Carto Quartier CSV')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Import all composter from csv file')
            ->addArgument('filePath', InputArgument::REQUIRED, 'path du fichier CSV a importer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('filePath');

        $reader = ReaderEntityFactory::createCSVReader();
        $reader->setFieldDelimiter(';');

        $reader->open($filePath);
        $composterCount = 0;
        $lines = 0;
        foreach ($reader->getSheetIterator() as $key => $sheet) {

            foreach ($sheet->getRowIterator() as $rkey => $row) {

                if( $rkey > 1 ){
                    $lines++;

                    // do stuff with the row
                    $cells = $row->getCells();

                    if( ! $cells[10]->isEmpty() ){

                        $composterSerialNumber = $cells[10]->getValue();
                        $composterRepository = $this->em->getRepository(Composter::class);

                        $composter = $composterRepository->findOneBy( [ 'serialNumber' => $composterSerialNumber ]);

                        if( ! $composter instanceof  Composter ){
                            $output->writeln( "{$cells[1]->getValue()} - {$composterSerialNumber} : pas trouvé" );
                        } else {

                            // Composteur dont le numéro de série récupérer par Carto Quartier ne semble pas correspondre
                            if( in_array($composterSerialNumber, [171, 164, 31, 212, 186, 130, 185, 213, 201, 105, 158, 211, 202, 203, 188, 187, 217, 219, 221, 222, 229, 233, 144, 195, 200, 207, 218, 23, 232, 208], true) ){

                                $output->writeln( "{$composterSerialNumber} : {$composter->getName()} - {$cells[1]->getValue()}" );

                            } else {

                                $composter->setPublicDescription( $cells[2]->getValue() );
                                $composter->setPermanencesDescription( $cells[8]->getValue() );
                                $this->em->persist( $composter );
                                $composterCount++;
                            }
                        }

                    }
                }

            }
        }
        $this->em->flush();
        $output->writeln( "{$composterCount} composteurs importé sur {$lines} lignes"  );
    }
}