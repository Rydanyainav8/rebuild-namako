<?php

namespace App\Controller;

use App\Entity\Carnet;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\CarnetRepository;
use App\Repository\TicketRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable as MonologDateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="ticket")
     */
    public function index(TicketRepository $tiketRepo, CarnetRepository $carnetRepo, Request $request): Response
    {

        $limit = 18;
        $page = (int)$request->query->get("page", 1);

        $filters = $request->get("carnets");
        // dd($filters);
        $tickets = $tiketRepo->getPaginatedTickets($page, $limit, $filters);
        // dd($tickets);
        $total = $tiketRepo->getTotalTickets($filters);
        // dd($total);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('ticket/_content.html.twig', compact('tickets', 'total', 'limit', 'page'))
            ]);
        }

        $carnets =  $carnetRepo->findAll();
        return $this->render('ticket/index.html.twig', compact('tickets', 'total', 'limit', 'page', 'carnets'));
    }
    
    /**
     * @Route("/create", name="addTicket")
     */
    public function add(Request $req, EntityManagerInterface $em, QrCodeController $qrcode)
    {
        $carnet = new Carnet();

        $day = date("Ymd");
        $hour = date("His");
        $c = $day . $hour;
        $range = range('A', 'Z');
        $index = array_rand($range);
        $Ranz = $range[$index];
        $stringdh = strtotime($c);
        $stringdhR = $stringdh . $Ranz;

        $t = strtotime($c);
        $tik = new Ticket();
        $form = $this->createForm(TicketType::class, $tik);
        $codeqr = $qrcode->qrcode($t);
        $form->handleRequest($req);

        $carn = $carnet->setMatricule($stringdhR);

        for ($i = 0; $i < 18; $i++) {
            $tik = new Ticket();
            $b = $t++;
            $tik->setNumero($b);
            $tik->setCreatedAt(new \DateTime('now'));
            $tik->setQr($codeqr);
            $tik->setValue(500);
            $tik->setCarnet($carn);
            $carnet->setTexte('Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vero saepe qui consectetur fuga consequatur id deserunt quaerat ex molestiae aspernatur, asperiores sit odit rem natus facilis quas possimus voluptatem. Nam quae sed eius reprehenderit, cupiditate corrupti in ipsam delectus laborum.');
            $carnet->setCreatedAt(new DateTime('now'));
            $carnet->setActive(false);
            $tik->setActive(false);
            $em->persist($tik);
        }
        $em->flush();
        return $this->redirectToRoute('ticket');
        // AVEC FORMULAIRE
        // if ($form->isSubmitted() && $form->isValid()) {


        //     $em->flush();
        //     return $this->redirectToRoute('ticket');
        // }
        // return $this->render('admin/ticket/create.html.twig', [
        //     'form' => $form->createView()
        // ]);
    }
    /**
     * @Route("/edit/{id}", name="editTiket")
     */
    public function edit(Ticket $tiket, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TicketType::class, $tiket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tiket);
            $em->flush();
            return $this->redirectToRoute('ticket');
        }
        return $this->render('ticket/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/activer/{id}", name="TicketActive")
     */
    public function activerTicket(Ticket $tiket)
    {
        $tiket->setActive(($tiket->getActive()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($tiket);
        $em->flush();

        return new Response("true");
    }
}
