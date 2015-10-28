<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_cover")
 */
class Cover
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\OneToOne(targetEntity="WechatUser", inversedBy="cover")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
   /**
     * @ORM\Column(name="title",type="string", length=200)
     */
    protected $title;
   /**
     * @ORM\Column(name="img_url",type="string", length=120)
     */
    protected $imgUrl;
    /**
     * @ORM\Column(name="gender",type="integer")
     */
    protected $gender;
    /**
     * @ORM\Column(name="style",type="integer")
     */
    protected $style;
    /**
     * @ORM\Column(name="hair_style",type="integer")
     */
    protected $hairStyle;
    /**
     * @ORM\Column(name="cloth1",type="integer")
     */
    protected $cloth1;
    /**
     * @ORM\Column(name="cloth2",type="integer")
     */
    protected $cloth2;
   /**
     * @ORM\Column(name="username",type="string", length=120)
     */
    protected $username;
    /**
     * @ORM\Column(name="mobile",type="string", length=200)
     */
    protected $mobile;
    /**
     * @ORM\Column(name="favour_num",type="integer")
     */
    protected $favourNum;
    /**
     * @ORM\Column(name="create_time",  type="datetime")
     */
    private $createTime;
    /**
     * @ORM\Column(name="create_ip", type="string", length=60)
     */
    private $createIp;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set imgUrl
     *
     * @param string $imgUrl
     * @return Cover
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * Get imgUrl
     *
     * @return string 
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * Set style
     *
     * @param integer $style
     * @return Cover
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return integer 
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set hairStyle
     *
     * @param integer $hairStyle
     * @return Cover
     */
    public function setHairStyle($hairStyle)
    {
        $this->hairStyle = $hairStyle;

        return $this;
    }

    /**
     * Get hairStyle
     *
     * @return integer 
     */
    public function getHairStyle()
    {
        return $this->hairStyle;
    }

    /**
     * Set cloth1
     *
     * @param integer $cloth1
     * @return Cover
     */
    public function setCloth1($cloth1)
    {
        $this->cloth1 = $cloth1;

        return $this;
    }

    /**
     * Get cloth1
     *
     * @return integer 
     */
    public function getCloth1()
    {
        return $this->cloth1;
    }

    /**
     * Set cloth2
     *
     * @param integer $cloth2
     * @return Cover
     */
    public function setCloth2($cloth2)
    {
        $this->cloth2 = $cloth2;

        return $this;
    }

    /**
     * Get cloth2
     *
     * @return integer 
     */
    public function getCloth2()
    {
        return $this->cloth2;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Cover
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Cover
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Cover
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set createIp
     *
     * @param string $createIp
     * @return Cover
     */
    public function setCreateIp($createIp)
    {
        $this->createIp = $createIp;

        return $this;
    }

    /**
     * Get createIp
     *
     * @return string 
     */
    public function getCreateIp()
    {
        return $this->createIp;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\WechatUser $user
     * @return Cover
     */
    public function setUser(\AppBundle\Entity\WechatUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\WechatUser 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set favourNum
     *
     * @param integer $favourNum
     * @return Cover
     */
    public function setFavourNum($favourNum)
    {
        $this->favourNum = $favourNum;

        return $this;
    }

    /**
     * Get favourNum
     *
     * @return integer 
     */
    public function getFavourNum()
    {
        return $this->favourNum;
    }
    public function increaseNum()
    {
        ++$this->favourNum;
        return $this;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     * @return Cover
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Cover
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
