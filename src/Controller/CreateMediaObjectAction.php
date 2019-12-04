<?php

namespace App\Controller;

use App\Entity\MediaObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreateMediaObjectAction extends AbstractController
{

    private $parameterBag;

    public function __invoke( MediaObject $data ): MediaObject
    {

        if( ! $data->getImageName() ){
            throw new BadRequestHttpException('Paramétre "imageName" obligatoire');
        }

        $imageName = $data->getImageName();
        $webPath = $this->parameterBag->get('upload_destination') . $imageName;

        // On rénome les fichiers qui ont le même nom
        if( file_exists( $webPath ) ){
            $imageName = preg_replace('/(.)([^.]+)$/', '-' . uniqid( '', false) . '.$2', $imageName );
            $data->setImageName( $imageName );
            $webPath = $this->parameterBag->get('upload_destination') . $imageName;
        }
        $content = strpos( $data->getData(), 'data' ) === 0 ? file_get_contents( $data->getData() ) : base64_decode( $data->getData() );
        file_put_contents($webPath, $content );

        $uploadedFile = new File($webPath);
        $data->setFile( $uploadedFile);

        return $data;
    }

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;

    }
}
