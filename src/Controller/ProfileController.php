<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfo;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProfileController extends BaseController
{
    /**
     * @param Request $request
     * @param int|null $id
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function indexAction(Request $request, int $id = null)
    {
        if (is_null($id)) {
            if (!$this->userIsLoggedIn()) {
                throw new AccessDeniedHttpException();
            }
            $user = $this->getUser();
        } else {
            $user = $this->doctrine->getRepository(User::class)->find($id);
        }

        $form = null;
        if ($user === $this->getUser()) {
            $form = $this->formFactory->create(UserInfo::class, $this->getUser()->getInfo());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->doctrine->getManager()->flush();

                $this->addFlash(self::FLASH_TYPE_INFORMATION, 'Je profiel is aangepast');

                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('somda/profile.html.twig', ['user' => $user, 'form' => $form ? $form->createView() : null]);
    }
}
