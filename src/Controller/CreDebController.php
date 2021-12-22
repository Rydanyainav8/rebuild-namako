<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\SearchUserType;
use App\Form\UsersType;
use App\Repository\GenderRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use App\Service\Ticket\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/CreDeb")
 */
class CreDebController extends AbstractController
{
    /**
     * @Route("/crediter/{id}", name="crediter")
     */
    public function Credtier($id, UserRepository $userRepo, TicketRepository $ticketRepo, Request $request): Response
    {
        // $request->getSession()->invalidate();

        $limit = 18;
        $page = (int)$request->query->get("page", 1);
        $filters = $request->get("carnets");

        $tickets = $ticketRepo->getPaginatedInActivateTickets($page, $limit, $filters);
        $total = $ticketRepo->getTotalTickets($filters);

        $form = $this->createForm(SearchUserType::class);
        $search = $form->handleRequest($request);
        $users = $userRepo->getuserId($id);
        // $tickets = $ticketRepo->findTiket();
        if ($form->isSubmitted() && $form->isValid()) {
            $tickets = $ticketRepo->searchTicket($search->get('mots')->getData());
            if ($tickets == null) {
                $this->addFlash('message', 'Aucun carnet trouvé');
            }
        }
        $form = $form->createView();
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView(
                    'cre_deb/crediter.html.twig',
                    compact('tickets', 'users', 'total', 'limit', 'page', 'form')
                )
            ]);
        }
        return $this->render(
            "cre_deb/crediter.html.twig",
            compact('tickets', 'users', 'total', 'limit', 'page', 'form')
        );
    }

    /**
     * @Route("/panier/", name="panier")
     */
    public function panierIndex(TicketService $ticketService, UserRepository $userRepo, Request $request)
    {   
        $total = $ticketService->getTotal();

        $form = $this->createFormBuilder()
            ->add('total', IntegerType::class,[
                'data' => $total,
                'label' => false,
                'required' => false
            ])
            ->add('username', TextType::class,[
                'required' => true,
            ])
            ->add('Valider', SubmitType::class,[
                'attr' => [
                    'class' => 'btn-Edit'
                ]
            ])

            ->getForm();
        $totaux = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $total = $totaux->get('total')->getData();
            $username = $totaux->get('username')->getData();
            $insertion = $userRepo->InsertIntoUser($username, $total);
            if (!$insertion) 
            {
                return new Response('tsy mety zany a!!!!');
            }
            else {
                $request->getSession()->invalidate();
                return $this->redirectToRoute('user');
            }
            // $userRepo->Totaux($idUser, $total);
            
        }
        return $this->render('cre_deb/panier.html.twig',[
            'items' => $ticketService->getFullCart(),
            'total' => $ticketService->getTotal(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/crediter/{idUser}/add/{numero}", name="addTicketToUser")
     */
    public function add($idUser, $numero, TicketService $ticketService)
    {
        $ticketService->getFullCart();
        $ticketService->add($numero);

        return $this->redirectToRoute("panier", compact('idUser'));
    }


    // /**
    //  * @Route("/crediter/{idUser}/panier/add/{numero}", name="addPanier")
    //  */
    // public function addPanier($idUser, $numero, Request $request, TicketRepository $ticketRepo)
    // {

    //     $session = $request->getSession();
    //     $panier = $session->get('panier', []);
    //     if (!empty($panier[$numero])) {
    //         $panier[$numero]++;
    //     } else {
    //         $panier[$numero] = 1;
    //     }
    //     $ticketRepo->desactiveTicket($numero);
    //     $session->set('panier', $panier);
    //     // dd($panier);
    //     // return new Response("Ajouter");
    //     return $this->render("cre_deb/addSuccess.html.twig", compact('idUser'));
    //     // return $this->redirectToRoute('CrediterPanier', compact('idBadge'));
    // }

    // /**
    //  * @Route("/crediter/{idUser}/panier", name="crediterPanier")
    //  */
    // public function panier($idUser, TicketRepository $ticketRepo, SessionInterface $session, UserRepository $userRepo, Request $request): Response
    // {

    //     $panier = $session->get('panier', []);
    //     $dataTiket = [];

        // foreach ($panier as $numero => $quantity) {
        //     $dataTiket[] = [
        //         'tikets' => $ticketRepo->FindNumero($numero),
        //         'tikets' => $ticketRepo->findOneBy(array(
        //             'numero' => $numero
        //         )),
        //         'quantity' => $quantity
        //     ];
        // }
        // dd($dataTiket);
        // $total = 0;
        // foreach ($dataTiket as $item) {
        //     $total += $item['tikets']->getValue() * $item['quantity'];
        // }

    //     // $form = $this->createForm(TotalFormType::class);
    //     // $user = new user();

    //     $form = $this->createFormBuilder()
    //         ->add('total', IntegerType::class, [
    //             'data' => $total,
    //             'label' => false,
    //             'required' => false,
    //         ])
    //         ->add('Valider', SubmitType::class, [
    //             'attr' => [
    //                 'class' => 'btn-Edit'
    //             ]
    //         ])
    //         ->getForm();
    //     $totaux = $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $total = $totaux->get('total')->getData();
    //         $userRepo->Totaux($idUser, $total);
    //         // $em->persist($newtotal);
    //         // $em->flush();
    //         $request->getSession()->invalidate();
    //         return $this->redirectToRoute('user');
    //     }
    //     // dd($totalItem);
    //     return $this->render(
    //         'user/panier.html.twig',
    //         [
    //             'dataTikets' => $dataTiket,
    //             'total' => $total,
    //             'form' => $form->createView()
    //         ]
    //     );
    // }
}
