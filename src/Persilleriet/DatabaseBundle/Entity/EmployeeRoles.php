<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\EmployeeRoles
 *
 * @ORM\Table(name="employee_roles")
 * @ORM\Entity
 */
class EmployeeRoles implements RoleInterface {
	public function __construct() {
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
	}
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=40, nullable=false)
     */
    private $title;

    /**
     * @var Departments
     *
     * @ORM\ManyToOne(targetEntity="Departments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;


	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
	private $users;

    /**
	 * @var boolean virtual_role
     *
	 * @ORM\Column(name="virtual_role", type="boolean", nullable=false)
     */
	private $virtual_role;

	public function addUser(User $user) {
		$this->users[] = $user;
	}

	public function removeUser(User $user) {
		$this->users->remove($user);
	}

	public function getRole() {
		if ($this->virtual_role) {
			return $this->getTitle(); 
		}
	}
    public function getDepartment() { 	return $this->department; }
    public function getId() { 			return $this->id; }
	public function getTitle() {		return $this->title; }
    public function setTitle($title) { 	$this->title = $title; }

    public function setDepartment(\Persilleriet\DatabaseBundle\Entity\Departments $department) {
        $this->department = $department;
    }

}
