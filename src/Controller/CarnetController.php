<?php

namespace App\Controller;

use App\Entity\Carnet;
use App\Form\CarnetType;
use App\Repository\CarnetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/carnet")
 */
class CarnetController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CarnetRepository $carnetRepo): Response
    {
        $carnet = $carnetRepo->findBy(array(), array('id' => 'DESC'));
        return $this->render('carnet/index.html.twig', [
            'Carnets' => $carnet,
        ]);
    }
    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Carnet $carnet, EntityManagerInterface $em)
    {
        $carnet->setActive(($carnet->getActive()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($carnet);
        $em->flush();
        return new Response("true");
    }
    /**
     * @Route("/edit/{id}", name="editCarnet")
     */
    public function editcarnet(Carnet $carnet, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(CarnetType::class, $carnet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($carnet);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('carnet/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/show/{id}", name="showCarnet")
     */
    public function showCarnet($id, CarnetRepository $carnetRepo): Response
    {
        $carnetonly = $carnetRepo->getCarnetbyId($id);
        $matriculeCarnetid = $id;
        $carnetsTicket = $carnetRepo->getTiketByCarnetId($matriculeCarnetid);
        // dd($carnets);
        return $this->render('carnet/print.html.twig', [
            'carnets' => $carnetsTicket,
            'carnetonlys' => $carnetonly
        ]);
    }
}
