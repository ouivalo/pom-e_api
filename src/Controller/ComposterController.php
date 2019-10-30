<?php


namespace App\Controller;


use App\Entity\Composter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComposterController extends AbstractController
{

    /**
     * @Route("/composters.geojson", name="composters-geojson")
     * @param Request $request
     * @return Response
     */
    public function getCompostersGeojson(  Request $request ) : Response
    {
        $composters =  $this->getDoctrine()
            ->getRepository(Composter::class)
            ->findAllForFrontMap();

        // On prépare un GeoJSON de centre formater le l'affichage sur la carte
        $features = [];
        /** @var Composter $c */
        foreach ( $composters as $c ){
            $features[] =[
                'type'  => 'Feature',
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => [$c->getLng(), $c->getLat()]
                ),
                'properties' => [
                    'commune'       => $c->getCommune() ? $c->getCommune()->getId() : null,
                    'categorie'     => $c->getCategorie() ? $c->getCategorie()->getId() : null,
                    'id'            => $c->getId(),
                    'name'          => $c->getName(),
                    'status'        => $c->getStatus(),
                ]
            ];
        }
        $geojson = [
            'type'      => 'FeatureCollection',
            'features'  => $features,
        ];

        return $this->json( $geojson );
    }
}