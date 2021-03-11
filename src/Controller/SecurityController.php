<?php
namespace App\Controller;
use App\Entity\Workers;
use App\Form\WorkersType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController {
    
// /**
//  * @Route("/login", name="login", methods={"POST"})
//  */

//     public function login(Request $request) {

//         $user = $this->getUser();
//         return $this->json([
//             'username'=> $user->getUsername(),
//             'token' => ''
//         ]);
//     }
}
