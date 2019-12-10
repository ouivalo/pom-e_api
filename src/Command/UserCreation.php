<?php


namespace App\Command;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreation extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'compost:user-create';

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
            ->setDescription('Create new user')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Create new user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('email', InputArgument::REQUIRED, 'email')
            ->addArgument('password', InputArgument::REQUIRED, 'password')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setEmail( $email )
            ->setPlainPassword( $password )
            ->setUsername( $username )
            ->setRoles( ['ROLE_ADMIN'])
            ->setEnabled( true);

        $this->em->persist( $user );
        $this->em->flush();
    }

}