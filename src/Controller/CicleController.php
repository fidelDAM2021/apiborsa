<?php

namespace App\Controller;

use App\Entity\Cicle;
use App\Form\CicleType;
use Throwable;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;



#[Route('api/cicle')]
class CicleController extends AbstractController
{
    #[Rest\Get('/', name: 'app_search_cycles')]
    public function searchCycles(ManagerRegistry $doctrine): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Cicle::class);
        $cicles=$rep->findAll();
        $ciclesList=[];
        if(count($cicles) > 0) {
            foreach($cicles as $cicle) {
                $ciclesList[]=$cicle->toArray();
            }
            $response=[
                'ok'=>true,
                'cicles'=>$ciclesList,
            ];
        } 
        else {
            $response=[
                'ok'=>false,
                'error'=>'No hi ha cicles',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value<\d+>}', name: 'app_searchbyid_cycle')]
    public function searchCicleById(ManagerRegistry $doctrine,$value=""): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Cicle::class);
        $cicle=$rep->find($value);
        if($cicle) {
            $cicleArray=$cicle->toArray();
            $response=[
                'ok'=>true,
                'cicle'=>$cicleArray,
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>'No existeix el cicle',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_cycle')]
    public function addNewcicle(ManagerRegistry $doctrine, Request
    $request,ValidatorInterface $validator): JsonResponse {
        $httpCode=201;
        try{
            $content=$request->getContent();
            $cicle=new Cicle();
            $cicle->fromJson($content);
            $errors = $validator->validate($cicle);
            if(count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                        $errorsArray[] = $error->getMessage();
                }
                $response=[
                    'ok'=>false,
                    'error'=>$errorsArray,
                ];
                $httpCode=400;
            }
            else {
                $entityManager=$doctrine->getManager();
                $entityManager->persist($cicle);
                $entityManager->flush();   
                $response=[
                    'ok'=>true,
                    'missatge'=>"S'ha inserit el cicle",
                ];
            }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en inserir el cicle",
                'message'=>$e->getMessage()
            ];
            $httpCode=400;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Delete('/{id<\d+>}', name: 'app_delete_cycle')]
    public function deletecicle(ManagerRegistry $doctrine, $id=0): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Cicle::class);
        $cicle=$rep->find($id);
        if($cicle) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($cicle);
            $entityManager->flush(); 
            $response=[
                'ok'=>true,
                'missatge'=>"S'ha eliminat el cicle",
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"El cicle no existeix",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Put('/{id<\d+>}', name: 'app_update_cycle')]
    public function updatecicle(ManagerRegistry $doctrine,Request
    $request, ValidatorInterface $validator, $id=0): Response {
        $httpCode=200;
        try{
        $content=$request->getContent();
        $rep=$doctrine->getRepository(Cicle::class);
        $cicle=$rep->find($id);
        if($cicle) {
            $cicle->fromJSON($content);
            $errors = $validator->validate($cicle);
            if(count($errors) > 0) {
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
            else {
                $entityManager=$doctrine->getManager();
                $entityManager->flush();
                $response=[
                    'ok'=>true,
                    'missatge'=>"S'ha modificat el cicle",
                ];
            }
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"No existeix el cicle",
            ];
        }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en modificar el cicle",
            ];
            $httpCode=400;
        }
        return new JsonResponse($response,$httpCode);
    }
}
