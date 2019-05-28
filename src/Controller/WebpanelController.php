<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 05.02.2019
 * Time: 12:18
 */

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;


class WebpanelController extends AbstractController
{

    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ParameterBagInterface $params)
    {
        parent::__construct($em, $serializer);
        $this->params = $params;
    }

    /**
     * @Route(path="/", name ="app.index")
     * @return string
     */
    public function index()
    {
        $basePath = '/';
        $title = 'Sample webpanel webpanel';
        $config = $this->params->get('webpanel');
        $styles = [];
        $scripts = [];
        if($config && isset($config['scripts'])){
            $scripts = $config['scripts'];
        }
        if($config && isset($config['styles'])){
            $styles = $config['styles'];
        }
        if(empty($scripts)){
            throw new \LogicException('Webpanel hasn\'t built yet');
        }
        return new Response($this->renderView('webpanel.html.twig', [
            'styles' => $styles,
            'scripts' => $scripts,
            'basePath' => $basePath,
            'title' => $title,
        ]));
    }

}