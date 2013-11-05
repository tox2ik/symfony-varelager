<?php

namespace Persilleriet\DatabaseBundle\Entity;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Persilleriet\DatabaseBundle\Entity\Addresses;
use Persilleriet\DatabaseBundle\Entity\Account;
use Persilleriet\DatabaseBundle\Entity\InputChecks;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="employes")
 * @ORM\Entity(repositoryClass="Persilleriet\DatabaseBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface {

	private $inputChecks;
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     * @ORM\Column(name="username", type="string", length=25, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string $nameFirst
     *
     * @ORM\Column(name="name_first", type="string", length=35, nullable=false)
     */
    private $nameFirst;

    /**
     * @var string $nameLast
     *
     * @ORM\Column(name="name_last", type="string", length=35, nullable=false)
     */
    private $nameLast;

	/**
     * @ORM\OneToOne(targetEntity="Addresses", inversedBy="user")
     * 	@ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

	///**
	// * @ORM\ManyToMany(targetEntity="EmployeeRoles", mappedBy="users")
	// * @ORM\JoinTable(name="employee_data",
	// *      joinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id")},
	// *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	// *      )
	// */
	//private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="EmployeeRoles", inversedBy="users")
     * @ORM\JoinTable(name="employee_data",
     *      joinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
	private $roles;


    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=40, nullable=false)
     */
    private $salt;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=40, nullable=false)
     */
    private $password;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

	public function __construct() {
		$this->inputChecks = new InputChecks();
		$this->active = true;
		$this->salt = $this->generateSalt();
		$this->roles = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function isAccountNonExpired() { return true; }
	public function isCredentialsNonExpired() { return true; }
	public function isAccountNonLocked() { 
		return $this->active; 
	}
	public function isEnabled() { 
		return	$this->isAccountNonExpired() && 
				$this->isAccountNonLocked() &&
				$this->isCredentialsNonExpired();
	}

	public function eraseCredentials() { }
	public function getActive()		{ return $this->active; }
	public function getAddress()	{ return $this->address; }
	public function getId()			{ return $this->id; }
	public function getNameFirst()	{ return $this->nameFirst; }
	public function getNameLast()	{ return $this->nameLast; }
	public function getPassword()	{ return $this->password; }
	public function getSalt()		{ return $this->salt; }
	public function getUsername()	{ return $this->username; }

	public function setActive($active)		{ $this->active = $active; }
	public function setAddress(Addresses $address) { 	
		$address->setUser($this);
		$this->address = $address; 
	}
	public function setNameFirst($nameFirst){ 
		$this->inputChecks = new InputChecks();
		if ($this->inputChecks->isDefinedNotEmpy($nameFirst)) {
			$this->nameFirst = $nameFirst; 
			return;
		}
		throw new Exception('Empty name');
   	}
	public function setNameLast($nameLast)	{ 
		$this->inputChecks = new InputChecks();
		if ($this->inputChecks->isDefinedNotEmpy($nameLast)) {
			$this->nameLast = $nameLast; 
			return;
		}
		throw new Exception('Empty last name');
	}
	public function setPassword($password)	{ $this->password = $password; }
	public function setSalt($salt)			{ $this->salt = $salt; }
	public function setUsername($username)	{ 
		$this->inputChecks = new InputChecks();
		if ($this->inputChecks->isDefinedNotEmpy($username)) {
			$this->username = $username; 
			return;
		}
		throw new Exception('Empty username');
	}

	public function equals(UserInterface $user) {
		return $user->getUsername() === $this->username;
	}

	public function getRoles() {
		$ret = array();
		foreach ($this->roles as $role) {
			$ret[]= $role;
		}
		//return array( 'ROLE_ADMIN', 'ROLE_USER' );
		return $ret;
	}
	public function addRole(EmployeeRoles $role) {
		$role->addUser($this);
		$this->roles[] = $role;
	}
	public function removeAllRoles() {
		error_log("$this->username -> removeAllRoles() roles-count: ". count($this->roles) );
		
		//foreach ($this->roles as $role) {
		$len = -1 + $this->roles->count();
		for ($i=$len; $i > -1 ; $i--) {
			error_log("-$i ". $this->roles->get($i)->getRole() ) ;
			$this->roles->remove($i);
		}
	}
	public function removeRole(EmployeeRoles $role) {
		$role->removeUser($this);
		$this->roles->remove($role);
	}

	public function generateSalt($max = 40) {
		$characterList = "abcdefghijklmnopqrstuvwxyz";
		$characterList.= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$characterList.= "0123456789";
		$lastIdx = strlen($characterList)-1;
		$salt = "";
		for ($i=0; $i < $max; $i++) {
			$salt .= $characterList{mt_rand(0,$lastIdx)};
		}
		return $salt;
	}

	public function __toString(){
		$msg = " id: $this->id, l:$this->username, name:$this->nameFirst $this->nameLast, addrid: ";
		$msg .= $this->address->getId() . " ";
		$msg .= "email: " .$this->address->getEmail();
			/*
		$msg = "";
		$msg .= "id: " . $this->id;
		$msg .= " user: " . $this->username;
		$msg .= " mail: " . $this->address->getEmail();
			 */
		return $msg;
	}

}
?>
