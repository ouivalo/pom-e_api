<?php


namespace App\Command;


use App\Entity\Composter;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ImportComposter extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'compost:import-composteur';

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
            ->setDescription('Import all composter from ods file')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Import all composter from ods file')
            ->addArgument('filePath', InputArgument::REQUIRED, 'path du fichier ODS a importer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $filePath = $input->getArgument('filePath');

        $composterRepository = $this->em->getRepository(Composter::class);
        $reader = ReaderEntityFactory::createODSReader();

        $reader->open( $filePath);
        $composterCount = 0;
        foreach ($reader->getSheetIterator() as $key => $sheet) {
            if( 1 === $key ){
                foreach ($sheet->getRowIterator() as $rkey => $row) {

                    // Les deux premières lignes du doc sont des entête
                    if( $rkey > 2 ){

                        $cells = $row->getCells();
                        $importId = (string) $cells[0];
                        $composter = $composterRepository->find( $importId);

                        if( ! $composter ){
                            $composter = new Composter();
                        }

                        $composter->setName( (string) $cells[1] );
                        $composter->setAddress( (string) $cells[6] );

                        // Lat Long
                        $latlong = trim( (string) $cells[10] );
                        $latlong = str_replace(PHP_EOL, ' ', $latlong);
                        $latlong = str_replace('  ', ' ', $latlong);
                        $hasCommat = strpos( $latlong, ','  );
                        $latlong = explode( $hasCommat ? ',' : ' ', $latlong );


                        if( count( $latlong ) === 2 && is_numeric( $latlong[0]) && is_numeric( $latlong[1] ) ){
                            $composter->setLat( (float) $latlong[0]);
                            $composter->setLng( (float) $latlong[1]);
                        } else if ( count( $latlong ) > 1 ){
                            $output->writeln( "Erreur lors de l‘import de latLong ( composteur #{$importId}): {$cells[10]}"  );
                        }

                        // Persist
                        $this->em->persist( $composter );
                        $composterCount++;
                    }
                }
            }
        }
        $output->writeln( "Import de {$composterCount} compsteurs"  );

        $reader->close();
        $this->em->flush();


    }
}