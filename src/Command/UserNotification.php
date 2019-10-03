<?php
/**
 * Send user notification
 */

namespace App\Command;

use App\Entity\Permanence;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mailjet\Resources;
use Mailjet\Client;
use Symfony\Component\Validator\Constraints\DateTime;


class UserNotification extends ContainerAwareCommand
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'compost:user-notification';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Send notifications to user for next permanence')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Send notifications to user for next permanence')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $permanenceRepo = $this->getContainer()->get('doctrine')->getRepository(Permanence::class);

        $permancesToComme = $permanenceRepo->findAllToNotify();
        $messages = [];

        foreach ( $permancesToComme as $perm ){

            $openers = $perm->getOpeners();

            foreach ($openers as $opener ){
                $messages[] = $this->getFormatMessage( $opener->getEmail(), $perm->getDate(), $opener->getUsername(), $output );
            }

            // TODO vérifier que la réponse de l'API est bien OK avant de $perm->setHasUsersBeenNotify(true)
            // Pas évident car j'ai mis la notification sur les permanence et que l'api répond par addresse mail
            $perm->setHasUsersBeenNotify(true);
            $entityManager->persist( $perm );
        }

        if( count( $messages ) > 0 ){

            $response = $this->sendEmail( $messages );
            $output->writeln( print_r( $response, true ) );
        }
        $entityManager->flush();
    }

    /**
     * @param String $mail
     * @param \DateTime $date
     * @param string $name
     * @param OutputInterface $output
     * @return array
     */
    private function sendEmail($messages)
    {


        $mj = new Client(
            getenv('MJ_APIKEY_PUBLIC'),
            getenv('MJ_APIKEY_PRIVATE'),
            true,
            ['version' => 'v3.1']
        );
        $body = [
            'Messages' => $messages
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->getData();
    }

    private function getFormatMessage( $mail, \DateTime $date, $name )
    {
        $date_formated = $date->format('d/m/Y');
        return [
            'From' => [
                'Email' => "contact@recyclindre.fr",
                'Name'  => "Recyclindre"
            ],
            'To' => [
                [
                    'Email' => $mail, // $mail
                    'Name'  => $name
                ]
            ],
            'TemplateID'        => (int)getenv('MJ_NOTIFICATION_TEMPLATE_ID'),
            'TemplateLanguage'  => true,
            'Subject'           => "[Recyclindre] c'est bientôt à vous d'ouvrir",
            'Variables'         => json_decode("{
                                        \"prenom\": \"{$name}\",
                                        \"date\": \"{$date_formated}\"
                                      }", true)
        ];
    }
}