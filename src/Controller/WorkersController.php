<?php

namespace App\Controller;

use App\Entity\Workers;
use App\Form\WorkersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/workers")
 */
class WorkersController extends AbstractController
{
    // /**
    //  * @Route("/", name="workers_index", methods={"GET"})
    //  */
    // public function index(): Response
    // {
    //     $workers = $this->getDoctrine()
    //         ->getRepository(Workers::class)
    //         ->findAll();

    //     return $this->render('workers/index.html.twig', [
    //         'workers' => $workers,
    //     ]);
    // }

    // /**
    //  * @Route("/new", name="workers_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $worker = new Workers();
    //     $form = $this->createForm(WorkersType::class, $worker);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($worker);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('workers_index');
    //     }

    //     return $this->render('workers/new.html.twig', [
    //         'worker' => $worker,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{workerId}", name="workers_show", methods={"GET"})
    //  */
    // public function show(Workers $worker): Response
    // {
    //     return $this->render('workers/show.html.twig', [
    //         'worker' => $worker,
    //     ]);
    // }

    // /**
    //  * @Route("/{workerId}/edit", name="workers_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, Workers $worker): Response
    // {
    //     $form = $this->createForm(WorkersType::class, $worker);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('workers_index');
    //     }

    //     return $this->render('workers/edit.html.twig', [
    //         'worker' => $worker,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{workerId}", name="workers_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, Workers $worker): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$worker->getWorkerId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($worker);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('workers_index');
    // }

    /**
     * @Route("/{workerId}/edit", name="workers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Workers $worker): Response
    {
        $form = $this->createForm(WorkersType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('workers_index');
        }

        return $this->render('workers/edit.html.twig', [
            'worker' => $worker,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/whoami", name="who_am_i", methods={"GET"})
     */

     public function whoami(): Response
     {
         $user = $this->getUser();

         $userObj = [
             'id' => $user->getId(),
             'name' =>$user->getWorkerName(),
             'branch' =>$user->getBranch()->getBranchName()

         ];
         return new JsonResponse($userObj);
     }
}
