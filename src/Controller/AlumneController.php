<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Alumne;
use App\Entity\Curriculum;
use App\Entity\Cicle;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use Throwable;

#[Route('api/alumne')]
class AlumneController extends AbstractController
{
    #[Rest\Get('/welcome/', name: 'app_welcome')]
    public function welcomeStudents(ManagerRegistry $doctrine): JsonResponse
    {
        $httpCode=200;
        $response = [
                'ok' => true,
                'error' => 'No hi ha alumnes',
            ];
        return new JsonResponse($response,$httpCode);
    }

    #[Rest\Get('/', name: 'app_search_students')]
    public function searchStudents(ManagerRegistry $doctrine): JsonResponse
    {
        $httpCode=200;
        $rep = $doctrine->getRepository(Alumne::class);
        $alumnes = $rep->findAll();
        $alumnesList = [];
        if (count($alumnes) > 0) {
            foreach ($alumnes as $alumne) {
                $alumnesList[] = $alumne->toArray();
            }
            $response = [
                'ok' => true,
                'alumnes' => $alumnesList,
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => 'No hi ha alumnes',
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Get('/{value<\d+>}/', name: 'app_searchbyid_student')]
    public function searchStudentById(ManagerRegistry $doctrine, $value = ""): JsonResponse
    {
        $httpCode=200;
        $rep = $doctrine->getRepository(Alumne::class);
        $alumne = $rep->find($value);
        if ($alumne) {
            $alumneArray = $alumne->toArray();
            $response = [
                'ok' => true,
                'alumne' => $alumneArray,
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => "No existeix l'alumne",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Post('/', name: 'app_new_student')]
    public function addNewalumne(
        ManagerRegistry $doctrine,
        Request $request,
        ValidatorInterface $validator 
    ): JsonResponse {
        $httpCode=201;
        try {
            $content = $request->getContent();
            $alumne = new Alumne();
            $alumne->fromJSON($content);
            $rep = $doctrine->getRepository(Alumne::class);
            $repcicle = $doctrine->getRepository(Cicle::class);
            $wrongCicle = false;
            foreach ($alumne->getCodisCicle() as $cicle) {
                $cicle = $repcicle->find($cicle);
                if ($cicle) {
                    $alumne->addCicle($cicle);
                } else {
                    $wrongCicle = true;
                }
            }
            if ($wrongCicle == false) {
                $curr = new Curriculum();
                $curr->setExperiencia($alumne->getDadesCurriculum()['experiencia']);
                $curr->setIdiomes($alumne->getDadesCurriculum()['idiomes']);
                $curr->setEstudis($alumne->getDadesCurriculum()['estudis']);
                $curr->setCompetencies($alumne->getDadesCurriculum()['competencies']);
                $alumne->setCurriculum($curr);
                $errors = $validator->validate($alumne);
                if (count($errors) > 0) {
                    $errorsArray = [];
                    foreach ($errors as $error) {
                        $errorsArray[] = $error->getMessage();
                    }
                    $response = [
                        'ok' => false,
                        'error' => $errorsArray,
                    ];
                    $httpCode=400;
                } else {
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($alumne);
                    $entityManager->flush();
                    $response = [
                        'ok' => true,
                        'missatge' => "S'ha inserit l'alumne",
                    ];
                }
            } else {
                $response = [
                    'ok' => false,
                    'error' => "Hi ha cicles que no existeixen"
                ];
                $httpCode=404;
            }
        } catch (Throwable $e) {
            $response = [
                'ok' => false,
                'error' => "Error en inserir l'alumne",
                'message' => $e->getMessage()
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Delete('/{id<\d+>}', name: 'app_delete_student')]
    public function deletealumne(ManagerRegistry $doctrine, $id = 0): JsonResponse
    {
        $httpCode=200;
        $rep = $doctrine->getRepository(Alumne::class);
        $alumne = $rep->find($id);
        if ($alumne) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($alumne);
            $entityManager->flush();
            $response = [
                'ok' => true,
                'missatge' => "S'ha eliminat l'alumne",
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => "L'alumne no existeix",
            ];
            $httpCode=404;
        }
        return new JsonResponse($response,$httpCode);
    }
    #[Rest\Put('/{id<\d+>}', name: 'app_update_student')]
    public function updatealumne(
        ManagerRegistry $doctrine,
        Request $request,
        ValidatorInterface $validator,
        $id = 0
    ): JsonResponse {
        $httpCode=200;
        try {
            $content = $request->getContent();
            $rep = $doctrine->getRepository(Alumne::class);
            $alumne = $rep->find($id);
            if ($alumne) {
                foreach ($alumne->getCicle() as $cicle) {
                    $alumne->removeCicle($cicle);
                }
                $alumne->fromJSON($content);
                $repcicle = $doctrine->getRepository(Cicle::class);
                $wrongCicle = false;
                foreach ($alumne->getCodisCicle() as $cicle) {
                    $cicle = $repcicle->find($cicle);
                    if ($cicle) {
                        $alumne->addCicle($cicle);
                    } else {
                        $wrongCicle = true;
                    }
                }
                if ($wrongCicle == false) {
                    $curr = $alumne->getCurriculum();
                    $curr->setExperiencia($alumne->getDadesCurriculum()['experiencia']);
                    $curr->setIdiomes($alumne->getDadesCurriculum()['idiomes']);
                    $curr->setEstudis($alumne->getDadesCurriculum()['estudis']);
                    $curr->setCompetencies($alumne->getDadesCurriculum()['competencies']);
                    $alumne->setCurriculum($curr);
                    $errors = $validator->validate($alumne);
                    if (count($errors) > 0) {
                        $errorsArray = [];
                        foreach ($errors as $error) {
                            $errorsArray[] = $error->getMessage();
                        }
                        $response = [
                            'ok' => false,
                            'error' => $errorsArray,
                        ];
                        $httpCode=400;
                    } else {
                        $entityManager = $doctrine->getManager();
                        $entityManager->flush();
                        $response = [
                            'ok' => true,
                            'missatge' => "S'ha modificat l'alumne",
                        ];
                    }
                } else {
                    $response = [
                        'ok' => false,
                        'error' => "Hi ha cicles que no existeixen"
                    ];
                    $httpCode=404;
                }
            } else {
                $response = [
                    'ok' => false,
                    'error' => "L'alumne no existeix",
                ];
                $httpCode=404;
            }
        } catch (Throwable $e) {
            $response = [
                'ok' => false,
                'error' => "Error en modificar l'alumne",
                'message'=>$e->getMessage()
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }
    
    #[Rest\Post('/curriculum/{id<\d+>}', name: 'app_curriculum_student')]
    public function curriculumalumne(ManagerRegistry $doctrine,Request
    $request, $id=0): JsonResponse {
        $httpCode=201;
        try {
            $repAl=$doctrine->getRepository(Alumne::class);
            $alumne=$repAl->find($id);
            if($alumne) {
                $repCu=$doctrine->getRepository(Curriculum::class);
                $curriculum=$repCu->findOneBy(["alumne"=>$id]);
                if($curriculum==null) {
                    $curriculum=new Curriculum();
                    $curriculum->setAlumne($alumne);
                }
                $content = $request->request->get("curriculum");
                $curriculum->fromJSON($content);
                $pdf=$request->files->get('pdf');
                if($pdf) {
                    $newFileName=$id.".pdf";
                    $pdf->move(
                        $this->getParameter('curriculums_directory'),
                        $newFileName
                    );
                    $alumne->setPDF(true);
                }
                $entityManager=$doctrine->getManager();
                $entityManager->persist($curriculum);
                $entityManager->flush();
                $response = [
                    'ok' => true,
                    'message' => "Currículum actualitzat"
                ];
            }
            else {
                $response = [
                    'ok' => false,
                    'error' => "L'alumne no existeix",
                ];
                $httpCode=404;
            }
        } catch (Throwable $e) {
            $response = [
                'ok' => false,
                'error' => "Error en pujar el currículum",
                'message'=>$e->getMessage()
            ];
            $httpCode=500;
        }
        return new JsonResponse($response,$httpCode);
    }

    #[Rest\Get('/download/{id}', name: 'app_download_student')]
    public function downloadAction($id) {
        try {
        $filePath = $this->getParameter('curriculums_directory'). "/" . $id. ".pdf";
        $fileName = basename($filePath);
        $response = new Response();
    
        // Comprueba si el archivo existe antes de intentar descargarlo
        if (!file_exists($filePath)) {
            //throw $this->createNotFoundException('El archivo no existe.');
            $response = [
                'ok' => false,
                'error' => "No existeix l'arxiu",
            ];
            $httpCode=500;
            return new JsonResponse($response,$httpCode);
        }
        else {
        $response->headers->set('Content-type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $fileName));
        $response->setContent(file_get_contents($filePath));
        $response->setStatusCode(200);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
        }
        } catch (Throwable $e) {
            $response = [
                'ok' => false,
                'error' => "Error en descarregar l'arxiu",
                'message'=>$e->getMessage()
            ];
            $httpCode=500;
            return new JsonResponse($response,$httpCode);
        }
    }
}