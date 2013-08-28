<?php
/**
 * Created by : Vincent SAISSET
 * Date: 21/08/13
 * Time: 16:06
 */

namespace ICAP\DropZoneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="icap__dropzonebundle_criterion")
 */
class Criterion {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="instruction", type="text", nullable=false)
     */
    protected $instruction;
    /**
     * @ORM\ManyToOne(
     *      targetEntity="ICAP\DropZoneBundle\Entity\DropZone",
     *      inversedBy="peerReviewCriteria"
     * )
     * @ORM\JoinColumn(name="drop_zone_id", referencedColumnName="id", nullable=false)
     */
    protected $dropZone;

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
    public function getId()
    {
        return $this->id;
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
}