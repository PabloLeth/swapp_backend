<?php

namespace App\Controller;

use App\Entity\Shift;
use App\Form\ShiftType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shift")
 */
class ShiftController extends AbstractController
{
    /**
     * @Route("/", name="shift_index", methods={"GET"})
     */
    public function index(): Response
    {
        $shifts = $this->getDoctrine()
            ->getRepository(Shift::class)
            ->findAll();

        return $this->render('shift/index.html.twig', [
            'shifts' => $shifts,
        ]);
    }

    /**
     * @Route("/new", name="shift_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $shift = new Shift();
        $form = $this->createForm(ShiftType::class, $shift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shift);
            $entityManager->flush();

            return $this->redirectToRoute('shift_index');
        }

        return $this->render('shift/new.html.twig', [
            'shift' => $shift,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shiftId}", name="shift_show", methods={"GET"})
     */
    public function show(Shift $shift): Response
    {
        return $this->render('shift/show.html.twig', [
            'shift' => $shift,
        ]);
    }

    /**
     * @Route("/{shiftId}/edit", name="shift_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Shift $shift): Response
    {
        $form = $this->createForm(ShiftType::class, $shift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shift_index');
        }

        return $this->render('shift/edit.html.twig', [
            'shift' => $shift,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shiftId}", name="shift_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Shift $shift): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shift->getShiftId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($shift);
            $entityManager->flush();
        }

        return $this->redirectToRoute('shift_index');
    }
}
