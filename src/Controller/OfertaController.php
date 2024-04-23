<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Empresa;
use App\Entity\Oferta;
use App\Entity\Cicle;
use Monolog\DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use Throwable;

#[Route('api/oferta')]
class OfertaController extends AbstractController
{
    #[Rest\Get('/', name: 'app_search_ofertes')]
    public function searchOfertes(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Oferta::class);
        $ofertes=$rep->findAll();
        $ofertesList=[];
        if(count($ofertes) > 0) {
            foreach($ofertes as $oferta) {
                $ofertesList[]=$oferta->toArray();
            }
            $response=[
                'ok'=>true,
                'ofertes'=>$ofertesList,
            ];
        } 
        else {
            $response=[
                'ok'=>false,
                'error'=>'No hi ha ofertes',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value<\d+>}/', name: 'app_searchbyid_oferta')]
    public function searchContactById(ManagerRegistry $doctrine,$value=""): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Oferta::class);
        $oferta=$rep->find($value);
        if($oferta) {
            $response=[
                'ok'=>true,
                'oferta'=>$oferta->toArray()
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"L'oferta no existeix"
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_oferta')]
    public function addNewOferta(ManagerRegistry $doctrine, Request
    $request, ValidatorInterface $validator): JsonResponse {
        $httpCode=201;
        try{
            $content=$request->getContent();
            $oferta=new Oferta();
            $oferta->fromJSON($content);
            $rep=$doctrine->getRepository(Oferta::class);
            $repcicle=$doctrine->getRepository(Cicle::class);
            $repempresa=$doctrine->getRepository(Empresa::class);
            $wrongCicle=false;
            foreach($oferta->getCodisCicle() as $cicle) {
                $cicle=$repcicle->find($cicle);
                if($cicle) {
                    $oferta->addCicle($cicle);
                }
                else {
                    $wrongCicle=true;
                }
            }
            if($wrongCicle==false) {
                $empresa=$repempresa->find($oferta->getNif());
                if($empresa) {
                    $oferta->setNIFempresa($empresa);
                    $errors=$validator->validate($oferta);
                    if(count($errors) > 0) {
                        $errorsArray = [];
                        foreach ($errors as $error) {
                                $errorsArray[] = $error->getMessage()." ".$error->getPropertyPath();
                        }
                        $response=[
                            'ok'=>false,
                            'error'=>$errorsArray,
                        ];
                        $httpCode=400;
                    }
                    else {
                        $entityManager=$doctrine->getManager();
                        $entityManager->persist($oferta);
                        $entityManager->flush();   
                        $response=[
                            'ok'=>true,
                            'missatge'=>"S'ha inserit l'oferta",
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
                    'error'=>"Hi ha cicles que no existeixen"
                ];
                $httpCode=404;
            }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en inserir l'oferta",
                'missatge'=>$e->getMessage(),
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
       
    }
    #[Rest\Delete('/{id<\d+>}', name: 'app_delete_oferta')]
    public function deleteOferta(ManagerRegistry $doctrine, $id=0): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Oferta::class);
        $oferta=$rep->find($id);
        if($oferta) {
                $entityManager = $doctrine->getManager();
                $entityManager->remove($oferta);
                $entityManager->flush(); 
                $response = [
                    'ok'=>true,
                    'missatge'=>"S'ha eliminat l'oferta"
                ];
        }
        else {
            $response = [
                'ok'=>false,
                'error'=>"L'oferta no existeix"
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Put('/{id<\d+>}', name: 'app_update_oferta')]
    public function updateOferta(ManagerRegistry $doctrine,Request
    $request,ValidatorInterface $validator, $id=0): JsonResponse {
        $httpCode=200;
        try{
            $content=$request->getContent();
            $rep=$doctrine->getRepository(Oferta::class);
            $oferta=$rep->find($id);
            if($oferta) {
                foreach($oferta->getCicle() as $cicle) {
                    $oferta->removeCicle($cicle);
                }
                $oferta->fromJSON($content);
                $repcicle=$doctrine->getRepository(Cicle::class);
                $repempresa=$doctrine->getRepository(Empresa::class);
                $wrongCicle=false;
                foreach($oferta->getCodisCicle() as $cicle) {
                    $cicle=$repcicle->find($cicle);
                    if($cicle) {
                        $oferta->addCicle($cicle);
                    }
                    else {
                        $wrongCicle=true;
                    }
                }
                if($wrongCicle==false) {
                    $empresa=$repempresa->find($oferta->getNif());
                    if($empresa) {
                        $oferta->setNIFempresa($empresa);
                        $errors=$validator->validate($oferta);
                        if(count($errors) > 0) {
                            $errorsArray = [];
                            foreach ($errors as $error) {
                                    $errorsArray[] = $error->getMessage().$error->getInvalidValue();
                            }
                            $response=[
                                'ok'=>false,
                                'error'=>$errorsArray,
                            ];
                            $httpCode=400;
                        }
                        else {
                            $entityManager=$doctrine->getManager();
                            $entityManager->persist($oferta);
                            $entityManager->flush();   
                            $response=[
                                'ok'=>true,
                                'missatge'=>"S'ha modificat l'oferta",
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
                        'error'=>"Hi ha cicles que no existeixen"
                    ];
                    $httpCode=404;
                }
            }
            else {
                $response=[
                    'ok'=>false,
                    'error'=>"L'oferta no existeix",
                ];
                $httpCode=404;
            }
            }
            catch(Throwable $e) {
                $response=[
                    'ok'=>false,
                    'error'=>"Error en modificar l'oferta",
                ];
                $httpCode=500;
            }
            return new JsonResponse($response,$httpCode);
    }
}
