<?php
/**
 * Created by : Vincent SAISSET
 * Date: 21/08/13
 * Time: 15:18
 */

namespace ICAP\DropZoneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Claroline\CoreBundle\Entity\Resource\AbstractResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="icap__dropzonebundle_dropzone")
 */
class DropZone extends AbstractResource {

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $instruction = null;
    /**
     * @ORM\Column(name="allow_workspace_resource", type="boolean", nullable=false)
     */
    protected $allowWorkspaceResource = false;
    /**
     * @ORM\Column(name="allow_upload", type="boolean", nullable=false)
     */
    protected $allowUpload = false;
    /**
     * @ORM\Column(name="allow_url", type="boolean", nullable=false)
     */
    protected $allowUrl = false;
    /**
     * @ORM\Column(name="peer_review", type="boolean", nullable=false)
     */
    protected $peerReview = false;
    /**
     * @ORM\Column(name="expected_total_correction", type="smallint", nullable=false)
     */
    protected $expectedTotalCorrection = 3;
    /**
     * @ORM\Column(name="allow_drop_in_review", type="boolean", nullable=false)
     */
    protected $allowDropInReview = false;
    /**
     * @ORM\Column(name="display_notation_to_learners", type="boolean", nullable=false)
     */
    protected $displayNotationToLearners = true;
    /**
     * @ORM\Column(name="display_notation_message_to_learners", type="boolean", nullable=false)
     */
    protected $displayNotationMessageToLearners = false;
    /**
     * @ORM\Column(name="minimum_score_to_pass", type="smallint", nullable=false)
     */
    protected $minimumScoreToPass = 10;
    /**
     * @ORM\Column(name="manual_planning", type="boolean", nullable=false)
     */
    protected $manualPlanning = true;
    /**
     * @ORM\Column(name="manual_state", type="string", nullable=false)
     */
    protected $manualState = 'notStarted';
    /**
     * @ORM\Column(name="start_allow_drop", type="datetime", nullable=true)
     */
    protected $startAllowDrop = null;
    /**
     * @ORM\Column(name="end_allow_drop", type="datetime", nullable=true)
     */
    protected $endAllowDrop = null;
    /**
     * @ORM\Column(name="end_review", type="datetime", nullable=true)
     */
    protected $endReview = null;
    /**
     * @ORM\Column(name="allow_comment_in_correction", type="boolean", nullable=false)
     */
    protected $allowCommentInCorrection = false;
    /**
     * @ORM\Column(name="total_criteria_column", type="smallint", nullable=false)
     */
    protected $totalCriteriaColumn = 5;
    /**
     * @ORM\OneToMany(
     *     targetEntity="ICAP\DropZoneBundle\Entity\Drop",
     *     mappedBy="dropZone",
     *     cascade={"all"},
     *     orphanRemoval=true
     * )
     */
    protected $drops;
    /**
     * @ORM\OneToMany(
     *     targetEntity="ICAP\DropZoneBundle\Entity\Criterion",
     *     mappedBy="dropZone",
     *     cascade={"all"},
     *     orphanRemoval=true
     * )
     */
    protected $peerReviewCriteria;

    public function __construct()
    {
        $this->drops = new ArrayCollection();
        $this->peerReviewCriteria = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTotalCriteriaColumn()
    {
        return $this->totalCriteriaColumn;
    }

    /**
     * @param mixed $totalCriteriaColumn
     */
    public function setTotalCriteriaColumn($totalCriteriaColumn)
    {
        $this->totalCriteriaColumn = $totalCriteriaColumn;
    }

    /**
     * @return mixed
     */
    public function getAllowCommentInCorrection()
    {
        return $this->allowCommentInCorrection;
    }

    /**
     * @param mixed $allowCommentInCorrection
     */
    public function setAllowCommentInCorrection($allowCommentInCorrection)
    {
        $this->allowCommentInCorrection = $allowCommentInCorrection;
    }

    /**
     * @return mixed
     */
    public function getAllowDropInReview()
    {
        return $this->allowDropInReview;
    }

    /**
     * @param mixed $allowDropInReview
     */
    public function setAllowDropInReview($allowDropInReview)
    {
        $this->allowDropInReview = $allowDropInReview;
    }

    /**
     * @return mixed
     */
    public function getAllowUpload()
    {
        return $this->allowUpload;
    }

    /**
     * @param mixed $allowUpload
     */
    public function setAllowUpload($allowUpload)
    {
        $this->allowUpload = $allowUpload;
    }

    /**
     * @return mixed
     */
    public function getAllowUrl()
    {
        return $this->allowUrl;
    }

    /**
     * @param mixed $allowUrl
     */
    public function setAllowUrl($allowUrl)
    {
        $this->allowUrl = $allowUrl;
    }

    /**
     * @return mixed
     */
    public function getAllowWorkspaceResource()
    {
        return $this->allowWorkspaceResource;
    }

    /**
     * @param mixed $allowWorkspaceResource
     */
    public function setAllowWorkspaceResource($allowWorkspaceResource)
    {
        $this->allowWorkspaceResource = $allowWorkspaceResource;
    }

    /**
     * @return mixed
     */
    public function getEndAllowDrop()
    {
        return $this->endAllowDrop;
    }

    /**
     * @param mixed $endAllowDrop
     */
    public function setEndAllowDrop($endAllowDrop)
    {
        $this->endAllowDrop = $endAllowDrop;
    }

    /**
     * @return mixed
     */
    public function getEndReview()
    {
        return $this->endReview;
    }

    /**
     * @param mixed $endReview
     */
    public function setEndReview($endReview)
    {
        $this->endReview = $endReview;
    }

    /**
     * @return mixed
     */
    public function getExpectedTotalCorrection()
    {
        return $this->expectedTotalCorrection;
    }

    /**
     * @param mixed $expectedTotalCorrection
     */
    public function setExpectedTotalCorrection($expectedTotalCorrection)
    {
        $this->expectedTotalCorrection = $expectedTotalCorrection;
    }

    /**
     * @return mixed
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * @param mixed $instruction
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;
    }

    /**
     * @return mixed
     */
    public function getManualPlanning()
    {
        return $this->manualPlanning;
    }

    /**
     * @param mixed $manualPlanning
     */
    public function setManualPlanning($manualPlanning)
    {
        $this->manualPlanning = $manualPlanning;
    }

    /**
     * @return mixed
     */
    public function getManualState()
    {
        return $this->manualState;
    }

    /**
     * @param mixed $manualState
     */
    public function setManualState($manualState)
    {
        $this->manualState = $manualState;
    }

    /**
     * @return mixed
     */
    public function getPeerReview()
    {
        return $this->peerReview;
    }

    /**
     * @param mixed $peerReview
     */
    public function setPeerReview($peerReview)
    {
        $this->peerReview = $peerReview;
    }

    /**
     * @return mixed
     */
    public function getStartAllowDrop()
    {
        return $this->startAllowDrop;
    }

    /**
     * @param mixed $startAllowDrop
     */
    public function setStartAllowDrop($startAllowDrop)
    {
        $this->startAllowDrop = $startAllowDrop;
    }

    /**
     * @return mixed
     */
    public function getDrops()
    {
        return $this->drops;
    }

    /**
     * @param mixed $drops
     */
    public function setDrops($drops)
    {
        $this->drops = $drops;
    }

    /**
     * @return mixed
     */
    public function getPeerReviewCriteria()
    {
        return $this->peerReviewCriteria;
    }

    /**
     * @param mixed $peerReviewCriteria
     */
    public function setPeerReviewCriteria($peerReviewCriteria)
    {
        $this->peerReviewCriteria = $peerReviewCriteria;
    }

    /**
     * @return mixed
     */
    public function getDisplayNotationMessageToLearners()
    {
        return $this->displayNotationMessageToLearners;
    }

    /**
     * @param mixed $displayNotationMessageToLearners
     */
    public function setDisplayNotationMessageToLearners($displayNotationMessageToLearners)
    {
        $this->displayNotationMessageToLearners = $displayNotationMessageToLearners;
    }

    /**
     * @return mixed
     */
    public function getDisplayNotationToLearners()
    {
        return $this->displayNotationToLearners;
    }

    /**
     * @param mixed $displayNotationToLearners
     */
    public function setDisplayNotationToLearners($displayNotationToLearners)
    {
        $this->displayNotationToLearners = $displayNotationToLearners;
    }

    /**
     * @return mixed
     */
    public function getMinimumScoreToPass()
    {
        return $this->minimumScoreToPass;
    }

    /**
     * @param mixed $minimumScoreToPass
     */
    public function setMinimumScoreToPass($minimumScoreToPass)
    {
        $this->minimumScoreToPass = $minimumScoreToPass;
    }
}