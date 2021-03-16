<?php

namespace App\Controller;

use App\Entity\Shift;
use App\Form\ShiftType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ShiftRepository;
use App\Repository\ShiftTypeRepository;
use App\Repository\BranchRepository;
use App\Repository\WorkersRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/shift")
 */
class ShiftController extends AbstractController
{
    /**
     * @Route("/pool", name="shift_pool", methods={"GET"})
     */
    public function pool( ShiftRepository $shiftRepo): Response
    { /*Function to bring ALL shifts on swapping*/
        $shifts = $shiftRepo->findBy(
            ['swapping' => 1]
        );
      
       
        return new JsonResponse($this-> serialize($shifts));
    }
    /**
     * @Route("/new", name="shift_new", methods={"POST"})
     */
    public function newShift( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo ): Response
    {/*function to create shifts*/
        $userLogged  = $this->getUser();                 /* funcion que devuele el usuario que esta logeado */
        $bodyRequest = $request->getContent();           /* sacamos el contenido de la request del POST */
        $shiftObjs   = json_decode($bodyRequest, true);  /*second parameter transforms it in asociative array*/

        foreach ($shiftObjs as $shiftObj ){                /*para mas de un caso hacemos un bucle para cada caso*/

      
            $shift = new Shift();
            $shift->setStartShift(new \DateTime($shiftObj['startShift']));/* testear fallo al introducir fechas */
            $shift->setEndShift(new \DateTime($shiftObj['endShift']));/* testear fallo al introducir fechas */
        
            $shift->setBranch($userLogged->getBranch()); /* testear posible fallo. posiblemente depurado */

            $workerObj = $wRepo->find($shiftObj['worker_id']); /* busca por el id que recibe del front para crear el objeto*/
            $shiftTypeObj = $sTRepo->find($shiftObj['shiftType']);
            
            $shift->setWorker($workerObj);
            $shift->setShiftType($shiftTypeObj);
            $shift->setSwappable($shiftObj['swappable']);
            $shift->setSwapping($shiftObj['swapping']);
            
            $em->persist($shift);
        }
        $em->flush();

        $answer = [
           'message' => "all data sent, saved in the data base"
        ];
    
        return new JsonResponse($answer);
    }
   /**
     * @Route("/rota", name="shift_rota", methods={"GET"})
     */
    public function rota( ShiftRepository $shiftRepo, WorkersRepository $wRepo): Response
    {/*Funcion que deuvleve TODOS los turnos del trabajador logueado */

        $userLogged  = $this->getUser();

        $shifts = $shiftRepo->findBy(
            ['worker' => $userLogged->getId()]
        );      

        return new JsonResponse ($this-> serialize($shifts));
    }

     /**
     * @Route("/rota", name="shift_rota_POST", methods={"POST"})
     */
    public function rangeRota( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion que deuvleve los turnos con los rangos de fecha */
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateFromjsn']);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateTojsn']);
      
        $shifts = $sRepo->getRotaRange($em, $dateFromctlr, $dateToctlr);

        return new JsonResponse( $this-> serialize($shifts));
    }

     /**
     * @Route("/rotauser", name="shift_rota_user", methods={"POST"})
     */
    public function rotaUser( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion que devuleve los turnos con los rangos de fecha del usuario logueado */
        $userLogged  = $this->getUser();
        // $user = $sRepo->findBy(['worker' => $userLogged->getId()]);
        $userId = $userLogged->getId(); 
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateFromjsn']);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateTojsn']);
      
        $shifts = $sRepo->getRotaRangeWorker($em, $dateFromctlr, $dateToctlr, $userId);

        return new JsonResponse( $this-> serialize($shifts));
    }
    /**
     * @Route("/swapping/{id}", name="user_swapping", methods={"PUT"})
     */
    public function swapping($id,Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo) : Response
    {/**/

        //necesita verificacion de que el usuario es el dueño de este turno
        $shift = $sRepo->find($id);
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $shift->setSwapping($reqArray['swapping']);
        $em->persist($shift);
        $em->flush();
        
        return new JsonResponse(['answer'=> 'change realized successfully']);
    }



   private function serialize($arrayShifts)
   {/*funcion para serializar turnos*/
    $shiftarray = [];

    foreach ($arrayShifts as $shift){
        
        $shiftObj = [
            'startShift' => $shift->getStartShift(),
            'endShift' => $shift->getEndShift(),
            'swapping' => $shift->getSwapping(),
            'swappable' => $shift->getSwappable(),
            'branch' => $shift->getBranch()->getBranchName(),
            'shiftType' => $shift->getShiftType()->getShiftType(),
            'worker' => $shift->getWorker()->getWorkerName(),
            'job' => $shift->getWorker()->getJob()->getJob(),


        ];
        $shiftarray[] = $shiftObj;          //to push it in shiftArray
    }
    return $shiftarray;
   }





























    // /**
    //  * @Route("/", name="shift_index", methods={"GET"})
    //  */
    // public function index(): Response
    // {
    //     $shifts = $this->getDoctrine()
    //         ->getRepository(Shift::class)
    //         ->findAll();

    //     return $this->render('shift/index.html.twig', [
    //         'shifts' => $shifts,
    //     ]);
    // }

    // /**
    //  * @Route("/mg/new", name="shift_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $shift = new Shift();
    //     $form = $this->createForm(ShiftType::class, $shift);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($shift);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('shift_index');
    //     }

    //     return $this->render('shift/new.html.twig', [
    //         'shift' => $shift,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{shiftId}", name="shift_show", methods={"GET"})
    //  */
    // public function show(Shift $shift): Response
    // {
    //     return $this->render('shift/show.html.twig', [
    //         'shift' => $shift,
    //     ]);
    // }

    // /**
    //  * @Route("/{shiftId}/edit", name="shift_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, Shift $shift): Response
    // {
    //     $form = $this->createForm(ShiftType::class, $shift);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('shift_index');
    //     }

    //     return $this->render('shift/edit.html.twig', [
    //         'shift' => $shift,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{shiftId}", name="shift_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, Shift $shift): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$shift->getShiftId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($shift);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('shift_index');
    // }
}
