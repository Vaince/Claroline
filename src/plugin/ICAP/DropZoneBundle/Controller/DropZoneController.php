<?php
/**
 * Created by : Vincent SAISSET
 * Date: 22/08/13
 * Time: 09:30
 */

namespace ICAP\DropZoneBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use ICAP\DropZoneBundle\Entity\Criterion;
use ICAP\DropZoneBundle\Entity\Document;
use ICAP\DropZoneBundle\Entity\Drop;
use ICAP\DropZoneBundle\Entity\DropZone;
use ICAP\DropZoneBundle\Form\CriterionDeleteType;
use ICAP\DropZoneBundle\Form\CriterionType;
use ICAP\DropZoneBundle\Form\DocumentDeleteType;
use ICAP\DropZoneBundle\Form\DocumentFileType;
use ICAP\DropZoneBundle\Form\DocumentResourceType;
use ICAP\DropZoneBundle\Form\DocumentUrlType;
use ICAP\DropZoneBundle\Form\DropType;
use ICAP\DropZoneBundle\Form\DropZoneCommonType;
use ICAP\DropZoneBundle\Form\DropZoneCriteriaType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class DropZoneController extends Controller {

    protected function isAllow($dropZone, $actionName)
    {
        $collection = new ResourceCollection(array($dropZone->getResourceNode()));
        if (false === $this->get('security.context')->isGranted($actionName, $collection)) {
            throw new AccessDeniedException();
        }
    }

    protected function isAllowToEdit($dropZone)
    {
        $this->isAllow($dropZone, 'EDIT');
    }

    protected function isAllowToOpen($dropZone)
    {
        $this->isAllow($dropZone, 'OPEN');
    }

    /**
     * @Route(
     *      "/{resourceId}/drops",
     *      name="icap_dropzone_drops",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function dropsAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit",
     *      name="icap_dropzone_edit",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @Route(
     *      "/{resourceId}/edit/common",
     *      name="icap_dropzone_edit_common",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @Template()
     */
    public function editCommonAction($dropZone)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

//        $dropZone->setName($dropZone->getResourceNode()->getName());

        $form = $this->createForm(new DropZoneCommonType(), $dropZone);//, array('language' => $this->container->getParameter('locale')));

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());

            $dropZone = $form->getData();

            if (!$dropZone->getPeerReview() and $dropZone->getManualState() == 'peerReview') {
                $dropZone->setManualState('notStarted');
            }
            if ($dropZone->getEditionState() < 2) {
                $dropZone->setEditionState(2);
            }

            if (!$dropZone->getDisplayNotationToLearners() and ! $dropZone->getDisplayNotationMessageToLearners()) {
                $form->get('displayNotationToLearners')->addError(new FormError('Choose at least one type of ranking'));
                $form->get('displayNotationMessageToLearners')->addError(new FormError('Choose at least one type of ranking'));
            }

            if (!$dropZone->getAllowWorkspaceResource() and !$dropZone->getAllowUpload() and !$dropZone->getAllowUrl()) {
                $form->get('allowWorkspaceResource')->addError(new FormError('Choose at least one type of document'));
                $form->get('allowUpload')->addError(new FormError('Choose at least one type of document'));
                $form->get('allowUrl')->addError(new FormError('Choose at least one type of document'));
            }

            if (!$dropZone->getManualPlanning()) {
                if ($dropZone->getStartAllowDrop() === null) {
                    $form->get('startAllowDrop')->addError(new FormError('Choose a date'));
                }
                if ($dropZone->getEndAllowDrop() === null) {
                    $form->get('endAllowDrop')->addError(new FormError('Choose a date'));
                }
                if ($dropZone->getPeerReview() && $dropZone->getEndReview() === null) {
                    $form->get('endReview')->addError(new FormError('Choose a date'));
                }
                if ($dropZone->getStartAllowDrop() !== null && $dropZone->getEndAllowDrop() !== null) {
                    if ($dropZone->getStartAllowDrop()->getTimestamp() > $dropZone->getEndAllowDrop()->getTimestamp()) {
                        $form->get('startAllowDrop')->addError(new FormError('Must be before end allow drop'));
                        $form->get('endAllowDrop')->addError(new FormError('Must be after start allow drop'));
                    }
                }
                if ($dropZone->getStartReview() !== null && $dropZone->getEndReview() !== null) {
                    if ($dropZone->getStartReview()->getTimestamp() > $dropZone->getEndReview()->getTimestamp()) {
                        $form->get('startReview')->addError(new FormError('Must be before end peer review'));
                        $form->get('endReview')->addError(new FormError('Must be after start peer review'));
                    }
                }
                if($dropZone->getStartAllowDrop() !== null && $dropZone->getStartReview() !== null) {
                    if ($dropZone->getStartAllowDrop()->getTimestamp() > $dropZone->getStartReview()->getTimestamp()) {
                        $form->get('startReview')->addError(new FormError('Must be after start allow drop'));
                        $form->get('startAllowDrop')->addError(new FormError('Must be before start peer review'));
                    }
                }
                if($dropZone->getEndAllowDrop() !== null && $dropZone->getEndReview() !== null) {
                    if ($dropZone->getEndAllowDrop()->getTimestamp() > $dropZone->getEndReview()->getTimestamp()) {
                        $form->get('endReview')->addError(new FormError('Must be after end allow drop'));
                        $form->get('endAllowDrop')->addError(new FormError('Must be before end peer review'));
                    }
                }
            }

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($dropZone);
                $em->flush();

                $destination = 'icap_dropzone_edit_participant';
                if ($dropZone->getPeerReview()) {
                    $destination = 'icap_dropzone_edit_criteria';
                }

                return $this->redirect(
                    $this->generateUrl(
                        $destination,
                        array(
                            'resourceId' => $dropZone->getId()
                        )
                    )
                );
            }
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'form' => $form->createView()
        );

    }

    /**
     * @Route(
     *      "/{resourceId}/edit/criteria",
     *      name="icap_dropzone_edit_criteria",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
     * )
     *
     * @Route(
     *      "/{resourceId}/edit/criteria/{page}",
     *      name="icap_dropzone_edit_criteria_paginated",
     *      requirements={"resourceId" = "\d+", "page" = "\d+"},
     *      defaults={"page" = 1}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @Template()
     */
    public function editCriteriaAction($dropZone, $page)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ICAPDropZoneBundle:Criterion');
        $query = $repository
            ->createQueryBuilder('criterion')
            ->andWhere('criterion.dropZone = :dropZone')
            ->setParameter('dropZone', $dropZone)
            ->orderBy('criterion.id', 'ASC');

        $adapter = new DoctrineORMAdapter($query);
        $pager   = new Pagerfanta($adapter);
        $pager->setMaxPerPage(3);
        try {
            $pager->setCurrentPage($page);
        } catch (NotValidCurrentPageException $e) {
            if ($page > 0) {
                return $this->redirect(
                    $this->generateUrl(
                        'icap_dropzone_edit_criteria_paginated',
                        array(
                            'resourceId' => $dropZone->getId(),
                            'page' => $pager->getNbPages()
                        )
                    )
                );
            } else {
                throw new NotFoundHttpException();
            }
        }

        $form = $this->createForm(new DropZoneCriteriaType(), $dropZone);

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());

            if ($form->isValid()) {
                $dropZone = $form->getData();
                if ($dropZone->getEditionState() < 3) {
                    $dropZone->setEditionState(3);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($dropZone);
                $em->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'icap_dropzone_edit_participant',
                        array(
                            'resourceId' => $dropZone->getId()
                        )
                    )
                );
            }
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'pager' => $pager,
            'form' => $form->createView()
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit/addcriterion/{page}/{criterionId}",
     *      name="icap_dropzone_edit_add_criterion",
     *      requirements={"resourceId" = "\d+", "criterionId" = "\d+", "page" = "\d+"},
     *      defaults={"criterionId" = 0}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @Template()
     */
    public function editAddCriterionAction($dropZone, $page, $criterionId)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $criterion = new Criterion();
        if ($criterionId != 0) {
            $criterion = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ICAPDropZoneBundle:Criterion')
                ->find($criterionId);
        }

        $form = $this->createForm(new CriterionType(), $criterion);

        if ($this->getRequest()->isXMLHttpRequest()) {

            return $this->render(
                'ICAPDropZoneBundle:DropZone:editAddCriterionModal.html.twig',
                array(
                    'workspace' => $dropZone->getResourceNode()->getWorkspace(),
                    'dropZone' => $dropZone,
                    'pathArray' => $dropZone->getPathArray(),
                    'form' => $form->createView(),
                    'criterion' => $criterion,
                    'page' => $page
                )
            );
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'form' => $form->createView(),
            'criterion' => $criterion,
            'page' => $page
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit/createcriterion/{page}/{criterionId}",
     *      name="icap_dropzone_edit_create_criterion",
     *      requirements={"resourceId" = "\d+", "criterionId" = "\d+", "page" = "\d+"},
     *      defaults={"criterionId" = 0}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @Template("ICAPDropZoneBundle:DropZone:editAddCriteria.html.twig")
     */
    public function editCreateCriterionAction($dropZone, $page, $criterionId)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);


        $criterion = new Criterion();
        if ($criterionId != 0) {
            $criterion = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ICAPDropZoneBundle:Criterion')
                ->find($criterionId);
        }

        $form = $this->createForm(new CriterionType(), $criterion);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $criterion = $form->getData();
            $criterion->setDropZone($dropZone);

            $em = $this->getDoctrine()->getManager();
            $em->persist($criterion);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'icap_dropzone_edit_criteria_paginated',
                    array(
                        'resourceId' => $dropZone->getId(),
                        'page' => $page
                    )
                )
            );
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'form' => $form->createView(),
            'criterion' => $criterion,
            'page' => $page
        );
    }


    /**
     * @Route(
     *      "/{resourceId}/edit/deletecriterion/{page}/{criterionId}",
     *      name="icap_dropzone_edit_delete_criterion",
     *      requirements={"resourceId" = "\d+", "criterionId" = "\d+", "page" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("criterion", class="ICAPDropZoneBundle:Criterion", options={"id" = "criterionId"})
     * @Template()
     */
    public function editDeleteCriterionAction($dropZone, $page, $criterion)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $form = $this->createForm(new CriterionDeleteType(), $criterion);


        if ($this->getRequest()->isXMLHttpRequest()) {

            return $this->render(
                'ICAPDropZoneBundle:DropZone:editDeleteCriterionModal.html.twig',
                array(
                    'workspace' => $dropZone->getResourceNode()->getWorkspace(),
                    'dropZone' => $dropZone,
                    'pathArray' => $dropZone->getPathArray(),
                    'criterion' => $criterion,
                    'form' => $form->createView(),
                    'page' => $page
                )
            );
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'criterion' => $criterion,
            'form' => $form->createView(),
            'page' => $page
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit/removecriterion/{page}/{criterionId}",
     *      name="icap_dropzone_edit_remove_criterion",
     *      requirements={"resourceId" = "\d+", "criterionId" = "\d+", "page" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("criterion", class="ICAPDropZoneBundle:Criterion", options={"id" = "criterionId"})
     * @Template("ICAPDropZoneBundle:DropZone:editDeleteCriterion.html.twig")
     */
    public function editRemoveCriterionAction($dropZone, $page, $criterion)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $form = $this->createForm(new CriterionDeleteType(), $criterion);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $criterion = $form->getData();
            $criterion->setDropZone($dropZone);

            $em = $this->getDoctrine()->getManager();
            $em->remove($criterion);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'icap_dropzone_edit_criteria_paginated',
                    array(
                        'resourceId' => $dropZone->getId(),
                        'page' => $page
                    )
                )
            );
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
            'criterion' => $criterion,
            'form' => $form->createView(),
            'page' => $page
        );
    }



    /**
     * @Route(
     *      "/{resourceId}/edit/participant",
     *      name="icap_dropzone_edit_participant",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function editParticipantAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'pathArray' => $dropZone->getPathArray(),
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/open",
     *      name="icap_dropzone_open",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function openAction($dropZone, $user)
    {
        //Participant view for a dropZone
        $this->isAllowToOpen($dropZone);

        $em = $this->getDoctrine()->getManager();
        $dropRepo = $em->getRepository('ICAPDropZoneBundle:Drop');
        $drop = $dropRepo->findOneBy(array('dropZone' => $dropZone, 'user' => $user, 'finished' => true));

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'drop' => $drop,
            'pathArray' => $dropZone->getPathArray(),
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/drop",
     *      name="icap_dropzone_drop",
     *      requirements={"resourceId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function dropAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);

        $em = $this->getDoctrine()->getManager();
        $dropRepo = $em->getRepository('ICAPDropZoneBundle:Drop');

        if ($dropRepo->findOneBy(array('dropZone' => $dropZone, 'user' => $user, 'finished' => true)) !== null) {
            //TODO throw error
        }

        $notFinishedDrop = $dropRepo->findOneBy(array('dropZone' => $dropZone, 'user' => $user, 'finished' => false));
        if ($notFinishedDrop === null) {
            $notFinishedDrop = new Drop();
            $notFinishedDrop->setUser($user);
            $notFinishedDrop->setDropZone($dropZone);
            $notFinishedDrop->setFinished(false);

            $em->persist($notFinishedDrop);
            $em->flush();
            $em->refresh($notFinishedDrop);
        }

        $form = $this->createForm(new DropType(), $notFinishedDrop);
        $drop = $notFinishedDrop;

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());

            if (count($notFinishedDrop->getDocuments()) == 0) {
                $form->addError(new FormError('Add at least one document'));
            }

            if ($form->isValid()) {
                $notFinishedDrop->setFinished(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($notFinishedDrop);
                $em->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'icap_dropzone_open',
                        array(
                            'resourceId' => $dropZone->getId()
                        )
                    )
                );
            }
        }

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'drop' => $drop,
            'pathArray' => $dropZone->getPathArray(),
            'form' => $form->createView(),
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/document/{documentType}/{dropId}",
     *      name="icap_dropzone_document",
     *      requirements={"resourceId" = "\d+", "dropId" = "\d+", "documentType" = "url|file|resource"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @ParamConverter("drop", class="ICAPDropZoneBundle:Drop", options={"id" = "dropId"})
     * @Template()
     */
    public function documentAction($dropZone, $user, $documentType, $drop)
    {
        $this->isAllowToOpen($dropZone);

        $formType = null;
        if ($documentType == 'url') {
            if (!$dropZone->getAllowUrl()) {
                throw new AccessDeniedException();
            }
            $formType = new DocumentUrlType();
        } else if ($documentType == 'file') {
            if (!$dropZone->getAllowUpload()) {
                throw new AccessDeniedException();
            }
            $formType = new DocumentFileType();
        } else if ($documentType == 'resource') {
            if (!$dropZone->getAllowWorkspaceResource()) {
                throw new AccessDeniedException();
            }
            $formType = new DocumentResourceType();
        }

        $form = $this->createForm($formType);

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());

            if ($form->isValid()) {
                $document = new Document();
                if ($documentType == 'url') {
                    $document->setUrl($form->getData()['document']);
                }
                if ($documentType == 'file') {
                    $file = $form->getData()['document'];
                    var_dump($file);
                    die();
                }

                $document->setDrop($drop);

                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'icap_dropzone_drop',
                        array(
                            'resourceId' => $dropZone->getId()
                        )
                    )
                );
            }
        }

        $view = 'ICAPDropZoneBundle:DropZone:document.html.twig';
        if ($this->getRequest()->isXMLHttpRequest()) {
            $view = 'ICAPDropZoneBundle:DropZone:documentInline.html.twig';
        }

        return $this->render(
            $view,
            array(
                'workspace' => $dropZone->getResourceNode()->getWorkspace(),
                'dropZone' => $dropZone,
                'drop' => $drop,
                'documentType' => $documentType,
                'pathArray' => $dropZone->getPathArray(),
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/delete/document/{dropId}/{documentId}",
     *      name="icap_dropzone_delete_document",
     *      requirements={"resourceId" = "\d+", "dropId" = "\d+"}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @ParamConverter("drop", class="ICAPDropZoneBundle:Drop", options={"id" = "dropId"})
     * @ParamConverter("document", class="ICAPDropZoneBundle:Document", options={"id" = "documentId"})
     * @Template()
     */
    public function deleteDocumentAction($dropZone, $user, $drop, $document)
    {
        $this->isAllowToOpen($dropZone);

        if ($drop->getId() != $document->getDrop()->getId()) {
            throw new \HttpInvalidParamException();
        }

        if ($drop->getUser()->getId() != $user->getId()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(new DocumentDeleteType(), $document);

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($document);
                $em->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'icap_dropzone_drop',
                        array(
                            'resourceId' => $dropZone->getId()
                        )
                    )
                );
            }
        }

        $view = 'ICAPDropZoneBundle:DropZone:deleteDocument.html.twig';
        if ($this->getRequest()->isXMLHttpRequest()) {
            $view = 'ICAPDropZoneBundle:DropZone:deleteDocumentModal.html.twig';
        }

        return $this->render(
            $view,
            array(
                'workspace' => $dropZone->getResourceNode()->getWorkspace(),
                'dropZone' => $dropZone,
                'drop' => $drop,
                'document' => $document,
                'pathArray' => $dropZone->getPathArray(),
                'form' => $form->createView(),
            )
        );
    }
}