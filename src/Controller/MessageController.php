<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
/*                                                                  CONTROLLER IN PROGRESS */

    /**
     * @Route("/new", name="message_new", methods={"POST"})
     */
    public function newMessage( Request $request, EntityManagerInterface $em): Response
    {
        $userLogged = $this->getUser();
        $bodyRequest = $request->getContent();
        $messageObj = json_decode($bodyRequest);

        $message = new Message();
      
        $message->setBranch($messageObj->branch); /* testear posible fallo: $userLogged['branch'] */
        $message->setWorker($messageObj->worker);
        $message->setSwappable($messageObj->swappable);
        $message->setMessageType($messageObj->messageType);
        $message->setSwapping($messageObj->swapping);
        
        $em->persist($message);
        $em->flush();

       $answer = [
           'id' => $message->getId()
       ];
        return new JsonResponse($answer);
    }
}