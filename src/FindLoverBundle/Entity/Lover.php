<?php

namespace FindLoverBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Lover
 *
 * @ORM\Table(name="lover")
 * @ORM\Entity(repositoryClass="FindLoverBundle\Repository\LoverRepository")
 */
class Lover implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255)
     *
     * @Assert\NotBlank(message="Nickname is required")
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="First name is required")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Last name is required")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Email is required")
     */
    private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="profile_picture", type="string", length=255)
	 *
	 * @Assert\Image()
	 */
    private $profilePicture;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     *
     * @Assert\NotBlank(message="Password is required")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="date_registered", type="datetime", length=255)
     */
    private $dateRegistered;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="gender", type="string", length=255)
	 *
	 * @Assert\NotBlank(message="Gender is required")
	 */
    private $gender;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
	 */
    private $phoneNumber;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="birth_date", type="datetime", length=255)
	 *
	 * @Assert\NotBlank(message="Birth date is required")
	 */
	private $birthDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_online", type="datetime", nullable=true)
     */
    private $lastOnline;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToMany(targetEntity="FindLoverBundle\Entity\Role")
	 * @ORM\JoinTable(name="lover_roles",
	 *     joinColumns={@ORM\JoinColumn(name="lover_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 *     )
	 */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="friends", type="text", nullable=true)
     */
    private $friends;

    /**
     * @var string
     *
     * @ORM\Column(name="recent_searches", type="text", nullable=true)
     */
    private $recentSearches;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     *
     * @return Lover
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Lover
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Lover
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Lover
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Lover
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set dateRegistered
     *
     * @param string $dateRegistered
     *
     * @return Lover
     */
    public function setDateRegistered($dateRegistered)
    {
        $this->dateRegistered = $dateRegistered;

        return $this;
    }

    /**
     * Get dateRegistered
     *
     * @return string
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

	/**
	 * Get gender
	 *
	 * @return string
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Set gender
	 *
	 * @param string $gender
	 *
	 * @return Lover
	 */
	public function setGender($gender) {
		$this->gender = $gender;

		return $this;
	}

	/**
	 * Get phoneNumber
	 *
	 * @return string
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	/**
	 * Set phoneNumber
	 *
	 * @param string $phoneNumber
	 *
	 * @return Lover
	 */
	public function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;

		return $this;
	}

	/**
	 * Get birthDate
	 *
	 * @return string
	 */
	public function getBirthDate() {
		return $this->birthDate;
	}

	/**
	 * Set birthDate
	 *
	 * @param string $birthDate
	 *
	 * @return Lover
	 */
	public function setBirthDate($birthDate) {
		$this->birthDate = $birthDate;

		return $this;
	}

    /**
     * Set lastOnline
     *
     * @param \DateTime $lastOnline
     *
     * @return Lover
     */
    public function setLastOnline($lastOnline)
    {
        $this->lastOnline = $lastOnline;

        return $this;
    }

    /**
     * Get lastOnline
     *
     * @return \DateTime
     */
    public function getLastOnline()
    {
        return $this->lastOnline;
    }

	/**
	 * @return string
	 */
	public function getProfilePicture() {
		return $this->profilePicture;
	}

	/**
	 * @param string $profilePicture
	 */
	public function setProfilePicture($profilePicture ) {
		$this->profilePicture = $profilePicture;
	}



	/**
	 * Get roles
	 *
	 * @return Collection|string[]
	 */
	public function getRoles() {
		$result = [];

		$roles = $this->roles;
		foreach ($roles as $role){
			if(gettype($role) == 'string')$result[] = $role;
			else $result[] = $role->getName();
		}

		return $result;
	}

    /**
     * @return string
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @return array
     */
    public function getFriendsIds()
    {
        return explode(', ', $this->getFriends());
    }

    /**
     * @param string $friends
     *
     * @return Lover
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this;
    }

    /**
     * @param $id int|string
     *
     * @return Lover
     */
    public function addFriend($id) {
        empty($this->getFriends()) ? $this->setFriends($id) : $this->setFriends($this->getFriends() . ", $id" );

        return $this;
    }

    /**
     * @return array
     */
    public function getRecentSearchesIds() {
        return explode(', ', $this->getRecentSearches());
    }

    /**
     * @return string
     */
    public function getRecentSearches()
    {
        return $this->recentSearches;
    }

    /**
     * @param string $recentSearches
     *
     * @return Lover
     */
    public function setRecentSearches($recentSearches)
    {
        $this->recentSearches = $recentSearches;

        return $this;
    }

    /**
     * @param $id string|int
     *
     * @return Lover
     */
    public function addRecentSearch($id)
    {
        $searches = $this->getRecentSearchesIds();
        if(in_array($id, $searches)) {
            unset($searches[array_search($id, $searches)]);
        }
        $searchesString = implode(', ', $searches);

        empty($searchesString) ? $this->setRecentSearches($id) : $this->setRecentSearches("$id, " . $searchesString);

        return $this;
    }

	/**
	 * @return bool
	 */
	public function isOnline() {
		return $this->getLastOnline() === null;
	}

	/**
	 * Add role
	 *
	 * @param $role Role
	 */
	public function addRole($role) {
		$this->roles[] = $role;
	}

	public function getSalt() {
		// TODO: Implement getSalt() method.
	}

	public function getUsername() {
		return $this->getEmail();
	}

	public function eraseCredentials() {
		// TODO: Implement eraseCredentials() method.
	}


}

