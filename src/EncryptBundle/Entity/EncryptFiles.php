<?php

namespace EncryptBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EncryptFiles
 *
 * @ORM\Table(name="encrypt_files", indexes={@ORM\Index(name="IDX_33F92805A76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class EncryptFiles
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="encrypt_files_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_extension", type="string", length=10, nullable=true)
     */
    private $fileExtension;

    /**
     * @var string
     *
     * @ORM\Column(name="encrypted_file_name", type="string", length=255, nullable=true)
     */
    private $encryptedFileName;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



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
     * Set file
     *
     * @param string $file
     *
     * @return EncryptFiles
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set fileExtension
     *
     * @param string $fileExtension
     *
     * @return EncryptFiles
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get fileExtension
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set encryptedFileName
     *
     * @param string $encryptedFileName
     *
     * @return EncryptFiles
     */
    public function setEncryptedFileName($encryptedFileName)
    {
        $this->encryptedFileName = $encryptedFileName;

        return $this;
    }

    /**
     * Get encryptedFileName
     *
     * @return string
     */
    public function getEncryptedFileName()
    {
        return $this->encryptedFileName;
    }

    /**
     * Set user
     *
     * @param \EncryptBundle\Entity\User $user
     *
     * @return EncryptFiles
     */
    public function setUser(\EncryptBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \EncryptBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
