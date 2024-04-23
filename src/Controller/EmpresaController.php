<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Empresa;
use App\Entity\Sector;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Form\EmpresaType;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Throwable;


#[Route('api/empresa')]
class EmpresaController extends AbstractController
{
    #[Rest\Get('/', name: 'app_search_empreses')]
    public function searchEmpreses(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Empresa::class);
        $empreses=$rep->findAll();
        $empresesList=[];
        if(count($empreses) > 0) {
            foreach($empreses as $empresa) {
                $empresesList[]=$empresa->toArray();
            }
            $response=[
                'ok'=>true,
                'empreses'=>$empresesList,
            ];
        } 
        else {
            $response=[
                'ok'=>false,
                'error'=>'No hi ha empreses',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value}/', name: 'app_searchbyid_empresa')]
    public function searchEmpresaById(ManagerRegistry $doctrine,$value=""): Response {
        $httpCode=200;
        $rep=$doctrine->getRepository(Empresa::class);
        $empresa=$rep->find($value);
        if($empresa) {
            $empresaArray=$empresa->toArray();
            $response=[
                'ok'=>true,
                'empresa'=>$empresaArray,
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"No existeix l'empresa",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_empresa')]
    public function addNewEmpresa(ManagerRegistry $doctrine, Request
    $request, ValidatorInterface $validator): JsonResponse {
        $httpCode=201;
        try{
            $content=$request->getContent();
            $empresa=new Empresa();
            $empresa->fromJson($content);
            $rep=$doctrine->getRepository(Sector::class);
            $sector=$rep->find($empresa->getCodSector());
            if($sector) {
                $empresa->setIdSector($sector);
                $errors = $validator->validate($empresa);
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
                    $entityManager->persist($empresa);
                    $entityManager->flush();   
                    $response=[
                        'ok'=>true,
                        'missatge'=>"S'ha inserit l'empresa",
                    ];
                }
            }
            else {
                $response=[
                    'ok'=>false,
                    'error'=>"No existeix el sector"
                ];
                $httpCode=404;
            }
        }
        catch(Throwable $e) {
            $response=[
                'ok'=>false,
                'error'=>"Error en inserir l'empresa",
                'missatge'=>$e->getMessage(),
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Delete('/{id}', name: 'app_delete_empresa')]
    public function deleteEmpresa(ManagerRegistry $doctrine, $id=0): JsonResponse {
        $httpCode=200;
        $rep=$doctrine->getRepository(Empresa::class);
        $empresa=$rep->find($id);
        if($empresa) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($empresa);
            $entityManager->flush(); 
            $response=[
                'ok'=>true,
                'missatge'=>"S'ha eliminat l'empresa",
            ];
        }
        else {
            $response=[
                'ok'=>false,
                'error'=>"L'empresa no existeix",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Put('/{id}', name: 'app_update_empresa')]
    public function updateEmpresa(ManagerRegistry $doctrine,Request
    $request,ValidatorInterface $validator,$id=0): JsonResponse {
        $httpCode=200;
        try{
            $content=$request->getContent();
            $rep=$doctrine->getRepository(Empresa::class);
            $repsector=$doctrine->getRepository(Sector::class);
            $empresa=$rep->find($id);
            $empresa->fromJson($content);
            if($empresa) {
                $sector=$repsector->find($empresa->getCodSector());
                if($sector) {
                    $empresa->setIdSector($sector);
                    $errors = $validator->validate($empresa);
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
                    $entityManager->persist($empresa);
                    $entityManager->flush();   
                    $response=[
                       'ok'=>true,
                      'missatge'=>"S'ha modificat l'empresa",
                     ];
                    }
                }
                else {
                  $response=[
                    'ok'=>false,
                    'error'=>"No existeix el sector"
                    ];
                    $httpCode=404;
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
                'error'=>"Error modificant l'empresa",
                'message'=>$e->getMessage(),
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
}