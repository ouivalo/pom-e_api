<?php


namespace App\Command;


use App\Entity\ApprovisionnementBroyat;
use App\Entity\Commune;
use App\Entity\Composter;
use App\Entity\PavilionsVolume;
use App\Entity\Pole;
use App\Entity\Quartier;
use App\Entity\User;
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



        $reader = ReaderEntityFactory::createODSReader();

        $reader->open( $filePath);
        $composterCount = 0;
        foreach ($reader->getSheetIterator() as $key => $sheet) {
            if( 1 === $key ){
                foreach ($sheet->getRowIterator() as $rkey => $row) {

                    // Les deux premières lignes du doc sont des entête
                    if( $rkey > 2 ){

                        $cells = $row->getCells();
                        //$this->importOnglet1( $cells, $output );


                        $composterCount++;
                    }
                }
            } else if( 2 === $key ){
                foreach ($sheet->getRowIterator() as $rkey => $row) {

                    // Les deux premières lignes du doc sont des entête
                    if( $rkey > 2 ){

                        $cells = $row->getCells();
                        $this->importOnglet2( $cells, $output );
                    }
                }
            }
        }
        $output->writeln( "Import de {$composterCount} compsteurs"  );

        $reader->close();
        $this->em->flush();


    }

    private function importOnglet2( $cells, OutputInterface $output )
    {
        $composterRepository                    = $this->em->getRepository(Composter::class);
        $approvisionnementBroyatRepository      = $this->em->getRepository(ApprovisionnementBroyat::class);

        $composter = $composterRepository->findOneBy( [ 'name' => (string) $cells[0] ] );
        if( ! $composter ){
            $output->writeln( "Pas trouvé {$cells[0]}"  );
        } else {

            // approvisionnement Broyat
            $appBroyatName = (string) $cells[4];

            if( ! empty( $appBroyatName ) ){

                if( 'Compostri (libre service)' === $appBroyatName ){

                    $appBroyatName = 'Libre service Compostri';
                } elseif ( 'Autonome + Compostri' === $appBroyatName ){
                    $appBroyatName = 'Compostri + Autonome';
                }

                $approvisionnementBroyat = $approvisionnementBroyatRepository->findOneBy( [ 'name' => $appBroyatName ] );

                if( ! $approvisionnementBroyat ){
                    $approvisionnementBroyat = new ApprovisionnementBroyat();
                    $approvisionnementBroyat->setName( $appBroyatName );
                    $this->em->persist( $approvisionnementBroyat );
                    $this->em->flush();
                }

                $composter->setApprovisionnementBroyat( $approvisionnementBroyat );
            }


            $composter->setShortDescription( (string) $cells[5] );
            $composter->setCadena( (string) $cells[6] );

            // Dynamisme
            $animation = is_numeric( (string) $cells[16] ) ? (int) (string) $cells[16] : false;
            $environnement = is_numeric( (string) $cells[17] ) ? (int) (string) $cells[17] : false;
            $technique	 = is_numeric( (string) $cells[18] ) ? (int) (string) $cells[18] : false;
            $autonomie = is_numeric( (string) $cells[19] ) ? (int) (string) $cells[19] : false;

            if( $animation !== false ) { $composter->setAnimation( $animation ); }
            if( $environnement !== false ) { $composter->setEnvironnement( $environnement ); }
            if( $technique !== false ) { $composter->setTechnique( $technique ); }
            if( $autonomie !== false ) { $composter->setAutonomie( $autonomie ); }

        }
    }
    private function importOnglet1( $cells, OutputInterface $output )
    {

        $composterRepository    = $this->em->getRepository(Composter::class);
        $communeRepository      = $this->em->getRepository(Commune::class);
        $poleRepository         = $this->em->getRepository(Pole::class);
        $quartierRepository     = $this->em->getRepository(Quartier::class);
        $volumeRepository       = $this->em->getRepository(PavilionsVolume::class);
        $userRepository         = $this->em->getRepository(User::class);


        $importId = (string) $cells[0];
        $composter = $composterRepository->find( $importId);

        if( ! $composter ){
            $composter = new Composter();
        }

        $this->importOnglet1( $cells );
        $composter->setName( (string) $cells[1] );
        $composter->setAddress( (string) $cells[6] );

        // Commune
        $communeName = trim( (string) $cells[3] );
        $commune = $communeRepository->findOneBy( [ 'name' => $communeName ] );

        if( ! $commune ){
            $commune = new Commune();
            $commune->setName( $communeName );
            $this->em->persist( $commune );
            $this->em->flush();
        }
        $composter->setCommune( $commune );

        // Pole
        $poleName = trim( (string) $cells[4] );

        if( $poleName !== '' ){
            if( $poleName === 'LSV' || strpos( $poleName, 'ignoble' ) ){
                $poleName =  'Loire, Sèvre et Vignoble';
            }
            $pole = $poleRepository->findOneBy( [ 'name' => $poleName ] );

            if( ! $pole ){
                $pole = new Pole();
                $pole->setName( $poleName );
                $this->em->persist( $pole );
                $this->em->flush();
                $output->writeln( "Pole créé : {$pole->getName()}"  );
            }
            $composter->setPole( $pole );
        }

        // Quartier
        $quartierName = trim( (string) $cells[5] );
        if( $quartierName !== '' ){

            if( $quartierName === 'Ile de Nantes' ){
                $quartierName =  'Nantes Île-de-Nantes';
            } elseif ( $quartierName === 'Dervallières Zola') {
                $quartierName =  'Nantes Dervallières-Zola';
            } elseif ( $quartierName === 'Nantes Malakoff – Saint-Donatien') {
                $quartierName =  'Nantes Malakoff - Saint-Donatien';
            } elseif ( $quartierName === 'Breil Barberie') {
                $quartierName =  'Nantes Breil-Barberie';
            }

            $quartier = $quartierRepository->findOneBy( [ 'name' => $quartierName ] );

            if( ! $quartier ){
                $quartier = new Quartier();
                $quartier->setName( $quartierName );
                $this->em->persist( $quartier );
                $this->em->flush();
                $output->writeln( "Quartier créé : {$quartier->getName()}"  );
            }
            $composter->setQuartier( $quartier );
        }

        // Volume des pavillons
        $volumeName = trim( (string) $cells[7] );
        if( $volumeName !== '' ){

            $pavilionVolume = $volumeRepository->findOneBy( [ 'volume' => $volumeName ] );

            if( ! $pavilionVolume ){
                $pavilionVolume = new PavilionsVolume();
                $pavilionVolume->setVolume( $volumeName );
                $this->em->persist( $pavilionVolume );
                $this->em->flush();
                $output->writeln( "volume de pavillons créé : {$pavilionVolume->getVolume()}"  );
            }
            $composter->setPavilionsVolume( $pavilionVolume );
        }

        // MC
        $mcName = trim( (string) $cells[11] );
        if( $mcName !== '' ){

            $mc = $userRepository->findOneBy( [ 'username' => $mcName ] );

            if( ! $mc ){
                $mc = new User();
                $mc->setUsername( $mcName );
                $mc->setEmail( mb_strtolower( $mcName ) . '@compostri.fr' );
                $mc->setPassword( $mcName );
                $mc->setRoles( ['ROLE_MC'] );
                $this->em->persist( $mc );
                $this->em->flush();
                $output->writeln( "Maitre composter créer : {$mc->getUsername()}"  );
            }
            $composter->setMc( $mc );
        }

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
    }
}