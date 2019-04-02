<?php

namespace App\Controller\API;

use App\Entity\Gnome;
use App\Form\GnomeType;
use App\Repository\GnomeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/gnomes")
 */
class GnomeController extends ApiController
{
    /**
     * @Route("/", name="gnome_new", methods="POST")
     * @param Request $request
     * @return JsonResponse
     * @SWG\Post(
     *     path="/api/gnomes/",
     *     produces={"application/json"},
     *     description="Post gnome",
     *     operationId="post_gnome",
     *     summary="Post gnome",
     *     tags={"Gnomes"},
     *     @SWG\Parameter(
     *          name="Post gnome",
     *          in="body",
     *          description="JSON content for Gnome",
     *          @SWG\Schema(
     *             @SWG\Property(property="name", type="string", example="name"),
     *             @SWG\Property(property="age", type="integer", example=25),
     *             @SWG\Property(property="strength", type="integer", example=10),
     *             @SWG\Property(property="image", type="base64", example="base64-image"),
     *          )
     *     ),
     *
     *    @SWG\Response(
     *          response="200",
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="status", type="string", example="Status information")
     *          ),
     *         description="Connection successful",
     *     )
     * )
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
     * @SWG\Get(
     *     path="/api/gnomes/{id}",
     *     produces={"application/json"},
     *     description="Get gnome",
     *     operationId="get_gnome",
     *     summary="Get gnome",
     *     tags={"Gnomes"},
     *     @SWG\Response(
     *          response="200",
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *                  @SWG\Property(property="status", type="string", example="OK"),
     *                  @SWG\Property(property="gnome", ref=@Model(type=Gnome::class)
     *                       )
     *                  )
     *          ),
     *         description="Connection successful",
     *     )
     * )
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
     * @SWG\Put(
     *     path="/api/gnomes/{id}",
     *     produces={"application/json"},
     *     description="Put gnome",
     *     operationId="put_gnome",
     *     summary="Put gnome",
     *     tags={"Gnomes"},
     *     @SWG\Parameter(
     *          name="put gnome",
     *          in="body",
     *          description="JSON content for gnome",
     *          @SWG\Schema(
     *             @SWG\Property(property="name", type="string", example="name"),
     *             @SWG\Property(property="age", type="integer", example=25),
     *             @SWG\Property(property="strength", type="integer", example=10),
     *             @SWG\Property(property="image", type="base64", example="base64-image"),
     *          )
     *     ),
     *     @SWG\Response(
     *          response="200",
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="status", type="string", example="Status information")
     *          ),
     *         description="Connection successful",
     *     )
     * )
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
     * @SWG\Delete(
     *     path="/api/gnomes/{id}",
     *     produces={"application/json"},
     *     description="delete gnome",
     *     operationId="delete_gnome",
     *     summary="Delete gnome",
     *     tags={"Gnomes"},
     *     @SWG\Response(
     *          response="200",
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="status", type="string", example="Status information")
     *          ),
     *         description="Connection successful",
     *     )
     *
     * )
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
