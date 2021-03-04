<?php

namespace App\Controller;

use App\Entity\ShiftType;
use App\Form\ShiftTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shift/type")
 */
class ShiftTypeController extends AbstractController
{
    /**
     * @Route("/", name="shift_type_index", methods={"GET"})
     */
    public function index(): Response
    {
        $shiftTypes = $this->getDoctrine()
            ->getRepository(ShiftType::class)
            ->findAll();

        return $this->render('shift_type/index.html.twig', [
            'shift_types' => $shiftTypes,
        ]);
    }

    /**
     * @Route("/new", name="shift_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $shiftType = new ShiftType();
        $form = $this->createForm(ShiftTypeType::class, $shiftType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shiftType);
            $entityManager->flush();

            return $this->redirectToRoute('shift_type_index');
        }

        return $this->render('shift_type/new.html.twig', [
            'shift_type' => $shiftType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shiftTypeId}", name="shift_type_show", methods={"GET"})
     */
    public function show(ShiftType $shiftType): Response
    {
        return $this->render('shift_type/show.html.twig', [
            'shift_type' => $shiftType,
        ]);
    }

    /**
     * @Route("/{shiftTypeId}/edit", name="shift_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ShiftType $shiftType): Response
    {
        $form = $this->createForm(ShiftTypeType::class, $shiftType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shift_type_index');
        }

        return $this->render('shift_type/edit.html.twig', [
            'shift_type' => $shiftType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shiftTypeId}", name="shift_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ShiftType $shiftType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shiftType->getShiftTypeId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($shiftType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('shift_type_index');
    }
}
