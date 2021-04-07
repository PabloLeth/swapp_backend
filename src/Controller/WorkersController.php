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
