<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("admin/category", name="category_admin",methods={"GET"})
     */
    public function admin(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/admin.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/category/add", name="category_add")
     */
    public function add(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            if ($category->getUsers() != null) {
                $users = $category->getUsers();
                foreach ($users as $user) {
                    $user->addCategory($category);
                }
            }
            $entityManager->flush();

            $this->addFlash("success", "add.success");
            return $this->redirectToRoute('category_admin');
        }
        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/admin/category/edit/{category}", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
     #  if ($category->getUsers() != null) {
     #      $usersInit = $category->getUsers();
     #  }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        #   foreach ($usersInit as $user) {
        #       $category->removeUser($user);
        #   }
        #TODO: Permettre d'enlever des utilisateurs des categories
            if ($category->getUsers() != null) {
                $users = $category->getUsers();
                foreach ($users as $user) {
                    $user->addCategory($category);
                }
            }

            $this->getDoctrine()->getManager()->persist($category);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "edit.success");
            return $this->redirectToRoute("category_edit", ["category" => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/admin/category/delete/{category}", name="category_delete")
     */
    public function delete(Request $request, Category $category): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash("success", "delete.success");
        return $this->redirectToRoute('category_admin');
    }

    /**
     * @Route("/category/request",name="category_request")
     */
    public function requestCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/request.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/request/{id}",name="category_send_request")
     */
    public function sendRequestForCategory(MailerInterface $mailer,CategoryRepository $categoryRepository, int $id)
    {

        $category = $categoryRepository->find($id);

        if($this->getUser()->getCategories()->contains($category))
        {
            $this->addFlash("warning", "already.part-of-category");

            return $this->render('category/request.html.twig', [
                'categories' => $categoryRepository->findAll(),
            ]);
        }

            $users = $category->getUsers();
        foreach ($users as $user) {
            if (in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_ADMIN_RESTRICTED', $user->getRoles())) {
                $email = (new Email())
                    ->from($this->getUser()->getEmail())
                    ->to($user->getEmail())
                    ->subject('Requête pour rejoindre la catégorie ' . $category->getName())
                    #->htmlTemplate('email/report.html.twig')
                    ->text("L'utilisateur ".$this->getUser()->getUsername()." a demandé a rejoindre la catégorie "
                        .$category->getName());
                if ($user->getIsAllowedEmails())
                {
                    $mailer->send($email);
                }
                $notification = $user->prepareNotification('Requête pour rejoindre la catégorie ' . $category->getName() . " : "
                    . "L'utilisateur ".$this->getUser()->getUsername()." a demandé a rejoindre la catégorie \n Accepter ?  - Refuser ?");
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($notification);
                $entityManager->flush();

            }
        }
        $this->addFlash("success", "request.success");

        return $this->render('category/request.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/request/{id}/accept",name="category_accept_request")
     */
    public function acceptRequest()
    {

    }

    /**
     * @Route("/category/request/{id}/deny",name="category_deny_request")
     */
    public function denyRequest()
    {

    }


}
