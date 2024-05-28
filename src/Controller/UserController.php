<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserPasswordHasher;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[Route('api/users')]
class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasher $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Rest\Post('/', name: 'app_register_user')]
    public function register(ManagerRegistry $doctrine, Request $request,
    ValidatorInterface $validator): JsonResponse
    {
        $httpCode=201;
        try {
            $user = new User();
            $content=$request->getContent();
            $user->fromJSON($content);
            $errors = $validator->validate($user);
            $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $rep=$doctrine->getRepository(User::class);
            $users=$rep->findAll();
            if(empty($users)) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            else {
                $user->setRoles(['ROLE_USER']);
            
            }
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                        $errorsArray[] = $error->getMessage();
                }
                $response=[
                    'ok'=>false,
                    'error' => $errorsArray,
                ];
                $httpCode=400;
            }
                  
            $entityManager=$doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $response=[
                'ok'=>true,
                'missatge'=>"Usuari creat",
            ];
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>$e->getMessage(),
                'trace'=>$e->getTrace(),
            ];
            $httpCode=500;
            return new JsonResponse($response,$httpCode);
        }
        return new JsonResponse($response, $httpCode);
    }
}
