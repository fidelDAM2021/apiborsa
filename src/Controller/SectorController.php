<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sector;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SectorType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[Route('api/sector')]
class SectorController extends AbstractController
{
    #[Rest\Get('/', name: 'app_search_sectors')]
    public function searchSectors(ManagerRegistry $doctrine): JsonResponse {
        //$sectors=sectorList::getsectors();
        $httpCode=200;
        $rep=$doctrine->getRepository(Sector::class);
        $sectors=$rep->findAll();
        $sectorsList=[];
        if(count($sectors) > 0) {
            foreach($sectors as $sector) {
                $sectorsList[]=$sector->toArray();
            }
            $response=[
                'ok'=>true,
                'sectors'=>$sectorsList,
            ];
        } 
        else {
            $response=[
                'ok'=>false,
                'error'=>'No hi ha sectors',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value<\d+>}/', name: 'app_searchbyid_sector')]
    public function searchsectorById(ManagerRegistry $doctrine,$value=""): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Sector::class);
        $sector=$rep->find($value);
        if($sector) {
            $sectorArray=$sector->toArray();
            $response=[
                'ok'=>true,
                'sector'=>$sectorArray,
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>'No existeix el sector',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_sector')]
    public function addNewsector(ManagerRegistry $doctrine, Request
    $request, ValidatorInterface $validator): JsonResponse {
        $httpCode=201;
        try{
            $content=$request->getContent();
            $sector=new Sector();
            $sector->fromJSON($content);
            $errors = $validator->validate($sector);

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
            else {
            $entityManager=$doctrine->getManager();
            $entityManager->persist($sector);
            $entityManager->flush();   
            $response=[
                'ok'=>true,
                'missatge'=>"S'ha inserit el sector",
            ];
            }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en inserir el sector",
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Delete('/{id<\d+>}', name: 'app_delete_sector')]
    public function deletesector(ManagerRegistry $doctrine, $id=0): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Sector::class);
        $sector=$rep->find($id);
        if($sector) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($sector);
            $entityManager->flush(); 
            $response=[
                'ok'=>true,
                'missatge'=>"S'ha eliminat el sector",
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"El sector no existeix",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Put('/{id<\d+>}', name: 'app_update_sector')]
    public function updatesector(ManagerRegistry $doctrine,Request
    $request, ValidatorInterface $validator, $id=0): Response {
        $httpCode=200;
        try{
            $content=$request->getContent();
            $rep=$doctrine->getRepository(Sector::class);
            $sector=$rep->find($id);
            if($sector) {
                $sector->fromJSON($content);
                $errors = $validator->validate($sector);
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
                else {
                    $entityManager=$doctrine->getManager();
                    $entityManager->flush();
                    $response=[
                        'ok'=>true,
                        'missatge'=>"S'ha modificat el sector",
                    ];
                }
            }
            else {
                $response=[
                    'ok'=>false,
                    'error'=>"No existeix el sector",
                ];
                $httpCode=404;
            }
            }
            catch(Throwable $e) {
                $response=[
                    'ok'=>false,
                    'error'=>"Error en modificar el sector",
                ];
                $httpCode=500;
            }
            return new JsonResponse($response,$httpCode);
    }
}