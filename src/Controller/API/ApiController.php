<?php


namespace App\Controller\API;

use App\Entity\Gnome;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package App\Controller\API
 */
class ApiController extends AbstractController
{
    /**
     * serializer field
     *
     * @var Serializer
     */
    protected $serializer;

    /**
     * serializer field
     *
     * @var Serializer
     */
    protected $containerInterface;


    /**
     * ApiController constructor.
     */
    public function __construct(SerializerInterface $serializer, ContainerInterface $containerInterface)
    {
        $this->serializer = $serializer;
        $this->containerInterface = $containerInterface;
    }

    /**
     * @param Gnome $gnome
     * @param $image
     * @param Request $request
     * @return bool|string
     */
    public function handleImageUpload(Gnome &$gnome, $image, Request $request)
    {

        $fileName = $gnome->getId() . '-' . time() . '.png';
        $path = $this->containerInterface->getParameter('image_upload_path');

        if (!is_dir($path)) mkdir($path, 0777, true);
        $path .= $fileName;

        $content = base64_decode($image);

        $basePath = $request->getUriForPath('/' . $path);

        if (getimagesizefromstring($content)['mime'] != 'image/png') {
            return false;
        };
        if (!$content) {
            return false;
        }

        file_put_contents($path, $content);

        $gnome->setImage($basePath);

        return $fileName;
    }

    /**
     * @return array
     */
    public function getFormErrorMessages(FormInterface $form)
    {
        $errors = [];

        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
                /** @var Form $child */
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getFormErrorMessages($child);
                }
            }
        } else {
            /** @var FormError $error */
            foreach ($form->getErrors() as $key => $error) {
                $errors[$key] = $error->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param string $messageContent
     * @return JsonResponse
     */
    public function statusCode404(string $messageContent)
    {
        return new JsonResponse(['status' => 'validation', 'message' => $messageContent], 404);
    }

    /**
     * @param $resource
     */
    public function addResource($resource)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($resource);
        $em->flush();
    }

    /**
     *
     */
    public function updateResource()
    {
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param $resource
     */
    public function removeResource($resource)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($resource);
        $em->flush();
    }
}