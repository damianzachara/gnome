<?php

namespace App\Controller\API;

use App\Entity\Gnome;
use App\Form\GnomeType;
use App\Repository\GnomeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gnomes")
 */
class GnomeController extends ApiController
{
    /**
     * @Route("/", name="gnome_new", methods="POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function postAction(Request $request): Response
    {
        $gnome = new Gnome();
        $form = $this->createForm(GnomeType::class, $gnome);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->handleImageUpload($gnome, $data['image'], $request) === false) {
                return $this->statusCode404('Invalid image file');
            } else {

                $this->handleImageUpload($gnome, $data['image'], $request);
                $this->addResource($gnome);

                $url = $this->generateUrl('gnome_get', ['id' => $gnome->getId()]);

                return new JsonResponse(['status' => 'ok'], 201, [
                    'Location' => $url
                ]);
            }
        } else {
            $errors = $this->getFormErrorMessages($form);
            return new JsonResponse(['status' => 'validation', 'message' => $errors], 400);
        }

    }

    /**
     * @Route("/{id}", name="gnome_get", methods="GET")
     * @param $id
     * @param GnomeRepository $gnomeRepository
     * @return JsonResponse
     */
    public function getAction($id, GnomeRepository $gnomeRepository): Response
    {
        $gnome = $gnomeRepository->find($id);

        if ($gnome != null) {
            $gnome = json_decode($this->serializer->serialize($gnome, 'json'));
            return new JsonResponse(['status' => 'ok', 'gnome' => $gnome], 200);
        } else {
            return $this->statusCode404('Not found resource');
        }
    }

    /**
     * @Route("/{id}", name="gnome_edit", methods="PUT")
     * @param Request $request
     * @param Gnome $id
     * @param GnomeRepository $gnomeRepository
     * @return JsonResponse
     */
    public function putAction(Request $request, $id, GnomeRepository $gnomeRepository): Response
    {
        $gnome = $gnomeRepository->find($id);

        if ($gnome != null) {
            $currentImg = $gnome->getImage();

            $form = $this->createForm(GnomeType::class, $gnome);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);

            if ($form->isSubmitted() && $form->isValid()) {

                if ($data['image'] != null) {
                $this->handleImageUpload($gnome, $data['image'], $request);
                } else {
                    $gnome->setImage($currentImg);
                }

                $this->updateResource();
                return new JsonResponse(['status' => 'ok'], 200);
            } else {
                $errors = $this->getFormErrorMessages($form);
                return new JsonResponse(['status' => 'validation', 'message' => $errors], 400);
            }
        } else {
            return $this->statusCode404('Not found resource');
        }

    }

    /**
     * @Route("/{id}", name="gnome_delete", methods={"DELETE"})
     * @param $id
     * @param GnomeRepository $gnomeRepository
     * @return JsonResponse
     */
    public function deleteAction($id, GnomeRepository $gnomeRepository): Response
    {
        $gnome = $gnomeRepository->find($id);

        if ($gnome != null) {
            $this->removeResource($gnome);
            return new JsonResponse(['status' => 'ok'], 200);
        } else {
            return $this->statusCode404('Not found resource');
        }
    }
}
