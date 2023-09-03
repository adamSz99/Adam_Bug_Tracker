<?php
/**
 * Security controller
 */

namespace App\Controller;

use App\Form\Type\ProfileType;
use App\Form\Type\UpgradePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Security controller
 */
class SecurityController extends AbstractController
{
    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * User service
     *
     * @var UserService
     */
    private UserService $userService;

    /**
     * Construct new controller
     *
     * @param TranslatorInterface $translator  Translator
     * @param UserService         $userService User service
     */
    public function __construct(TranslatorInterface $translator, UserService $userService)
    {
        $this->translator = $translator;
        $this->userService = $userService;
    }

    /**
     * Login action
     *
     * @param AuthenticationUtils $authenticationUtils Authentication utils
     *
     * @return Response Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('report_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Upgrade password action
     *
     * @param Request                     $request        Request
     * @param User                        $user           User
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     *
     * @return Response Response
     */
    #[Route(
        '/{id}/upgrade-password',
        name: 'user_upgrade_password',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function upgradePassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UpgradePasswordType::class, $user, ['method' => 'PUT']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $this->userService->save($user);
            $this->addFlash('success', 'message.updated_successfully');
            $this->redirectToRoute('report_index');
        }

        return $this->render('profile/upgrade_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Response
     */
    #[Route(
        '/{id}/profile',
        name: 'profile',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function profile(Request $request, User $user): Response
    {
        $form = $this->createForm(
            ProfileType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('profile', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render(
            'profile/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Logout action
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
