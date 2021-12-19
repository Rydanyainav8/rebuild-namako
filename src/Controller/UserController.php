<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchUserType;
use App\Form\UsersType;
use App\Repository\GenderRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user")
     */
    public function index(UserRepository $userRepo, GenderRepository $genderRepo, Request $request, QrCodeController $qrcode): Response
    {

        $form = $this->createForm(SearchUserType::class);

        $filters = $request->get("genders");

        $limit = 10;
        $page = (int)$request->query->get("page", 1);
        $users = $userRepo->getPaginatedusers($page, $limit, $filters);
        $search = $form->handleRequest($request);
        $total = $userRepo->getTotalusers($filters);
        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepo->search($search->get('mots')->getData());
            if ($users == null) {
                $this->addFlash('message', 'Aucun resultat trouvé');
            }
        }
        // dd($total);

        $genders = $genderRepo->findAll();

        // return $this->render('admin/user/index.html.twig', [
        //     'users' => $users,
        //     'form' => $form->createView(),
        //     'total' => $total,
        //     'Gender' => $gender,
        //     'limit' => $limit,
        //     'page' => $page,
        // ]);
        $form = $form->createView();

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView(
                    'user/index.html.twig',
                    compact('users', 'total', 'limit', 'page', 'form')
                )
            ]);
        }

        return $this->render(
            'user/index.html.twig',
            compact('users', 'total', 'limit', 'page', 'genders', 'form')
        );
    }

    /**
     * @Route("/create", name="createUser")
     */
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $user = new User();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imazfile */
            // $imazfile = $form->get('image')->getData();
            $imazfile = $form['imageFile']->getData();
            if ($imazfile) {
                $originalFileName = pathinfo($imazfile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $slugger->slug($originalFileName);
                $newFilename = $safeFileName . '-' . uniqid() . '.' . $imazfile->guessExtension();

                $imazfile->move(
                    $this->getParameter('imaz_directory'),
                    $newFilename
                );

                $user->setPdp($newFilename);
            }
            // $em = $this->getDoctrine()->getManager();
            // $codeqr = $qrcode->qrcode($url);
            $user->setMatricule(date("YmdHis"));
            $user->setSolde(0);
            $user->setQr(0);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user');
        }

        return $this->render('user/createEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/edit/{id}", name="edituser")
     */
    public function edit(user $user, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imazfile */
            // $imazfile = $form->get('image')->getData();
            $imazfile = $form['imageFile']->getData();
            if ($imazfile) {
                $originalFileName = pathinfo($imazfile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $slugger->slug($originalFileName);
                $newFilename = $safeFileName . '-' . uniqid() . '.' . $imazfile->guessExtension();

                $imazfile->move(
                    $this->getParameter('imaz_directory'),
                    $newFilename
                );

                $user->setPdp($newFilename);
            }
            // $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render('user/createEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
     * @Route("/supprimer/{id}", name="supprimerUser")
     */
    public function supprimer(User $User)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($User);
        $em->flush();
        $this->addFlash('message', 'Supprimeé avec succès');
        return $this->redirectToRoute('user');
    }
    
    /**
     * @Route("/crediter/{id}", name="crediter")
     */
    public function Credtier($id, UserRepository $userRepo ,TicketRepository $ticketRepo, Request $request): Response
    {
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
            $tickets = $ticketRepo->search($search->get('mots')->getData());
            if ($tickets == null) {
                $this->addFlash('message', 'Aucun carnet trouvé');
            }
        }
        $form = $form->createView();
        if ($request->get('ajax'))
        {
            return new JsonResponse([
                'content' => $this->renderView('user/crediter.html.twig',
                compact('tickets', 'users', 'total', 'limit', 'page', 'form')
                )
                ]);
        }
        return $this->render("user/crediter.html.twig", 
            compact('tickets', 'users', 'total', 'limit', 'page', 'form')
        );
    }
    /**
     * @Route("/crediter/{idUser}/panier", name="CrediterPanier")
     */
    public function panier($idUser, TicketRepository $ticketRepo, SessionInterface $session, UserRepository $userRepo, Request $request): Response
    {
        
        $panier = $session->get('panier', []);
        $dataTiket = [];
        
        foreach ($panier as $numero => $quantity)
        {
            $dataTiket[] = [
                'tikets' => $ticketRepo->FindNumero($numero),
                // 'tikets' => $ticketRepo->findOneBy(array(
                //     'numero' => $numero
                // )),
                'quantity' => $quantity
            ];
        }
        // dd($dataTiket);
        $total = 0;
        foreach($dataTiket as $item)
        {
            $total += $item['tikets']->getValue() * $item['quantity'];
        }
        
        // $form = $this->createForm(TotalFormType::class);
        // $user = new user();

        $form = $this->createFormBuilder()
                    ->add('total', IntegerType::class,[
                        'data' => $total,
                        'label' => false,
                        'required' => false,
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
            $userRepo->Totaux($idUser, $total);
            // $em->persist($newtotal);
            // $em->flush();
            return $this->redirectToRoute('user');
        }
        // dd($totalItem);
        return $this->render('user/panier.html.twig',
        [
            'dataTikets' => $dataTiket,
            'total' => $total,
            'form' => $form->createView()
        ]);
    }
}