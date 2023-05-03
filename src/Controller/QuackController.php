<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Repository\QuackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuackController extends AbstractController
{

    #[Route('/', name: 'app', methods: ['GET', 'HEAD'])]
    public function base()
    {
         return $this->render('base.html.twig', [''
        ]);
    }
    #[Route('/quack', name: 'app_quack', methods: ['POST', 'GET', 'HEAD'])]
    public function index(QuackRepository $quackRepository)
    {
        $quacks = $quackRepository -> findAll();
        return $this->render('quack/index.html.twig', [
            'quacks' => $quacks,
        ]);
    }

    #[Route('/quack/create', name: 'quack_create')]
    public function createQuack(Request $request, QuackRepository $quackRepository): Response
    {
        $quack = new Quack();
        $quack->setContent('Write a quack');
        $quack->setCreatedAt(new \DateTime(''));

        $form = $this->createFormBuilder($quack)
            ->add('content', TextType::class)
            ->add('created_at', \Symfony\Component\Form\Extension\Core\Type\DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Quack'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $quack = $form->getData();
            $quackRepository->save($quack, true);
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_quack');
        }

        return $this->render('quack/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/quack/{id}', name: 'quack_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for id ' . $id
            );
        }
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    #[Route('/quack/remove/{id}', name: 'quack_remove')]
    public function remove(EntityManagerInterface $entityManager, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);
        $entityManager->remove($quack);
        $entityManager->flush();
        return $this->redirectToRoute('app_quack', []);
    }


    #[Route('/quack/edit/{id}', name: 'quack_edit')]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);
        $quack->setContent('Modify');
        $quack->setCreatedAt(new \DateTime(''));

        $form = $this->createFormBuilder($quack)
            ->add('content', TextType::class)
            ->add('created_at', \Symfony\Component\Form\Extension\Core\Type\DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Modify Quack'])
            ->getForm();

        $form->handleRequest($request);

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for id '.$id
            );
        }
        $entityManager->flush();

        return $this->render('quack/edit.html.twig', [
            'form' => $form,
        ]);
    }



}
