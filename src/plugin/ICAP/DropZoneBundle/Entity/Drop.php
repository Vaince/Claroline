<?php
/**
 * Created by : Vincent SAISSET
 * Date: 21/08/13
 * Time: 15:39
 */

namespace ICAP\DropZoneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="icap__dropzonebundle_drop")
 */
class Drop {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="drop_date", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected $dropDate;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $reported = false;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $valid = true;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $finished = false;
    /**
     * @ORM\ManyToOne(
     *      targetEntity="ICAP\DropZoneBundle\Entity\DropZone",
     *      inversedBy="drops"
     * )
     * @ORM\JoinColumn(name="drop_zone_id", referencedColumnName="id", nullable=false)
     */
    protected $dropZone;
    /**
     * @ORM\OneToMany(
     *     targetEntity="ICAP\DropZoneBundle\Entity\Document",
     *     mappedBy="drop",
     *     cascade={"all"},
     *     orphanRemoval=true
     * )
     */
    protected $documents;
    /**
     * @ORM\ManyToOne(
     *      targetEntity="Claroline\CoreBundle\Entity\User"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
    public function getDropDate()
    {
        return $this->dropDate;
    }

    /**
     * @param mixed $dropDate
     */
    public function setDropDate($dropDate)
    {
        $this->dropDate = $dropDate;
    }

    /**
     * @return mixed
     */
    public function getDropZone()
    {
        return $this->dropZone;
    }

    /**
     * @param mixed $dropZone
     */
    public function setDropZone($dropZone)
    {
        $this->dropZone = $dropZone;
    }

    /**
     * @return mixed
     */
    public function getReported()
    {
        return $this->reported;
    }

    /**
     * @param mixed $reported
     */
    public function setReported($reported)
    {
        $this->reported = $reported;
    }

    /**
     * @return mixed
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param mixed $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
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
}