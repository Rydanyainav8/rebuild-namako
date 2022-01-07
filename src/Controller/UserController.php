<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MontantDebType;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{

    /**
     * @Route("user/", name="user")
     */
    public function index(UserRepository $userRepo, GenderRepository $genderRepo, Request $request, QrCodeController $qrcode): Response
    {
        
        $form = $this->createForm(SearchUserType::class);
        $filters = $request->get("genders");

        // $request->getSession()->invalidate();

        $limit = 10;
        $page = (int)$request->query->get("page", 1);
        $users = $userRepo->getPaginatedusers($page, $limit, $filters);
        $search = $form->handleRequest($request);
        $total = $userRepo->getTotalusers($filters);
        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepo->searchUser($search->get('mots')->getData());
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
        // $request->getSession()->invalidate();

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
     * @Route("adminbg/create", name="createUser")
     */
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        $daty = date("YmdHis");
        // $fo = $form['password']->getData();
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

            $hash = $hasher->hashPassword($user, $daty);
            $user->setPassword($hash);
            $user->setMatricule($daty);
            $user->setSolde(0);
            $user->setRoles(array('ROLE_BG'));
            $user->setQr(0);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("adminbg/edit/{id}", name="edituser")
     */
    public function edit(User $user, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
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
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("adminbg/supprimer/{id}", name="supprimerUser")
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
     * @Route("adminbg/print/{id}", name="printBadge")
     */
    public function printBadge($id, UserRepository $userRepo): Response
    {
        $userbyid = $userRepo->find($id);

        return $this->render('user/print.html.twig', [
            'user' => $userbyid
        ]);
    }
    /**
     * @Route("adminbg/listTicket/{id}", name="listticket")
     */
    public function listTicket(User $user, UserRepository $userRepo): Response
    {
        $listTickets = $userRepo->getTicketByUserId($user);

        return $this->render('user/listTicket.html.twig', compact('listTickets'));
    }
}
