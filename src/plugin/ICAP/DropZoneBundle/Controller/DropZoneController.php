<?php
/**
 * Created by : Vincent SAISSET
 * Date: 22/08/13
 * Time: 09:30
 */

namespace ICAP\DropZoneBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use ICAP\DropZoneBundle\Entity\DropZone;
use ICAP\DropZoneBundle\Form\DropZoneCommonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     *      "/{resourceId}/open",
     *      name="icap_dropzone_open",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function openAction($dropZone, $user)
    {
        //Participant view for a dropZone
        $this->isAllowToOpen($dropZone);

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone
        );
    }
    /**
     * @Route(
     *      "/{resourceId}/drops",
     *      name="icap_dropzone_drops",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
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
            'dropZone' => $dropZone
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit",
     *      name="icap_dropzone_edit",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
     * )
     * @Route(
     *      "/{resourceId}/edit/common",
     *      name="icap_dropzone_edit_common",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function editCommonAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $form = $this->container->get('form.factory')->create(new DropZoneCommonType(), $dropZone);

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
            'form' => $form->createView()
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/update/common",
     *      name="icap_dropzone_update_common",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
     * )
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template("ICAPDropZoneBundle:DropZone:editCommon.html.twig")
     */
    public function updateCommonAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        $form = $this->createForm(new DropZoneCommonType(), $dropZone);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $dropZone = $form->getData();

            if (!$dropZone->getPeerReview() and $dropZone->getManualState() == 'peerReview') {
                $dropZone->setManualState('notStarted');
            }

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

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone,
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
     * @ParamConverter("dropZone", class="ICAPDropZoneBundle:DropZone", options={"id" = "resourceId"})
     * @ParamConverter("user", options={"authenticatedUser" = true})
     * @Template()
     */
    public function editCriteriaAction($dropZone, $user)
    {
        $this->isAllowToOpen($dropZone);
        $this->isAllowToEdit($dropZone);

        return array(
            'workspace' => $dropZone->getResourceNode()->getWorkspace(),
            'dropZone' => $dropZone
        );
    }

    /**
     * @Route(
     *      "/{resourceId}/edit/participant",
     *      name="icap_dropzone_edit_participant",
     *      requirements={"resourceId" = "\d+"},
     *      defaults={"page" = 1}
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
            'dropZone' => $dropZone
        );
    }


}