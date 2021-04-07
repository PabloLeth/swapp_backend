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
    public function pool(  EntityManagerInterface $em, ShiftRepository $shiftRepo): Response
    { /*Function to bring ALL shifts on swapping*/
        $userLogged  = $this->getUser();
        $userId = $userLogged->getId(); 
        $userJob = $userLogged->getJob();

        $shiftsOff = $shiftRepo->findDaysOff($em, $userId);
        $pool = $shiftRepo->findSwappingByJobId($em, $userJob, $userId);
        
       $shifts = [];
        foreach($pool as $spool){

            $datespool = $spool->getDate();
            $shiftTypepool = $spool->getShiftType();

            foreach($shiftsOff as $shiftOff){
       
                $dateOff = $shiftOff->getDate();
                $shiftTypeOff = $shiftOff->getShiftType();

                if ($datespool == $dateOff && $shiftTypepool == $shiftTypeOff){
                    $shifts [] = $spool ;
                }
                
        }
        }


        return new JsonResponse($this-> serialize($shifts));
    }
    /**
     * @Route("/manager/new", name="shift_new", methods={"POST"})
     */
    public function newShift( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo ): Response
    {/*function to create shifts*/
        $userLogged  = $this->getUser();             
        $bodyRequest = $request->getContent();          
        $shiftsObj   = json_decode($bodyRequest, true); 

        foreach ($shiftsObj as $workerObj ){                
            $worker = $wRepo->find($workerObj['id']);
          
            foreach ($workerObj['shifts'] as $shift){
                if (!isset($shift['id'])){
              
                    $newshift = new Shift();
                    if(  isset($shift['startShift'])  ){
                        $newshift->setStartShift(new \DateTime($shift['startShift']));
                    }
                    if(  isset($shift['endShift'])  ){

                        $newshift->setEndShift(new \DateTime($shift['endShift']));
                    }
                    $newshift->setDate(new \DateTime($shift['date']));
                    $newshift->setBranch($userLogged->getBranch()); 
         
                  
                    $shiftTypeObj = $sTRepo->find($shift['shiftType']); 
                    
                    $newshift->setWorker($worker);
                    $newshift->setShiftType($shiftTypeObj);
                    $newshift->setActive($shift['active']);
                     $newshift->setSwappable(1);   
                     $newshift->setSwapping(0);
                    
                    $em->persist($newshift);
                }
           }

                                                            
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
    {/*Funcion que deuvleve TODOS los TURNOS del USUARIO LOGUEADO         */

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
    {/*Funcion que deuvleve los TURNOS con los RANGOS DE FECHA              */
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

        $dateFromAddedTime = $reqArray['dateFromjsn']." 00:00:00.000000";
        $dateToAddedTime = $reqArray['dateTojsn']." 23:59:59.000000";
        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$dateFromAddedTime);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateToAddedTime);

        
       
      
        $shifts = $sRepo->getRotaRange($em, $dateFromctlr, $dateToctlr);

        return new JsonResponse( $this-> serialize($shifts));
    }

     /**
     * @Route("/rotauser", name="shift_rota_user", methods={"POST"})
     */
    public function rotaUser( Request $request, EntityManagerInterface $em, WorkersRepository $wRepo, ShiftTypeRepository $sTRepo, ShiftRepository $sRepo): Response
    {/*Funcion que devuleve los TURNOS con los RANGOS DE FECHA del USUARIO LOGUEADO */
        $userLogged  = $this->getUser();
        
        $userId = $userLogged->getId(); 
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);

       
        $dateFromAddedTime = $reqArray['dateFromjsn']." 00:00:00.000000";
        $dateToAddedTime = $reqArray['dateTojsn']." 23:59:59.000000";
        $dateFromctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u',$dateFromAddedTime);
        $dateToctlr = \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateToAddedTime);

        
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
     
        $shiftSent = $sRepo->find($id);
        $bodyRequest = $request->getContent();
        $reqArray = json_decode($bodyRequest, true);
        $isSwappable = $shiftSent->getSwappable();
        $isSwapping = $shiftSent->getSwapping();
      
        if ($isSwappable == 0 || $reqArray['swapping'] == 0 && $isSwapping == 0 ){
            return new JsonResponse(['answer'=> 'there is no permission, contact with your manager'], 403);
        }
   
        if ($reqArray['swapping'] == 0){ 
            $userLogged = $this->getUser();
            $userId = $userLogged->getId();

            $dateSS =  $shiftSent->getDate();
            $shiftTypeSS =  $shiftSent->getShiftType();
            $workerSS = $shiftSent->getWorker();

          
            $userShiftOff = $sRepo->findOneBy(array (
                'date' => $dateSS,
                'shiftType'=> $shiftTypeSS,
                'worker'=>  $userLogged
            )) ;

             $userShiftOff->setWorker( $workerSS );
            $shiftSent->setWorker( $userLogged );
            $shiftSent->setSwappable(0);
            
             $em->persist($userShiftOff);
        }
        $shiftSent->setSwapping($reqArray['swapping']);
        $em->persist($shiftSent);
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
                   
        $workers = $wRepo->getWorkersBranch($em, $userBranch);
        $answer = [];                
        $shifts = [];
          foreach($workers as $worker){
            $worker->getId();
            $workerShifts = $sRepo->getRotaRangeWorker($em, $dateFromctlr, $dateToctlr, $worker);

            foreach ($workerShifts as $workerShift){
            
                $shiftWorkerObj = [
                    
                'id' => $workerShift->getId(),
                'date' => $workerShift->getDate(),
                'startShift' => $workerShift->getStartShift(),
                'endShift' => $workerShift->getEndShift(),
                'shiftType' => $workerShift->getShiftType()->getId(),
                'active' => $workerShift->getActive(),
                'branch' => $workerShift->getBranch()->getId()
                ];
                $shifts[] = $shiftWorkerObj;    

            }
            $workerObj = [
            'worker' => $worker->getWorkerName(),
            'id' => $worker->getId(), 
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
   
   private function serialize($arrayShifts)
   {/*funcion para serializar TURNOS*/
    $shiftarray = [];

    foreach ($arrayShifts as $shift){
        
        $shiftObj = [
            'startShift' => $shift->getStartShift(),
            'endShift' => $shift->getEndShift(),
            'date' => $shift->getDate(),
            'swapping' => $shift->getSwapping(),
            'swappable' => $shift->getSwappable(),
            'active' => $shift->getActive(),
            'branch' => $shift->getBranch()->getBranchName(),
            'shiftType' => $shift->getShiftType()->getShiftType(),
            'worker' => $shift->getWorker()->getWorkerName(),
            'job' => $shift->getWorker()->getJob()->getJob(),
            'id' => $shift->getId()

        ];
        $shiftarray[] = $shiftObj;         
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
