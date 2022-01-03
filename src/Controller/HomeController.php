<?php

namespace App\Controller;

use App\Repository\CarnetRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @Route("/")
     */
    public function index(TicketRepository $ticketRepo, CarnetRepository $carnetRepo): Response
    {

        $activeCarnet = $carnetRepo->findbyActiveCarnet();
        $totalCarnet = $carnetRepo->getTotalCarnet();
        $saleCarnet = $carnetRepo->findbyInactiveCarnet();
        
        $activeTicket = $ticketRepo->findbyActiveTicket();
        $totalTicket = $ticketRepo->getTotalTicket();
        $saleTicket = $ticketRepo->findbyInactiveTicket();




        return $this->render('home/index.html.twig', compact(
            'activeCarnet', 
            'totalCarnet',
            'saleCarnet',
            'activeTicket',
            'totalTicket',
            'saleTicket',
        ));
    }
}
