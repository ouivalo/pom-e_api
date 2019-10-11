<?php


namespace App\Command;


use App\Entity\ApprovisionnementBroyat;
use App\Entity\Commune;
use App\Entity\Composter;
use App\Entity\LivraisonBroyat;
use App\Entity\PavilionsVolume;
use App\Entity\Pole;
use App\Entity\Quartier;
use App\Entity\User;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Entity\Cell;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ImportComposter extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'compost:import-composteur';

    private $em;
    private $output;

    public function __construct(EntityManagerInterface $entityManager )
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
        $this->output = $output;

        $filePath = $input->getArgument('filePath');



        $reader = ReaderEntityFactory::createODSReader();

        $reader->open( $filePath);
        $composterCount = 0;
        foreach ($reader->getSheetIterator() as $key => $sheet) {


            if( 1 === $key ){
                foreach ($sheet->getRowIterator() as $rkey => $row) {

                    // Les deux premières lignes du doc sont des entête
                    if( $rkey > 2 ){

//                        $cells = $row->getCells();
//                        $this->importOnglet1( $cells );
//                        $this->em->flush();


                        $composterCount++;
                    }
                }
            } else if( 2 === $key ){
                foreach ($sheet->getRowIterator() as $rkey => $row) {

                    // Les deux premières lignes du doc sont des entête
                    if( $rkey > 2 ){

                        $cells = $row->getCells();
                        $this->importOnglet2( $cells );
                        $this->em->flush();
                    }
                }
            }
        }
        $output->writeln( "Import de {$composterCount} compsteurs"  );

        $reader->close();


    }

    private function importOnglet2( $cells )
    {
        $composterRepository                    = $this->em->getRepository(Composter::class);
        $approvisionnementBroyatRepository      = $this->em->getRepository(ApprovisionnementBroyat::class);

        $composter = $composterRepository->findOneBy( [ 'name' => (string) $cells[0] ] );
        if( ! $composter ){
            $this->output->writeln( "Pas trouvé le composter '{$cells[0]}'"  );
        } else {

            // approvisionnement Broyat
//            $appBroyatName = (string) $cells[4];
//
//            if( ! empty( $appBroyatName ) ){
//
//                if( 'Compostri (libre service)' === $appBroyatName ){
//
//                    $appBroyatName = 'Libre service Compostri';
//                } elseif ( 'Autonome + Compostri' === $appBroyatName ){
//                    $appBroyatName = 'Compostri + Autonome';
//                }
//
//                $approvisionnementBroyat = $approvisionnementBroyatRepository->findOneBy( [ 'name' => $appBroyatName ] );
//
//                if( ! $approvisionnementBroyat ){
//                    $approvisionnementBroyat = new ApprovisionnementBroyat();
//                    $approvisionnementBroyat->setName( $appBroyatName );
//                    $this->em->persist( $approvisionnementBroyat );
//                    $this->em->flush();
//                }
//
//                $composter->setApprovisionnementBroyat( $approvisionnementBroyat );
//            }
//
//
//            $composter->setShortDescription( (string) $cells[5] );
//            $composter->setCadena( (string) $cells[6] );

            // Dates
//            $installation   =  ! $cells[7]->isEmpty() ? $this->getDateStringFromFile( $cells[7] ) : false;
//            $inauguration   =  ! $cells[8]->isEmpty() ? $this->getDateStringFromFile( $cells[8] ) : false;
//            $miseEnRoute    =  ! $cells[9]->isEmpty() ? $this->getDateStringFromFile( $cells[9] ) : false;
//
//            if( $installation instanceof DateTime) {
//                $composter->setDateInstallation($installation);
//            }
//            if( $inauguration instanceof DateTime) {
//                $composter->setDateInauguration($inauguration);
//            }
//            if( $miseEnRoute instanceof DateTime){
//                $composter->setDateMiseEnRoute($miseEnRoute);
//            } else {
//                $composter->setDateMiseEnRoute( new DateTime( "{$cells[1]->getValue()}-06-26" ));
//            }
//
//            // Dynamisme
//            $animation = is_numeric( (string) $cells[16] ) ? (int) (string) $cells[16] : false;
//            $environnement = is_numeric( (string) $cells[17] ) ? (int) (string) $cells[17] : false;
//            $technique	 = is_numeric( (string) $cells[18] ) ? (int) (string) $cells[18] : false;
//            $autonomie = is_numeric( (string) $cells[19] ) ? (int) (string) $cells[19] : false;

//            if( $animation !== false ) { $composter->setAnimation( $animation ); }
//            if( $environnement !== false ) { $composter->setEnvironnement( $environnement ); }
//            if( $technique !== false ) { $composter->setTechnique( $technique ); }
//            if( $autonomie !== false ) { $composter->setAutonomie( $autonomie ); }


            // Livraison de Broyat
            // 2018
//            $livraison2018 = $cells[13]->getValue();
//            if( $livraison2018 ){
//                $find = preg_match('/([\d]+)( *bacs)*/', $livraison2018, $matches);
//                if( $find ){
//                    $quantity = (int) $matches[1];
//                    $livraisonBroyat = new LivraisonBroyat();
//                    $livraisonBroyat->setQuantite( $quantity );
//                    $livraisonBroyat->setUnite( $quantity < 100 ? 'bacs' : 'L');
//                    $livraisonBroyat->setLivreur( 'compostri' );
//                    $livraisonBroyat->setComposter( $composter );
//                    $livraisonBroyat->setDate( new DateTime( '2018-06-26' ) );
//
//                    $this->em->persist( $livraisonBroyat );
//                } else {
//                    $this->output->writeln( "Pas gérable : '{$livraison2018}'"  );
//                }
//            }

            // 2019
//            $livraison2019 = $cells[14]->getValue();
//            if( $livraison2019 ){
//                $find = preg_match('/([\d]+)( *bacs)*/', $livraison2019, $matches);
//                if( $find ){
//                    $quantity = (int) $matches[1];
//                    $livraisonBroyat = new LivraisonBroyat();
//                    $livraisonBroyat->setQuantite( $quantity );
//                    $livraisonBroyat->setUnite( $quantity < 100 ? 'bacs' : 'L');
//                    $livraisonBroyat->setLivreur( 'compostri' );
//                    $livraisonBroyat->setComposter( $composter );
//                    $livraisonBroyat->setDate( new DateTime( '2019-06-26' ) );
//
//                    $this->em->persist( $livraisonBroyat );
//                } else {
//                    $this->output->writeln( "Pas gérable : '{$livraison2019}'"  );
//                }
//            }

            // 2019 ALISE
//            $livraisonAlise = $cells[15]->getValue();
//            if( $livraisonAlise ){
//                $find = preg_match('/([\d]+)( *poubelles)*/', $livraisonAlise, $matches);
//                if( $find ){
//                    $quantity = (int) $matches[1];
//                    $livraisonBroyat = new LivraisonBroyat();
//                    $livraisonBroyat->setQuantite( $quantity );
//                    $livraisonBroyat->setUnite( 'poubelles');
//                    $livraisonBroyat->setLivreur( 'alise' );
//                    $livraisonBroyat->setComposter( $composter );
//                    $livraisonBroyat->setDate( new DateTime( '2019-06-26' ) );
//
//                    $this->em->persist( $livraisonBroyat );
//                } else {
//                    $this->output->writeln( "Pas gérable : '{$livraisonAlise}'"  );
//                }
//            }
        }
    }

    /**
     * @param $cells
     * @throws Exception
     */
    private function importOnglet1( $cells ): void
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

        $composter->setName( (string) $cells[1] );

        // date d'installation
        if( ! $cells[2]->isEmpty() ){ $composter->setDateMiseEnRoute( new DateTime( "{$cells[2]->getValue()}-06-26" ) ); }

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
                $this->output->writeln( "Pole créé : {$pole->getName()}"  );
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
                $this->output->writeln( "Quartier créé : {$quartier->getName()}"  );
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
                $this->output->writeln( "volume de pavillons créé : {$pavilionVolume->getVolume()}"  );
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
                $this->output->writeln( "Maitre composter créer : {$mc->getUsername()}"  );
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
            $this->output->writeln( "Erreur lors de l‘import de latLong ( composteur #{$importId}): {$cells[10]}"  );
        }

        // Persist
        $this->em->persist( $composter );
    }


    /**
     * @param Cell $date
     * @return DateTime|null
     * @throws Exception
     */
    private function getDateStringFromFile( Cell $date ) : ?DateTime
    {

        $dateFormated = null;

        if( $date->isDate() ){

            $dateFormated = $date->getValue();

        } else {
            $find = preg_match('/(\d\d\/\d\d\/\d\d\d\d)/', $date, $matches);

            if( $find ){
                $dateArray = explode( '/', $matches[1] );
                $dateFormated = new DateTime( "{$dateArray[2]}-{$dateArray[1]}-{$dateArray[0]}");
            } else {
                $this->output->writeln( "Pas un bon format de date : {$date}"  );
            }
        }

        return $dateFormated;
    }
}