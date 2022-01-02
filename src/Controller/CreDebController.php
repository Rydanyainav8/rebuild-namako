<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MontantDebType;
use App\Form\SearchUserType;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use App\Service\Ticket\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CreDebController extends AbstractController
{
    /**
     * @Route("/crediter/{idUser}", name="crediter")
     */
    public function Credtier($idUser, UserRepository $userRepo, TicketRepository $ticketRepo, Request $request): Response
    {
        // $request->getSession()->invalidate();

        $limit = 18;
        $page = (int)$request->query->get("page", 1);
        $filters = $request->get("carnets");
        $tickets = $ticketRepo->getPaginatedInActivateTickets($page, $limit, $filters);
        $total = $ticketRepo->getTotalTickets($filters);
        $form = $this->createForm(SearchUserType::class);
        $search = $form->handleRequest($request);
        $users = $userRepo->getuserId($idUser);

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
     * @Route("/crediter/panier/{idUser}", name="panier")
     */
    public function panierIndex($idUser, TicketService $ticketService, UserRepository $userRepo, Request $request)
    {
        $total = $ticketService->getTotal();

        $form = $this->createFormBuilder()
            ->add('total', IntegerType::class, [
                'data' => $total,
                'label' => false,
                'required' => false
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-Edit'
                ]
            ])
            // ->add('Matricule', TextType::class,[
            //     'data' => $idMatricule
            // ])

            ->getForm();
        $totaux = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $total = $totaux->get('total')->getData();
            // $matricule = $totaux->get('Matricule')->getData();
            // $totaux->get('username')->getData();
            $userRepo->InsertIntoUser($idUser, $total);
            // if (!$insertion) 
            // {
            //     return new Response('Something wrong');
            // }
            // else {
            //     // $request->getSession()->invalidate();
            // }
            // $userRepo->Totaux($idUser, $total);

            // $request->getSession()->invalidate(1);
            // $cls = $request->getSession()->clear();

            return $this->redirectToRoute('app_logout');
        }
        return $this->render('cre_deb/panier.html.twig', [
            'items' => $ticketService->getFullCart(),
            'total' => $ticketService->getTotal(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/crediter/{idUser}/add/{numero}", name="addTicketToUser")
     */
    public function add($idUser, $numero, TicketService $ticketService, TicketRepository $ticketRepo, Session $session)
    {
        $ticketService->getFullCart();

        $ticketService->add($numero);
        $ticketRepo->desactiveTiket($numero);
        return $this->render("cre_deb/addSuccess.html.twig", compact('idUser'));
        // return $this->redirectToRoute("panier", compact('idUser')); 
    }

    /**
     * @Route("/debiter/{id}", name="debiter")
     */
    public function debiter(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MontantDebType::class);
        $montant = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $soldes = $badgeRepo->montant($montant->get('somme')->getData());
            $somme = $montant->get('somme')->getData();
            $currentSolde = $user->getSolde();
            $newSomme = $currentSolde - $somme;
            if ($newSomme < 0) {
                $this->addFlash('error', 'La solde ne doit jamais être négative');
                return $this->redirectToRoute('user');
            }
            $user->setSolde($newSomme);

            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render("cre_deb/debiter.html.twig", [
            'form' => $form->createView(),
        ]);
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
