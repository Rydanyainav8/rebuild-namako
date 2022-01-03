<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AdminRepository $adminRepo): Response
    {
        $admin = $adminRepo->findAll();
        return $this->render('admin/index.html.twig', [
            'admins' => $admin
        ]);
    }
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $admin = new Admin();

        $form = $this->createForm(AdminType::class, $admin);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $hash = $hasher->hashPassword($admin, $admin->getPassword());
            $admin->setPassword($hash);

            $em->persist($admin);
            $em->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Admin $admin)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();
        return $this->redirectToRoute('admin_index');
    }
}
