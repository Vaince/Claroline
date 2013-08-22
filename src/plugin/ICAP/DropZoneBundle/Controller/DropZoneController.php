<?php
/**
 * Created by : Vincent SAISSET
 * Date: 22/08/13
 * Time: 09:30
 */

namespace ICAP\DropZoneBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
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

        return $this->get('security.context')->isGranted($actionName, $collection);
    }

    protected function isAllowToEdit($dropZone)
    {
        return $this->isAllow($dropZone, 'EDIT');
    }

    protected function isAllowToOpen($dropZone)
    {
        return $this->isAllow($dropZone, 'OPEN');
    }

    /**
     * @Route(
     *      "/{resourceId}",
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
        $collection = new ResourceCollection(array($dropZone->getResourceNode()));
        if ($this->get('security.context')->isGranted("OPEN", $collection)) {
            if ($this->get('security.context')->isGranted("EDIT", $collection)) {

                return array();
            }
        } else {
            throw new AccessDeniedException($collection->getErrorsForDisplay());
        }
    }
}