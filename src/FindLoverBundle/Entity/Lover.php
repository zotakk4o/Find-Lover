<?php

namespace FindLoverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lover
 *
 * @ORM\Table(name="lover")
 * @ORM\Entity(repositoryClass="FindLoverBundle\Repository\LoverRepository")
 */
class Lover
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
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
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
	 */
	private $birthDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_online", type="datetime")
     */
    private $lastOnline;


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
}

