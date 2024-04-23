<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contacte;
use App\Entity\Empresa;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use Throwable;

#[Route('api/contacte')]
class ContactController extends AbstractController
{
    #[Rest\Get('/', name: 'app_search_contacts')]
    public function searchContacts(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Contacte::class);
        $contactes=$rep->findAll();
        $contactesList=[];
        if(count($contactes) > 0) {
            foreach($contactes as $contacte) {
                $contactesList[]=$contacte->toArray();
            }
            $response=[
                'ok'=>true,
                'contactes'=>$contactesList,
            ];
        } 
        else {
            $response=[
                'ok'=>false,
                'error'=>'No hi ha contactes',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value<\d+>}/', name: 'app_searchbyid_contact')]
    public function searchContactById(ManagerRegistry $doctrine,$value=""): Response {
        $httpCode=200;
        $rep=$doctrine->getRepository(Contacte::class);
        $contacte=$rep->find($value);
        if($contacte) {
            $contacteArray=$contacte->toArray();
            $response=[
                'ok'=>true,
                'contacte'=>$contacteArray,
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"No existeix el contacte",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_contact')]
    public function addNewContact(ManagerRegistry $doctrine, Request
    $request, ValidatorInterface $validator): JsonResponse {
        $httpCode=201;
        try{
            $content=$request->getContent();
            $contacte=new Contacte();
            $contacte->fromJson($content);
            $rep=$doctrine->getRepository(Empresa::class);
            $empresa=$rep->find($contacte->getNif());
            if($empresa) {
                $contacte->setNIFempresa($empresa);
                $errors=$validator->validate($contacte);
                if(count($errors) > 0) {
                    $errorsArray = [];
                    foreach ($errors as $error) {
                            $errorsArray[] = $error->getMessage();
                    }
                    $response=[
                        'ok'=>false,
                        'error'=>"Error en les dades",
                        'errors'=>$errorsArray,
                    ];
                    $httpCode=400;
                }
                else {
                    $entityManager=$doctrine->getManager();
                    $entityManager->persist($contacte);
                    $entityManager->flush();   
                    $response=[
                        'ok'=>true,
                        'missatge'=>"S'ha inserit el contacte",
                    ];
                }
            }
            else {
                $response=[
                    'ok'=>false,
                    'error'=>"No existeix l'empresa"
                ];
                $httpCode=404;
            }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en inserir el contacte",
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Delete('/{id<\d+>}', name: 'app_delete_contact')]
    public function deleteContact(ManagerRegistry $doctrine, $id=0): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Contacte::class);
        $contacte=$rep->find($id);
        if($contacte) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($contacte);
            $entityManager->flush(); 
            $response=[
                'ok'=>true,
                'missatge'=>"S'ha eliminat el contacte",
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"El contacte no existeix",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response);
    }
    #[Rest\Put('/{id<\d+>}', name: 'app_update_contact')]
    public function updateContact(ManagerRegistry $doctrine,Request
    $request, ValidatorInterface $validator, $id=0): JsonResponse {
        $httpCode=200;
        try{
            $content=$request->getContent();
            $rep=$doctrine->getRepository(Contacte::class);
            $repempresa=$doctrine->getRepository(Empresa::class);
            $contacte=$rep->find($id);
            if($contacte) {
                $contacte->fromJson($content);
                $empresa=$repempresa->find($contacte->getNif());
                if($empresa) {
                    $contacte->setNIFEmpresa($empresa);
                    $errors=$validator->validate($contacte);
                    if(count($errors) > 0) {
                        $errorsArray = [];
                        foreach ($errors as $error) {
                            $errorsArray[] = $error->getMessage();
                        }
                        $response=[
                            'ok'=>false,
                            'error'=>"Error en les dades",
                            'errors'=>$errorsArray,
                        ];
                        $httpCode=400;
                    }
                    else {
                        $entityManager=$doctrine->getManager();
                        $entityManager->persist($contacte);
                        $entityManager->flush();   
                        $response=[
                        'ok'=>true,
                        'missatge'=>"S'ha modificat el contacte",
                        ];
                    }
                }
                else {
                  $response=[
                    'ok'=>false,
                    'error'=>"No existeix l'empresa"
                ];
                $httpCode=404;
                }
            }
            else {
                $response=[
                  'ok'=>false,
                  'error'=>"No existeix el contacte"
                ];
                $httpCode=404;
            }
           
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error modificant el contacte"
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }    
}
