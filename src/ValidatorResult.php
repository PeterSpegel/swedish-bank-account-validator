<?php

namespace SwedishBankAccountValidator;

class ValidatorResult
{
    /** @var string */
    private $bankName;
    /** @var ClearingNumber */
    private $clearingNumber;
    /** @var string */
    private $serialNumber;
    /** @var string */
    private $swedishErrorMessage;
    /** @var string */
    private $englishErrorMessage;
    private $isInvalidSerialNumberFormat = false;
    private $isInvalidSwedbankChecksum = false;
    private $isInvalidChecksum = false;
    private $isInvalidClearingNumber = false;

    /**
     * ValidatorResult constructor.
     * @param string $bankName
     * @param ClearingNumber $clearingNumber
     * @param string $serialNumber
     */
    public function __construct($bankName = null, ClearingNumber $clearingNumber = null, $serialNumber = null)
    {
        $this->bankName = $bankName;
        $this->clearingNumber = $clearingNumber;
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getClearingNumber()
    {
        return $this->clearingNumber->__toString();
    }

    /**
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setSwedishErrorMessage($message)
    {
        $this->swedishErrorMessage = $message;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setEnglishErrorMessage($message)
    {
        $this->englishErrorMessage = $message;
        return $this;
    }

    public function hasError()
    {
        return !!$this->englishErrorMessage;
    }

    public function hasInvalidSerialNumberFormat()
    {
        return $this->isInvalidSerialNumberFormat;
    }

    public function hasInvalidSwedbankChecksum()
    {
        return $this->isInvalidSwedbankChecksum;
    }

    public function hasInvalidChecksum()
    {
        return $this->isInvalidChecksum;
    }

    public function hasInvalidClearingNumber()
    {
        return $this->isInvalidClearingNumber;
    }

    public function setInvalidSerialNumberFormat()
    {
        $this->isInvalidSerialNumberFormat = true;
        return $this;
    }

    public function setIsInvalidSwedbankChecksum()
    {
        $this->isInvalidSwedbankChecksum = true;
        $this->isInvalidChecksum = true;
        return $this;
    }

    public function setInvalidChecksum()
    {
        $this->isInvalidChecksum = true;
        return $this;
    }

    public function setInvalidClearingNumber()
    {
        $this->isInvalidClearingNumber = true;
        return $this;
    }

    public function getSwedishErrorMessage()
    {
        return $this->swedishErrorMessage;
    }

    public function getEnglishErrorMessage()
    {
        return $this->englishErrorMessage;
    }
}
