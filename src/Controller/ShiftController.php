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
     * @Route("/manager/new", name="shift_new", methods={"POST"})
     */
    public function newShift( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo ): Response
    {/*function to create shifts*/
        $userLogged  = $this->getUser();                 /* funcion que devuele el usuario que esta logeado */
        $bodyRequest = $request->getContent();           /* sacamos el contenido de la request del POST */
        $shiftObjs   = json_decode($bodyRequest, true);  /*second parameter 'true' transforms it in asociative array*/

        foreach ($shiftObjs as $shiftObj ){                /*para mas de un caso hacemos un bucle para cada caso*/

                                                            /*necesito verificar que vienen startshift y end shift del front*/
            $shift = new Shift();
            $shift->setStartShift(new \DateTime($shiftObj['startShift']));/* testear fallo al introducir fechas ex:"2021-03-12 12:00:00.000"*/
            $shift->setEndShift(new \DateTime($shiftObj['endShift']));/* testear fallo al introducir fechas */
            $shift->setDate(new \DateTime($shiftObj['date']));
            $shift->setBranch($userLogged->getBranch()); /* testear posible fallo. posiblemente depurado */

            $workerObj = $wRepo->find($shiftObj['worker_id']); /* busca por el id que recibe del front para crear el objeto*/
            $shiftTypeObj = $sTRepo->find($shiftObj['shiftType']); /* necesito que el front me mande el id, 1 o 2 */
            
            $shift->setWorker($workerObj);
            $shift->setShiftType($shiftTypeObj);
            $shift->setSwappable($shiftObj['swappable']);   /* añadir datos por defecto*/
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
    {/*Funcion que deuvleve TODOS los TURNOS del USUARIO LOGUEADO           ELIMINAR??*/

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
    {/*Funcion que deuvleve los TURNOS con los RANGOS DE FECHA              ELIMINAR?? */
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $dateFromAddedTime = $reqArray['dateFromjsn']." 00:00:00.000000";
        $dateToAddedTime = $reqArray['dateTojsn']." 23:59:59.000000";
        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$dateFromAddedTime);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateToAddedTime);

        // $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateFromjsn']);
        // $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateTojsn']);
       
      
        $shifts = $sRepo->getRotaRange($em, $dateFromctlr, $dateToctlr);

        return new JsonResponse( $this-> serialize($shifts));
    }

     /**
     * @Route("/rotauser", name="shift_rota_user", methods={"POST"})
     */
    public function rotaUser( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion que devuleve los TURNOS con los RANGOS DE FECHA del USUARIO LOGUEADO */
        $userLogged  = $this->getUser();
        // $user = $sRepo->findBy(['worker' => $userLogged->getId()]);
        $userId = $userLogged->getId(); 
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        /*      para cuando es solo la fecha sin hora*/ 
        $dateFromAddedTime = $reqArray['dateFromjsn']." 00:00:00.000000";
        $dateToAddedTime = $reqArray['dateTojsn']." 23:59:59.000000";
        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$dateFromAddedTime);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateToAddedTime);

        // $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateFromjsn']);
        // $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$reqArray['dateTojsn']);
      
        $shifts = $sRepo->getRotaRangeWorker($em, $dateFromctlr, $dateToctlr, $userId);

        return new JsonResponse( $this-> serialize($shifts));
    }

    /**
     * @Route("/swapping/{id}", name="user_swapping", methods={"PUT"})
     */
    public function swapping($id,Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo) : Response
    {/*funcion que cambia el estado de swapping del shift.id que se envia por PUT*/

        $userLogged  = $this->getUser();
        $userId = $userLogged->getId();
        //?? necesitará verificacion de que el usuario es el dueño de este turno
        $shift = $sRepo->find($id);
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);
        $isSwappable = $shift->getSwappable();
        $isSwapping = $shift->getSwapping();
      
        // devuelve 403 si no hay permisos o alguien introduce un id a proposito
        if ($isSwappable == 0 || $reqArray['swapping'] == 0 && $isSwapping == 0 ){
            return new JsonResponse(['answer'=> 'there is no permission, contact with your manager'], 403);
        }
        //condicional para tomar el turno
        if ($reqArray['swapping'] == 0){ //?? probablemente añadir doble condicional para comprobar el estado de swapping previo
            $userLogged = $this->getUser();
            $userId = $userLogged->getId();
            $shift->setWorker($userLogged);
            $shift->setSwappable(0);
             // quizas añadir para cancelar el volver a cambiarlo una vez tomado aqui: $shift->setSwappable(0);
        }
        $shift->setSwapping($reqArray['swapping']);
        $em->persist($shift);
        $em->flush();
        
        return new JsonResponse(['answer'=> 'change completed successfully']);
    }

     /**
     * @Route("/manager/rotacheck", name="shift_mg_rotacheck", methods={"POST"})
     */
    public function rotaCheck( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion de MANAGER que devuleve los TURNOS con los RANGOS DE FECHA del BRANCH del MANAGER LOGUEADO  EnCoNsTrUcCiOn*/
                        
        $userLogged  = $this->getUser();
      
        $userBranch = $userLogged->getBranch(); 
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $dateFromAddedTime = $reqArray['dateFromjsn']." 00:00:00.000000";
        $dateToAddedTime = $reqArray['dateTojsn']." 23:59:59.000000";
        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$dateFromAddedTime);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateToAddedTime);
    
        
        $shifts = $sRepo->getRotaBranch($em, $dateFromctlr, $dateToctlr, $userBranch);
        if(count($shifts) === 0){
            $workers = $wRepo->getWorkersBranch($em,$userBranch);
            $answer = [];
            foreach($workers as $worker){
            
                $answer[] = [
                    'worker' => $worker->getWorkerName(),
                    'id' => $worker->getId()
            ];
            }

            return new JsonResponse($answer, 240); //catch 240 para mandar una rota vacia
        }
                     

                        
        $workers = $wRepo->getWorkersBranch($em, $userBranch);
        $answer = [];                
        $shifts = [];
          foreach($workers as $worker){
            $worker->getId();
            $workerShifts = $sRepo->getRotaRangeWorker($em, $dateFromctlr, $dateToctlr, $worker);

            foreach ($workerShifts as $workerShift){
            
                $shiftWorkerObj = [
                    
                'id' => $workerShift->getId(),
                'startShift' => $workerShift->getStartShift(),
                'endShift' => $workerShift->getEndShift(),
                'shiftType' => $workerShift->getShiftType()->getShiftType()   
                ];
                $shifts[] = $shiftWorkerObj;    

            }
            $workerObj = [
            'worker' => $worker->getWorkerName(),
            'id' => $worker->getId(), /* no testeado */
            'shifts' => $shifts
            ];
            $answer[] = $workerObj;
            $shifts= [];
          } 
             
        return new JsonResponse($answer);
    }
    
    /**
     * @Route("/manager/rotachange", name="shift_mg_rotachange", methods={"POST"})
     */
    public function rotaChange( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion de MANAGER que update los TURNOS con los RANGOS DE FECHA del BRANCH del MANAGER LOGUEADO */
        
    }
   private function serialize($arrayShifts)
   {/*funcion para serializar TURNOS*/
    $shiftarray = [];

    foreach ($arrayShifts as $shift){
        
        $shiftObj = [
            'startShift' => $shift->getStartShift(),
            'endShift' => $shift->getEndShift(),
            'swapping' => $shift->getSwapping(),
            'swappable' => $shift->getSwappable(),
            'active' => $shift->getActive(),
            'branch' => $shift->getBranch()->getBranchName(),
            'shiftType' => $shift->getShiftType()->getShiftType(),
            'worker' => $shift->getWorker()->getWorkerName(),
            'job' => $shift->getWorker()->getJob()->getJob(),
            'id' => $shift->getId()

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
