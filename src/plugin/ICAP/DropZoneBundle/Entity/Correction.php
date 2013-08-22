<?php
/**
 * Created by : Vincent SAISSET
 * Date: 21/08/13
 * Time: 16:40
 */

namespace ICAP\DropZoneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="icap__dropzonebundle_correction")
 */
class Correction {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="total_grade", type="smallint", nullable=true)
     */
    protected $totalGrade = null;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment = null;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $valid = true;
    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected $startDate;
    /**
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $endDate = null;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $finished = false;
    /**
     * @ORM\OneToMany(
     *      targetEntity="ICAP\DropZoneBundle\Entity\Grade",
     *      mappedBy="correction",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    protected $grades;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="Claroline\CoreBundle\Entity\User"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    function __construct()
    {
        $this->grades = new ArrayCollection();
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return mixed
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param mixed $grades
     */
    public function setGrades($grades)
    {
        $this->grades = $grades;
    }

    /**
     * @return mixed
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $totalGrade
     */
    public function setTotalGrade($totalGrade)
    {
        $this->totalGrade = $totalGrade;
    }

    /**
     * @return mixed
     */
    public function getTotalGrade()
    {
        return $this->totalGrade;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return mixed
     */
    public function getValid()
    {
        return $this->valid;
    }
}