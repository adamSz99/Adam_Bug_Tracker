<?php
/**
 * Report controller.
 */

namespace App\Controller;

use App\Entity\Report;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\Type\ReportType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReportController
 */
#[Route('/reports')]
class ReportController extends AbstractController
{
    /**
     * Report service
     */
    private ReportService $reportService;

    /**
     * Translator interface
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param ReportService       $reportService Report service
     * @param TranslatorInterface $translator    Translator
     */
    public function __construct(ReportService $reportService, TranslatorInterface $translator)
    {
        $this->reportService = $reportService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route(
        name: 'report_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $filters = $this->filters($request);
        $page = $request->query->getInt('page', 1);
        $pagination = $this->reportService->getPaginatedList($page, $filters);

        return $this->render(
            'report/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Report $report Report
     *
     * @return Response Response
     */
    #[Route(
        '/{id}',
        name: 'report_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Report $report): Response
    {
        return $this->render('report/show.html.twig', ['report' => $report]);
    }

    /**
     * Create action.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route(
        '/create',
        name: 'report_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        $user = $this->getUser();
        if (!$user->isAdminRole()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));

            return $this->redirectToRoute('report_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setAuthor($user);
            $this->reportService->save($report);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request Request
     * @param Report  $report  Report
     *
     * @return Response Response
     */
    #[Route(
        '/{id}/edit',
        name: 'report_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Report $report): Response
    {
        $user = $this->getUser();
        if (!$user->isAdminRole()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));

            return $this->redirectToRoute('report_index');
        }

        $form = $this->createForm(
            ReportType::class,
            $report,
            ['method' => 'PUT', 'action' => $this->generateUrl('report_edit', ['id' => $report->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reportService->save($report);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render(
            'report/edit.html.twig',
            ['form' => $form->createView(), 'report' => $report]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request Request
     * @param Report  $report  Report
     *
     * @return Response Response
     */
    #[Route('/{id}/delete', name: 'report_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Report $report): Response
    {
        $form = $this->createForm(FormType::class, $report, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('report_delete', ['id' => $report->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reportService->delete($report);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render(
            'report/delete.html.twig',
            ['form' => $form->createView(), 'report' => $report]
        );
    }

    /**
     * Filters action.
     *
     * @param Request $request Request
     *
     * @return array Array
     */
    private function filters(Request $request): array
    {
        return [
            'category_id' => $request->query->getInt('filters_category_id'),
        ];
    }
}
